<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasAnyRole(['admin', 'developer', 'supervisor'])) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    private function getCountries()
    {
        return [
            1 => 'Colombia',
            2 => 'Argentina',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $works = Work::with(['location.client', 'supervisor'])->get();
        $countries = $this->getCountries();
        return view('backoffice.work.index', compact('works', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locations = Location::with('client')->where('active', 1)->get();
        $supervisors = User::whereHas('roles', function ($q) {
            $q->where('slug', 'supervisor');
        })->where('active', 1)->get();
        return view('backoffice.work.create', compact('locations', 'supervisors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,location_id',
            'work_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('works', 'work_name')->where(function ($query) use ($request) {
                    return $query->where('location_id', $request->location_id);
                })
            ],
            'work_description' => 'nullable|string',
            'supervisor_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'work_status' => 'required|in:pending,in_progress,completed',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        Work::create($data);

        return redirect()->route('work.index')->with('success', 'Registro creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Work $work)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Work $work)
    {
        $locations = Location::with('client')->where('active', 1)->get();
        $supervisors = User::whereHas('roles', function ($q) {
            $q->where('slug', 'supervisor');
        })->where('active', 1)->get();
        return view('backoffice.work.edit', compact('work', 'locations', 'supervisors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Work $work)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,location_id',
            'work_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('works', 'work_name')->where(function ($query) use ($request) {
                    return $query->where('location_id', $request->location_id);
                })->ignore($work->work_id, 'work_id')
            ],
            'work_description' => 'required|string',
            'supervisor_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'work_status' => 'required|in:pending,in_progress,completed',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        $work->update($data);

        return redirect()->route('work.index')->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Work $work)
    {
        // 1. Que no tenga una requisición de materiales abierta
        $openRequisitionsCount = $work->requisitions()
            ->whereIn('status', ['pending', 'approved', 'purchased'])
            ->count();
            
        if ($openRequisitionsCount > 0) {
            return redirect()->route('work.index')->with('error', 'No se puede eliminar la obra porque tiene requisiciones de materiales abiertas.');
        }

        // 2. Que no tenga técnico, auxiliar o externo asociado
        if ($work->users()->count() > 0) {
            return redirect()->route('work.index')->with('error', 'No se puede eliminar la obra porque tiene personal asignado (técnico, auxiliar o externo).');
        }

        // 3. Que no haya horas trabajadas en esta obra
        if ($work->timeLogs()->count() > 0) {
            return redirect()->route('work.index')->with('error', 'No se puede eliminar la obra porque ya existen horas trabajadas registradas.');
        }

        $work->delete();
        return redirect()->route('work.index')->with('success', 'Registro eliminado correctamente.');
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Work $work)
    {
        $newWork = $work->replicate();
        
        $newWork->work_name = 'Copia de ' . $newWork->work_name;

        // Validar si ya existe el nombre para evitar error de Unique
        $exists = Work::where('location_id', $newWork->location_id)
            ->where('work_name', $newWork->work_name)
            ->exists();
            
        if ($exists) {
            $newWork->work_name = $newWork->work_name . ' (' . time() . ')';
        }

        $newWork->save();

        // Duplicar también los usuarios/técnicos asignados
        $userIds = $work->users()->pluck('users.id');
        if ($userIds->isNotEmpty()) {
            $newWork->users()->attach($userIds);
        }

        return redirect()->route('work.index')->with('success', 'Proyecto duplicado correctamente.');
    }

    /**
     * View to manage technicians assigned to works.
     */
    public function techniciansIndex()
    {
        $works = Work::where('active', 1)->with('location.client')->get()->map(function($work) {
            if ($work->location) {
                $work->location->location_name = mb_strtoupper($work->location->location_name, 'UTF-8');
            }
            $work->work_name = mb_strtoupper($work->work_name, 'UTF-8');
            return $work;
        })->sortBy(function($work) {
            return ($work->location->location_name ?? '') . ' - ' . ($work->work_name ?? '');
        });
        $technicians = User::whereHas('roles', function ($q) {
            $q->where('slug', 'technician');
        })->where('active', 1)->orderBy('name')->get();
        $assistants = User::whereHas('roles', function ($q) {
            $q->where('slug', 'assistant');
        })->where('active', 1)->orderBy('name')->get();
        $externals = User::whereHas('roles', function ($q) {
            $q->where('slug', 'external');
        })->where('active', 1)->orderBy('name')->get();
        return view('backoffice.work.technicians', compact('works', 'technicians', 'assistants', 'externals'));
    }

    /**
     * Get technicians assigned to a specific work.
     */
    public function getTechnicians(Work $work)
    {
        $assigned = $work->users()->whereHas('roles', function ($q) {
            $q->where('slug', 'technician');
        })->select('users.id', 'users.name', 'users.email')->get();

        $assignedAssistants = $work->users()->whereHas('roles', function ($q) {
            $q->where('slug', 'assistant');
        })->select('users.id', 'users.name', 'users.email')->get();

        $assignedExternals = $work->users()->whereHas('roles', function ($q) {
            $q->where('slug', 'external');
        })->select('users.id', 'users.name', 'users.email')->get();

        return response()->json([
            'assigned' => $assigned,
            'assigned_assistants' => $assignedAssistants,
            'assigned_externals' => $assignedExternals
        ]);
    }

    /**
     * Assign a technician to a specific work.
     */
    public function addTechnician(Request $request, Work $work)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if (!$work->users()->where('users.id', $request->user_id)->exists()) {
            $work->users()->attach($request->user_id);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'El técnico ya está asignado a este proyecto.']);
    }

    /**
     * Remove a technician from a specific work.
     */
    public function removeTechnician(Request $request, Work $work)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $work->users()->detach($request->user_id);

        return response()->json(['success' => true]);
    }
}
