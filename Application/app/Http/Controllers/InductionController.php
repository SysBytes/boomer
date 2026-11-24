<?php

namespace App\Http\Controllers;

use App\Models\CorporateContent;
use App\Models\User;
use App\Models\UserContentProgress;
use App\Services\InductionEvidenceService;
use Illuminate\Http\Request;

class InductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Pantalla principal de Inducción Corporativa obligatoria y secuencial.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $contents = $this->mandatoryContentsFor($user);

        if ($contents->isEmpty()) {
            return redirect()->route('dashboard.corporate-contents.index');
        }

        $progressMap = UserContentProgress::where('user_id', $user->id)
            ->whereIn('content_id', $contents->pluck('id'))
            ->get()
            ->keyBy('content_id');

        $currentContent = null;
        $currentProgress = null;
        $currentStepNumber = null;
        $completedCount = 0;

        foreach ($contents as $idx => $item) {
            $progress = $progressMap->get($item->id);
            if ($this->isContentCompleted($progress, $item)) {
                $completedCount++;
                continue;
            }

            if ($currentContent === null) {
                $currentContent = $item;
                $currentStepNumber = $idx + 1;
            }
        }

        if ($currentContent !== null) {
            $currentProgress = UserContentProgress::firstOrCreate(
                ['user_id' => $user->id, 'content_id' => $currentContent->id],
                ['view_time_seconds' => 0, 'status' => 'pending']
            );
            $progressMap->put($currentContent->id, $currentProgress);
        }

        $allContentsCompleted = ($currentContent === null);

        return view('induction.index', compact(
            'user',
            'contents',
            'progressMap',
            'currentContent',
            'currentProgress',
            'currentStepNumber',
            'completedCount',
            'allContentsCompleted'
        ));
    }

    /**
     * Registra y acumula el tiempo de permanencia real sobre el contenido actual.
     */
    public function heartbeat(Request $request, CorporateContent $content)
    {
        $user = auth()->user();
        $contents = $this->mandatoryContentsFor($user);

        $position = $contents->search(fn ($item) => (int) $item->id === (int) $content->id);
        if ($position === false) {
            abort(403, 'No tienes acceso a este contenido obligatorio.');
        }

        $progressMap = UserContentProgress::where('user_id', $user->id)
            ->whereIn('content_id', $contents->pluck('id'))
            ->get()
            ->keyBy('content_id');

        if ($this->hasPendingPredecessors($contents, (int) $position, $progressMap)) {
            return response()->json([
                'message' => 'Debes completar los contenidos anteriores en orden secuencial.',
            ], 422);
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
     * Valida que se haya cumplido el tiempo requerido y marca el contenido como completado.
     */
    public function completeContent(Request $request, CorporateContent $content)
    {
        $user = auth()->user();
        $contents = $this->mandatoryContentsFor($user);

        $position = $contents->search(fn ($item) => (int) $item->id === (int) $content->id);
        if ($position === false) {
            abort(403, 'No tienes acceso a este contenido obligatorio.');
        }

        $progressMap = UserContentProgress::where('user_id', $user->id)
            ->whereIn('content_id', $contents->pluck('id'))
            ->get()
            ->keyBy('content_id');

        if ($this->hasPendingPredecessors($contents, (int) $position, $progressMap)) {
            $msg = 'Debes completar los contenidos anteriores en orden secuencial.';
            if ($request->expectsJson()) {
                return response()->json(['completed' => false, 'message' => $msg], 422);
            }
            return redirect()->route('induction.index')->with('error', $msg);
        }

        $progress = UserContentProgress::firstOrCreate(
            ['user_id' => $user->id, 'content_id' => $content->id],
            ['view_time_seconds' => 0, 'status' => 'pending']
        );

        $required = max(0, (int) $content->required_time_seconds);

        // Permite acreditar los últimos segundos desde el último heartbeat (máx. +15s) sin permitir saltarse contenidos largos sin heartbeats previos
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
            $msg = "Aún debes permanecer {$remaining} segundos más en este contenido para completarlo.";

            if ($request->expectsJson()) {
                return response()->json([
                    'completed' => false,
                    'message' => $msg,
                    'view_time_seconds' => (int) $progress->view_time_seconds,
                    'required_time_seconds' => $required,
                ], 422);
            }

            return redirect()->route('induction.index')->with('error', $msg);
        }

        $progress->status = 'completed';
        if ($progress->completed_at === null) {
            $progress->completed_at = now();
        }
        $progress->save();

        if ($request->expectsJson()) {
            return response()->json([
                'completed' => true,
                'completed_at_label' => $progress->completed_at->format('d/m/Y H:i'),
                'redirect_url' => route('induction.index'),
            ]);
        }

        return redirect()->route('induction.index')->with('success', 'Contenido completado correctamente.');
    }

    /**
     * Certifica la inducción, guarda la evidencia fotográfica y firma digital en storage privado
     * y cambia el estado del usuario a completed.
     */
    public function certify(Request $request, InductionEvidenceService $evidenceService)
    {
        $user = auth()->user();
        $contents = $this->mandatoryContentsFor($user);

        if ($contents->isEmpty()) {
            $target = route('dashboard.corporate-contents.index');
            if ($request->expectsJson()) {
                return response()->json([
                    'certified' => false,
                    'redirect_url' => $target,
                ], 422);
            }

            return redirect($target);
        }

        $progressMap = UserContentProgress::where('user_id', $user->id)
            ->whereIn('content_id', $contents->pluck('id'))
            ->get()
            ->keyBy('content_id');

        foreach ($contents as $item) {
            if (!$this->isContentCompleted($progressMap->get($item->id), $item)) {
                $msg = 'Debes completar todos los contenidos obligatorios antes de certificar tu inducción.';
                if ($request->expectsJson()) {
                    return response()->json(['certified' => false, 'message' => $msg], 422);
                }
                return redirect()->route('induction.index')->with('error', $msg);
            }
        }

        $evidenceService->storeEvidence($request, $user);

        $user->update(['induction_status' => 'completed']);

        $targetRoute = route('dashboard.corporate-contents.index');

        if ($request->expectsJson()) {
            return response()->json([
                'certified' => true,
                'redirect_url' => $targetRoute,
            ]);
        }

        return redirect($targetRoute)->with('success', '¡Inducción corporativa completada y certificada exitosamente!');
    }

    private function mandatoryContentsFor(User $user)
    {
        return CorporateContent::visibleTo($user)
            ->where('corporate_contents.is_mandatory', true)
            ->with(['subcategory.category'])
            ->orderBy('corporate_contents.subcategory_id')
            ->orderBy('corporate_contents.id')
            ->get();
    }

    private function isContentCompleted(?UserContentProgress $progress, CorporateContent $content): bool
    {
        if ($progress === null || $progress->status !== 'completed') {
            return false;
        }

        return (int) $progress->view_time_seconds >= max(0, (int) $content->required_time_seconds);
    }

    private function hasPendingPredecessors($contents, int $position, $progressMap): bool
    {
        for ($i = 0; $i < $position; $i++) {
            $prev = $contents->get($i);
            if ($prev && !$this->isContentCompleted($progressMap->get($prev->id), $prev)) {
                return true;
            }
        }

        return false;
    }
}
