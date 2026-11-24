<?php

namespace App\Http\Controllers;

use App\Models\CorporateContent;
use App\Models\DocumentCategory;
use App\Models\User;
use App\Models\UserContentProgress;
use App\Services\InductionEvidenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UserContentLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Biblioteca de contenidos corporativos del usuario autenticado,
     * agrupada por Categoría -> Subcategoría y filtrada por rol/usuario.
     */
    public function index()
    {
        $user = auth()->user();

        $categories = DocumentCategory::query()
            ->whereHas('subcategories.contents', function ($q) use ($user) {
                $q->visibleTo($user);
            })
            ->with(['subcategories' => function ($sub) use ($user) {
                $sub->whereHas('contents', function ($q) use ($user) {
                    $q->visibleTo($user);
                })
                ->with(['contents' => function ($q) use ($user) {
                    $q->visibleTo($user)->orderBy('title');
                }])
                ->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $viewedMap = UserContentProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->pluck('completed_at', 'content_id');

        return view('dashboard.corporate_contents.index', compact('categories', 'viewedMap'));
    }

    /**
     * Pantalla de primera visualización y validación (selfie + firma) para cualquier contenido.
     */
    public function showVerification(CorporateContent $content)
    {
        $user = auth()->user();

        if (!$this->isRecipient($user, $content)) {
            if ($user->hasAnyRole(['admin', 'developer'])) {
                return redirect()->route('dashboard.corporate-contents.view', $content->id);
            }
            abort(403, 'No tienes permiso para acceder a este contenido.');
        }

        if (!$content->is_external && $content->resolveStorageDisk() === null) {
            abort(404, 'El archivo solicitado no existe o fue removido.');
        }

        $progress = UserContentProgress::firstOrCreate(
            ['user_id' => $user->id, 'content_id' => $content->id],
            ['view_time_seconds' => 0, 'status' => 'pending']
        );

        if ($progress->status === 'completed' && $progress->completed_at !== null) {
            return redirect()
                ->route('dashboard.corporate-contents.index')
                ->with('success', 'Este contenido ya fue validado anteriormente. Puedes consultarlo directamente desde la biblioteca.');
        }

        $content->load(['subcategory.category']);

        return response()
            ->view('dashboard.corporate_contents.verify', compact('user', 'content', 'progress'))
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
                'Pragma' => 'no-cache',
                'X-Frame-Options' => 'DENY',
                'Content-Security-Policy' => "frame-ancestors 'none'",
            ]);
    }

    /**
     * Acumula tiempo de permanencia en un contenido durante su primera visualización.
     */
    public function heartbeat(Request $request, CorporateContent $content)
    {
        $fetchSite = $request->header('Sec-Fetch-Site');
        if ($fetchSite !== null && !in_array($fetchSite, ['same-origin', 'none'], true)) {
            abort(403, 'Solicitud de origen no permitido.');
        }

        $user = auth()->user();
        if (!$this->isRecipient($user, $content)) {
            abort(403, 'No tienes permiso para acceder a este contenido.');
        }

        $data = $request->validate([
            'increment_seconds' => 'nullable|integer|min:1|max:60',
            'view_time_seconds' => 'nullable|integer|min:0|max:86400',
        ]);

        $progress = UserContentProgress::firstOrCreate(
            ['user_id' => $user->id, 'content_id' => $content->id],
            ['view_time_seconds' => 0, 'status' => 'pending']
        );

        if (isset($data['view_time_seconds'])) {
            $newSeconds = max((int) $progress->view_time_seconds, (int) $data['view_time_seconds']);
        } elseif (isset($data['increment_seconds'])) {
            $newSeconds = (int) $progress->view_time_seconds + (int) $data['increment_seconds'];
        } else {
            $newSeconds = (int) $progress->view_time_seconds + 5;
        }

        $progress->view_time_seconds = $newSeconds;
        $progress->save();

        $required = max(0, (int) $content->required_time_seconds);

        return response()->json([
            'view_time_seconds' => (int) $progress->view_time_seconds,
            'required_time_seconds' => $required,
            'remaining_seconds' => max(0, $required - (int) $progress->view_time_seconds),
            'can_complete' => (int) $progress->view_time_seconds >= $required,
            'status' => $progress->status,
        ]);
    }

    /**
     * Valida tiempo requerido, guarda selfie/firma en storage privado y habilita el visor modal normal.
     */
    public function confirmVerification(Request $request, CorporateContent $content, InductionEvidenceService $evidenceService)
    {
        $user = auth()->user();

        if (!$this->isRecipient($user, $content)) {
            abort(403, 'No tienes permiso para acceder a este contenido.');
        }

        $progress = UserContentProgress::firstOrCreate(
            ['user_id' => $user->id, 'content_id' => $content->id],
            ['view_time_seconds' => 0, 'status' => 'pending']
        );

        $required = max(0, (int) $content->required_time_seconds);

        if ($request->filled('view_time_seconds') && is_numeric($request->input('view_time_seconds'))) {
            $reported = max(0, (int) $request->input('view_time_seconds'));
            if ((int) $progress->view_time_seconds > 0 || $required <= 5) {
                $capped = min($reported, (int) $progress->view_time_seconds + 15);
                $progress->view_time_seconds = max((int) $progress->view_time_seconds, $capped);
                $progress->save();
            }
        }

        if ((int) $progress->view_time_seconds < $required) {
            $remaining = $required - (int) $progress->view_time_seconds;
            $msg = "Aún debes permanecer {$remaining} segundos más en este contenido para validarlo.";

            if ($request->expectsJson()) {
                return response()->json([
                    'verified' => false,
                    'message' => $msg,
                    'view_time_seconds' => (int) $progress->view_time_seconds,
                    'required_time_seconds' => $required,
                ], 422);
            }

            return redirect()->route('dashboard.corporate-contents.verify', $content->id)->with('error', $msg);
        }

        $evidenceService->storeEvidence($request, $user, $content);

        $progress->status = 'completed';
        if ($progress->completed_at === null) {
            $progress->completed_at = now();
        }
        $progress->save();

        if ($request->expectsJson()) {
            return response()->json([
                'verified' => true,
                'completed_at_label' => $progress->completed_at->format('d/m/Y H:i'),
                'redirect_url' => route('dashboard.corporate-contents.index'),
            ]);
        }

        return redirect()
            ->route('dashboard.corporate-contents.index')
            ->with('success', '¡Contenido validado correctamente! A partir de ahora puedes abrirlo directamente en el visor.');
    }

    /**
     * Consulta/reafirma visualización en aperturas posteriores (conservando la primera fecha/hora).
     * Si es la primera vez sin evidencia previa, exige completar el flujo con selfie y firma.
     */
    public function markViewed(Request $request, CorporateContent $content)
    {
        $fetchSite = $request->header('Sec-Fetch-Site');
        if ($fetchSite !== null && !in_array($fetchSite, ['same-origin', 'none'], true)) {
            abort(403, 'Solicitud de origen no permitido.');
        }

        $user = auth()->user();

        if (!$this->isRecipient($user, $content)) {
            if ($this->canViewContent($user, $content)) {
                return response()->json(['viewed' => false, 'preview' => true]);
            }
            abort(403, 'No tienes permiso para acceder a este contenido.');
        }

        $progress = UserContentProgress::where('user_id', $user->id)
            ->where('content_id', $content->id)
            ->first();

        if ($progress && $progress->status === 'completed' && $progress->completed_at !== null) {
            return response()->json([
                'viewed' => true,
                'first_time' => false,
                'completed_at' => $progress->completed_at->toIso8601String(),
                'completed_at_label' => $progress->completed_at->format('d/m/Y H:i'),
            ], 200, [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, private, max-age=0',
            ]);
        }

        return response()->json([
            'viewed' => false,
            'requires_verification' => true,
            'verify_url' => route('dashboard.corporate-contents.verify', $content->id),
            'message' => 'Debes completar la validación con selfie y firma digital la primera vez que visualizas este contenido.',
        ], 422, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private, max-age=0',
        ]);
    }

    /**
     * Muestra el visor HTML protegido (nunca entrega el PDF crudo directamente).
     */
    public function viewFile(CorporateContent $content)
    {
        $user = auth()->user();

        if (!$this->canViewContent($user, $content)) {
            abort(403, 'No tienes permiso para acceder a este contenido.');
        }

        if (!$user->hasAnyRole(['admin', 'developer'])) {
            $isVerified = UserContentProgress::where('user_id', $user->id)
                ->where('content_id', $content->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->exists();

            if (!$isVerified) {
                return redirect()->route('dashboard.corporate-contents.verify', $content->id);
            }
        }

        if ($content->is_external) {
            return redirect()->away($content->url);
        }

        if ($content->resolveStorageDisk() === null) {
            abort(404, 'El archivo solicitado no existe o fue removido.');
        }

        $viewedAt = UserContentProgress::where('user_id', $user->id)
            ->where('content_id', $content->id)
            ->where('status', 'completed')
            ->value('completed_at');

        $viewedLabel = $viewedAt ? \Illuminate\Support\Carbon::parse($viewedAt)->format('d/m/Y H:i') : null;

        return response()
            ->view('dashboard.corporate_contents.viewer', compact('content', 'viewedLabel'))
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
                'Pragma' => 'no-cache',
                'X-Frame-Options' => 'DENY',
                'Content-Security-Policy' => "frame-ancestors 'none'",
            ]);
    }

    /**
     * Emite un token efímero vinculado al usuario y su sesión para transmitir el archivo.
     */
    public function issueToken(Request $request, CorporateContent $content)
    {
        $fetchSite = $request->header('Sec-Fetch-Site');
        if ($fetchSite !== null && !in_array($fetchSite, ['same-origin', 'none'], true)) {
            abort(403, 'Solicitud de origen no permitido.');
        }

        $user = auth()->user();

        if (!$this->canViewContent($user, $content)) {
            abort(403, 'No tienes permiso para acceder a este contenido.');
        }

        if ($content->is_external || $content->resolveStorageDisk() === null) {
            abort(404, 'Archivo no encontrado.');
        }

        $token = bin2hex(random_bytes(32));
        $isPdf = $content->type === 'pdf';

        Cache::put("induction_stream_token:{$token}", [
            'user_id' => $user->id,
            'session_id' => $request->session()->getId(),
            'content_id' => $content->id,
            'type' => $content->type,
            'single_use' => $isPdf,
        ], $isPdf ? now()->addSeconds(45) : now()->addMinutes(10));

        return response()->json([
            'stream_url' => route('dashboard.corporate-contents.stream', [
                'content' => $content->id,
                'token' => $token,
            ]),
            'type' => $content->type,
            'title' => $content->title,
            'watermark' => ($isPdf && $content->has_watermark)
                ? sprintf('Propiedad exclusiva de Boomer Elecomm, impresa por %s', $user->name)
                : null,
        ], 200, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Vary' => 'Sec-Fetch-Dest, Sec-Fetch-Mode, Sec-Fetch-Site',
        ]);
    }

    /**
     * Transmite los bytes del contenido validando Fetch Metadata, token efímero y sesión.
     */
    public function stream(Request $request, CorporateContent $content, string $token)
    {
        $fetchSite = $request->header('Sec-Fetch-Site');
        $fetchMode = $request->header('Sec-Fetch-Mode');
        $fetchDest = $request->header('Sec-Fetch-Dest');

        if ($fetchSite !== null && $fetchSite !== 'same-origin') {
            abort(403, 'Acceso directo no permitido.');
        }

        if ($fetchMode === 'navigate' || $fetchDest === 'document') {
            abort(403, 'Acceso directo no permitido.');
        }

        if ($content->type === 'pdf' && $request->header('X-Content-Viewer') !== '1') {
            abort(403, 'Solicitud inválida.');
        }

        $cacheKey = "induction_stream_token:{$token}";
        $payload = $content->type === 'pdf'
            ? Cache::pull($cacheKey)
            : Cache::get($cacheKey);

        if (
            !is_array($payload) ||
            (int) ($payload['content_id'] ?? 0) !== (int) $content->id ||
            (int) ($payload['user_id'] ?? 0) !== (int) auth()->id() ||
            !hash_equals((string) ($payload['session_id'] ?? ''), (string) $request->session()->getId())
        ) {
            abort(403, 'Enlace expirado o no autorizado.');
        }

        if (!$this->canViewContent(auth()->user(), $content)) {
            abort(403, 'No tienes permiso para acceder a este contenido.');
        }

        $disk = $content->resolveStorageDisk();
        if (!$disk || $content->is_external) {
            abort(404, 'Archivo no encontrado.');
        }

        $headers = [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Content-Security-Policy' => "default-src 'none'; frame-ancestors 'none'",
            'X-Frame-Options' => 'DENY',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            'Vary' => 'Sec-Fetch-Dest, Sec-Fetch-Mode, Sec-Fetch-Site',
            'Content-Type' => $content->type === 'pdf'
                ? 'application/octet-stream'
                : (Storage::disk($disk)->mimeType($content->file_path) ?: 'video/mp4'),
        ];

        return response()->file(Storage::disk($disk)->path($content->file_path), $headers);
    }

    private function isRecipient(User $user, CorporateContent $content): bool
    {
        return CorporateContent::visibleTo($user)
            ->where('corporate_contents.id', $content->id)
            ->exists();
    }

    private function canViewContent(User $user, CorporateContent $content): bool
    {
        if ($user->hasAnyRole(['admin', 'developer'])) {
            return true;
        }

        return $this->isRecipient($user, $content);
    }
}
