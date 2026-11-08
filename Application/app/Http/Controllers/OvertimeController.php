<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\TimeLog;
use App\Models\User;
use App\Models\Work;
use App\Services\OvertimeCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query
        $query = Overtime::with(['user', 'work.location.client', 'timeLog', 'approver']);

        // Si es supervisor (y no admin/developer), solo ve sus proyectos y usuarios technician/assistant
        if ($user->hasRole('supervisor') && !$user->hasAnyRole(['admin', 'developer'])) {
            $query->whereHas('work', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            })
                ->whereHas('user.roles', function ($q) {
                    $q->whereIn('slug', ['technician', 'assistant']);
                });
        }

        // Filtros
        if (!$request->has('status')) {
            $request->merge(['status' => 'pending']);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('work_id')) {
            $query->where('work_id', $request->work_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $overtimes = $query->orderBy('date', 'desc')->get();

        $users = User::whereIn('id', Overtime::select('user_id')->distinct())->get()->map(function($u) {
            $u->name = mb_strtoupper($u->name, 'UTF-8');
            return $u;
        })->sortBy('name');

        $worksQuery = Work::where('active', 1)->with('location.client');
        if ($user->hasRole('supervisor') && !$user->hasAnyRole(['admin', 'developer'])) {
            $worksQuery->where('supervisor_id', $user->id);
        }
        
        $works = $worksQuery->get()->map(function($work) {
            if ($work->location) {
                $work->location->location_name = mb_strtoupper($work->location->location_name, 'UTF-8');
            }
            $work->work_name = mb_strtoupper($work->work_name, 'UTF-8');
            return $work;
        })->sortBy(function($work) {
            return ($work->location->location_name ?? '') . ' - ' . ($work->work_name ?? '');
        });

        return view('backoffice.overtime.index', compact('overtimes', 'users', 'works'));
    }

    public function updateStatus(Request $request, Overtime $overtime)
    {
        $user = Auth::user();

        // Validar permisos
        if (!$user->hasAnyRole(['admin', 'developer']) && $overtime->supervisor_id != $user->id) {
            abort(403, 'No autorizado para aprobar esta hora extra.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        $overtime->update([
            'status' => $request->status,
            'approved_by' => $request->status === 'pending' ? null : $user->id
        ]);

        return redirect()->back()->with('success', 'Estado de hora extra actualizado correctamente.');
    }

    public function recalculate(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'developer'])) {
            abort(403, 'No autorizado para recalcular horas extras.');
        }

        // Reconstruir query de TimeLogs con los mismos filtros
        $query = TimeLog::query()->whereNotNull('check_out_time')->whereNotNull('hours_worked');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('work_id')) {
            $query->where('work_id', $request->work_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('check_in_time', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('check_out_time', '<=', $request->end_date);
        }

        $timeLogs = $query->get();
        $count = 0;

        foreach ($timeLogs as $timeLog) {
            OvertimeCalculator::calculateForTimeLog($timeLog);
            $count++;
        }

        return redirect()->back()->with('success', "Se han recalculado las horas extras para {$count} registros de asistencia.");
    }

    public function destroy(Overtime $overtime)
    {
        $user = Auth::user();
        if (!$user->hasRole('developer')) {
            abort(403, 'No autorizado para eliminar horas extras.');
        }

        $overtime->delete();

        return redirect()->back()->with('success', 'Registro de hora extra eliminado correctamente.');
    }
}
