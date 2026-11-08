<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Requisition;
use App\Models\TimeLog;
use App\Models\User;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show the materials consumed by project report.
     * Only works that have at least one delivered requisition are selectable.
     */
    public function materialsByWork(Request $request)
    {
        // Projects that have at least one delivered requisition
        $works = Work::whereHas('requisitions', function ($q) {
            $q->where('status', 'delivered');
        })->with('location.client')->get();

        $selectedWork = null;
        $materials    = collect();

        if ($request->filled('work_id')) {
            $selectedWork = Work::with('location.client')->findOrFail($request->work_id);

            // Aggregate materials from all delivered requisitions of this work
            $materials = DB::table('material_requisition')
                ->join('requisitions', 'requisitions.requisition_id', '=', 'material_requisition.requisition_id')
                ->join('materials', 'materials.material_id', '=', 'material_requisition.material_id')
                ->join('material_categories', 'material_categories.material_category_id', '=', 'materials.material_category_id')
                ->where('requisitions.work_id', $request->work_id)
                ->where('requisitions.status', 'delivered')
                ->select(
                    'material_categories.material_category_name',
                    'materials.material_name',
                    'materials.material_unit',
                    DB::raw('SUM(material_requisition.quantity) as total_quantity')
                )
                ->groupBy(
                    'material_categories.material_category_name',
                    'materials.material_name',
                    'materials.material_unit'
                )
                ->orderBy('material_categories.material_category_name')
                ->orderBy('materials.material_name')
                ->get();
        }

        return view('backoffice.reports.materials_by_work', compact('works', 'selectedWork', 'materials'));
    }

    /**
     * Current inventory report — filterable by stock status.
     */
    public function inventory(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all | with_stock | no_stock

        $query = \App\Models\Material::with('category')
            ->where('active', 1)
            ->orderBy('material_category_id')
            ->orderBy('material_name');

        if ($filter === 'with_stock') {
            $query->where('stock', '>', 0);
        } elseif ($filter === 'no_stock') {
            $query->where('stock', '<=', 0);
        }

        $materials = $query->get()->groupBy(fn($m) => $m->category->material_category_name ?? 'Sin Categoría');

        return view('backoffice.reports.inventory', compact('materials', 'filter'));
    }

    /**
     * Report of users assigned to works.
     * Shows works and their associated users with technician, assistant, or external roles.
     */
    public function workUsers(Request $request)
    {
        // Solo accesible para roles developer y admin
        if (!auth()->user()->hasAnyRole(['developer', 'admin'])) {
            abort(403, 'Acceso denegado. No tienes permisos para ver este reporte.');
        }

        // Obras que tienen usuarios asignados con estos roles
        $works = Work::with(['location.client', 'users.roles'])
            ->whereHas('users', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('slug', ['technician', 'assistant', 'external']);
                });
            })
            ->get()->map(function($work) {
                if ($work->location) {
                    $work->location->location_name = mb_strtoupper($work->location->location_name, 'UTF-8');
                }
                $work->work_name = mb_strtoupper($work->work_name, 'UTF-8');
                return $work;
            })->sortBy(function($work) {
                return ($work->location->location_name ?? '') . ' - ' . ($work->work_name ?? '');
            });

        $filteredWorks = $works;

        if ($request->filled('work_id')) {
            $filteredWorks = $works->where('work_id', $request->work_id);
        }

        $reportData = [];

        foreach ($filteredWorks as $work) {
            $assignments = collect();

            foreach ($work->users as $user) {
                if ($user->hasAnyRole(['technician', 'assistant', 'external'])) {
                    // Traducción de roles para mostrar en la vista
                    $roleName = 'N/A';
                    $roleOrder = 99;
                    
                    if ($user->hasRole('technician')) { 
                        $roleName = 'Técnico'; 
                        $roleOrder = 1; 
                    } elseif ($user->hasRole('assistant')) { 
                        $roleName = 'Auxiliar'; 
                        $roleOrder = 2; 
                    } elseif ($user->hasRole('external')) { 
                        $roleName = 'Externo'; 
                        $roleOrder = 3; 
                    }

                    $assignments->push((object)[
                        'user_name'   => $user->name,
                        'user_email'  => $user->email,
                        'user_role'   => $roleName,
                        'role_order'  => $roleOrder
                    ]);
                }
            }

            // Ordenar por rol
            $assignments = $assignments->sortBy('role_order');

            if ($assignments->isNotEmpty()) {
                $reportData[] = (object)[
                    'work' => $work,
                    'assignments' => $assignments
                ];
            }
        }

        return view('backoffice.reports.work_users', compact('works', 'reportData'));
    }

    /**
     * Reporte de horas trabajadas por usuario (Regulares y Extras)
     */
    public function workedHours(Request $request)
    {
        if (!$request->has('start_date') && !$request->has('end_date')) {
            $request->merge([
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d'),
            ]);
        }

        $query = TimeLog::with(['user', 'work.location.client', 'overtimes' => function($q) {
            $q->where('status', 'approved');
        }])
        ->whereNotNull('check_out_time')
        ->whereNotNull('hours_worked');

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
            $query->whereDate('check_in_time', '<=', $request->end_date);
        }

        $timeLogs = $query->orderBy('check_in_time', 'desc')->get();
        
        $reportRows = collect();

        // Cargar feriados de todos los países de una vez para optimizar
        $holidays = Holiday::where('active', 1)->get();

        foreach ($timeLogs as $log) {
            $date = Carbon::parse($log->check_in_time);
            $hoursWorked = (float) $log->hours_worked;
            $countryId = $log->work->location->client->country_id ?? null;

            // Determinar si es feriado
            $isHoliday = false;
            if ($countryId) {
                foreach ($holidays->where('country_id', $countryId) as $holiday) {
                    $holDate = Carbon::parse($holiday->holiday_date);
                    if ($holiday->is_annual) {
                        if ($holDate->month == $date->month && $holDate->day == $date->day) {
                            $isHoliday = true;
                            break;
                        }
                    } else {
                        if ($holDate->isSameDay($date)) {
                            $isHoliday = true;
                            break;
                        }
                    }
                }
            }

            // Calcular horas regulares
            $regularHours = 0;
            
            if ($date->isSunday() || $isHoliday) {
                $regularHours = 0; // Todo es extra en domingo o feriado
            } else if ($date->isSaturday()) {
                $regularHours = min($hoursWorked, 4); // Sábados max 4 hrs
            } else {
                // Lunes a Viernes (Max 9h de jornada, de las cuales 8 son pagables si se toma el descanso)
                $capped = min($hoursWorked, 9);
                // Si trabaja más de 5 horas, asumimos que se toma la hora de almuerzo
                $regularHours = $capped > 5 ? $capped - 1 : $capped;
            }

            // Añadir fila de Horas Regulares si hay
            if ($regularHours > 0) {
                $reportRows->push((object)[
                    'date' => $date->format('d/m/Y'),
                    'user_name' => $log->user->name ?? 'N/A',
                    'work_name' => $log->work->work_name ?? 'N/A',
                    'type' => 'Horas Regulares',
                    'type_badge' => '<span class="badge bg-primary">Regular</span>',
                    'hours' => round($regularHours, 2),
                    'sort_date' => $date->format('Y-m-d H:i:s'),
                    'check_in' => $log->check_in_time ? \Carbon\Carbon::parse($log->check_in_time)->format('h:i A') : '-',
                    'check_out' => $log->check_out_time ? \Carbon\Carbon::parse($log->check_out_time)->format('h:i A') : '-',
                ]);
            }

            // Añadir filas de Horas Extras Aprobadas
            foreach ($log->overtimes as $overtime) {
                $reportRows->push((object)[
                    'date' => Carbon::parse($overtime->date)->format('d/m/Y'),
                    'user_name' => $log->user->name ?? 'N/A',
                    'work_name' => $log->work->work_name ?? 'N/A',
                    'type' => 'Horas Extras (' . $overtime->type_formatted . ')',
                    'type_badge' => '<span class="badge bg-success">Extra</span>',
                    'hours' => round($overtime->hours, 2),
                    'sort_date' => $date->format('Y-m-d H:i:s'), // Usar misma fecha para agrupar visualmente
                    'check_in' => $log->check_in_time ? \Carbon\Carbon::parse($log->check_in_time)->format('h:i A') : '-',
                    'check_out' => $log->check_out_time ? \Carbon\Carbon::parse($log->check_out_time)->format('h:i A') : '-',
                ]);
            }
        }

        // Agrupar por usuario para la vista
        $groupedRows = $reportRows->sortBy('sort_date')->groupBy('user_name');

        $users = User::with('roles')->whereHas('timeLogs', function ($q) {
            $q->whereNotNull('hours_worked');
        })->orderBy('name', 'asc')->get()->map(function($user) {
            $user->name = ucwords(strtolower($user->name));
            return $user;
        });
        
        $works = Work::where('active', 1)->with('location.client')->get()->map(function($work) {
            if ($work->location) {
                $work->location->location_name = mb_strtoupper($work->location->location_name, 'UTF-8');
            }
            $work->work_name = mb_strtoupper($work->work_name, 'UTF-8');
            return $work;
        })->sortBy(function($work) {
            return ($work->location->location_name ?? '') . ' - ' . ($work->work_name ?? '');
        });

        return view('backoffice.reports.worked_hours', compact('groupedRows', 'users', 'works'));
    }
}
