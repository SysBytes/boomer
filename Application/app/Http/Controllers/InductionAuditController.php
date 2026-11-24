<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInductionEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class InductionAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasAnyRole(['admin', 'developer'])) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    /**
     * Panel gerencial de auditoría de inducción (estrictamente solo lectura).
     */
    public function index(Request $request)
    {
        $isDeveloper = auth()->user()->hasRole('developer');

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
        ];

        $baseQuery = User::query()
            ->where('users.active', 1)
            ->whereHas('inductionEvidences')
            ->whereHas('contentProgress', function ($q) {
                $q->where('status', 'completed')->whereNotNull('completed_at');
            });

        if (!$isDeveloper) {
            $baseQuery->whereDoesntHave('roles', function ($q) {
                $q->where('roles.slug', 'developer');
            });
        }

        // Indicadores gerenciales globales (respetando aislamiento del rol developer)
        $stats = [
            'total_employees' => (clone $baseQuery)->count(),
            'completed' => (clone $baseQuery)->where('induction_status', 'completed')->count(),
            'pending' => (clone $baseQuery)->where('induction_status', 'pending')->count(),
            'total_evidences' => UserInductionEvidence::query()
                ->whereHas('user', function ($q) use ($isDeveloper) {
                    $q->where('active', 1)
                        ->whereHas('contentProgress', function ($p) {
                            $p->where('status', 'completed')->whereNotNull('completed_at');
                        });
                    if (!$isDeveloper) {
                        $q->whereDoesntHave('roles', fn ($r) => $r->where('roles.slug', 'developer'));
                    }
                })
                ->count(),
        ];

        $query = (clone $baseQuery)
            ->with([
                'roles',
                'latestInductionEvidence.content',
            ])
            ->withCount([
                'inductionEvidences',
                'contentProgress as completed_contents_count' => function ($q) {
                    $q->where('status', 'completed');
                },
            ])
            ->withMax('inductionEvidences as last_accepted_at', 'accepted_at');

        if ($filters['search'] !== '') {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('users.name', 'like', "%{$term}%")
                    ->orWhere('users.email', 'like', "%{$term}%");
            });
        }

        if (in_array($filters['status'], ['completed', 'pending'], true)) {
            $query->where('users.induction_status', $filters['status']);
        }

        $dateFrom = $this->parseDate($filters['date_from']);
        $dateTo = $this->parseDate($filters['date_to']);

        if ($dateFrom !== null || $dateTo !== null) {
            $query->whereHas('inductionEvidences', function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom !== null) {
                    $q->where('accepted_at', '>=', $dateFrom->copy()->startOfDay());
                }
                if ($dateTo !== null) {
                    $q->where('accepted_at', '<=', $dateTo->copy()->endOfDay());
                }
            });
        }

        $employees = $query
            ->orderByRaw("CASE WHEN users.induction_status = 'completed' THEN 0 ELSE 1 END")
            ->orderByDesc('last_accepted_at')
            ->orderBy('users.name')
            ->paginate(15)
            ->withQueryString();

        return view('backoffice.induction.audit', compact('employees', 'stats', 'filters', 'isDeveloper'));
    }

    /**
     * Detalle de auditoría de un empleado: evidencias recolectadas y desglose de contenidos vistos.
     */
    public function show(Request $request, User $user)
    {
        $isDeveloper = auth()->user()->hasRole('developer');
        $user->loadMissing('roles');

        if (!$isDeveloper && $user->hasRole('developer')) {
            abort(403, 'No autorizado.');
        }

        $user->load([
            'inductionEvidences' => function ($q) {
                $q->with('content')
                    ->orderByDesc('accepted_at')
                    ->orderByDesc('id');
            },
            'contentProgress' => function ($q) {
                $q->with('content.subcategory.category')
                    ->orderByDesc('completed_at')
                    ->orderByDesc('updated_at');
            },
        ]);

        $visibleRoles = $isDeveloper
            ? $user->roles
            : $user->roles->where('slug', '!=', 'developer');

        $evidences = $user->inductionEvidences->map(function (UserInductionEvidence $evidence) {
            return [
                'id' => $evidence->id,
                'scope_label' => $evidence->content
                    ? 'Confirmación de contenido: ' . $evidence->content->title
                    : 'Certificación general de inducción obligatoria',
                'accepted_at_label' => $evidence->accepted_at
                    ? $evidence->accepted_at->format('d/m/Y H:i:s')
                    : 'Sin fecha',
                'ip_address' => $evidence->ip_address ?: 'No registrada',
                'photo_url' => $evidence->evidence_photo_path
                    ? route('induction.audit.evidence_file', [$evidence->id, 'photo'])
                    : null,
                'signature_url' => $evidence->signature_path
                    ? route('induction.audit.evidence_file', [$evidence->id, 'signature'])
                    : null,
            ];
        })->values();

        $progress = $user->contentProgress->map(function ($item) {
            $seconds = max(0, (int) $item->view_time_seconds);
            $content = $item->content;

            return [
                'title' => $content ? $content->title : 'Contenido eliminado',
                'category' => $content?->subcategory?->category?->name ?? 'N/A',
                'subcategory' => $content?->subcategory?->name ?? 'N/A',
                'type' => $content ? ($content->is_external ? 'Enlace' : strtoupper($content->type)) : 'N/A',
                'is_mandatory' => (bool) ($content?->is_mandatory ?? false),
                'view_time_seconds' => $seconds,
                'view_time_label' => $this->formatSeconds($seconds),
                'status' => $item->status,
                'completed_at_label' => $item->completed_at
                    ? $item->completed_at->format('d/m/Y H:i:s')
                    : 'Pendiente',
            ];
        })->values();

        return response()->json([
            'employee' => [
                'id' => $user->id,
                'name' => ucwords(mb_strtolower($user->name)),
                'email' => $user->email,
                'induction_status' => $user->induction_status,
                'roles' => $visibleRoles->pluck('name')->values()->all(),
            ],
            'evidences' => $evidences,
            'progress' => $progress,
        ], 200, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private, max-age=0',
        ]);
    }

    /**
     * Entrega segura de la fotografía selfie o firma digital desde storage privado (solo admin/developer).
     */
    public function evidenceFile(UserInductionEvidence $evidence, string $kind)
    {
        $evidence->loadMissing('user.roles');

        if (!auth()->user()->hasRole('developer') && $evidence->user && $evidence->user->hasRole('developer')) {
            abort(403, 'No autorizado.');
        }

        $path = $kind === 'photo' ? $evidence->evidence_photo_path : $evidence->signature_path;
        $disk = null;

        if ($path) {
            foreach (['local', 'public'] as $candidate) {
                if (Storage::disk($candidate)->exists($path)) {
                    $disk = $candidate;
                    break;
                }
            }
        }

        if (!$disk) {
            abort(404, 'Archivo de evidencia no encontrado.');
        }

        return response()->file(Storage::disk($disk)->path($path), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ]);
    }

    private function parseDate(string $value): ?Carbon
    {
        if ($value === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function formatSeconds(int $seconds): string
    {
        $mins = intdiv($seconds, 60);
        $secs = $seconds % 60;

        if ($mins === 0) {
            return $secs . 's';
        }

        return sprintf('%02d:%02d (%ds)', $mins, $secs, $seconds);
    }
}
