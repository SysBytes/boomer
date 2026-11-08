<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
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

    // getCountries() eliminado para usar el modelo Country

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $holidays = Holiday::orderBy('holiday_date', 'asc')->get();
        $countries = \App\Models\Country::where('active', 1)->get();
        return view('backoffice.holiday.index', compact('holidays', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = \App\Models\Country::where('active', 1)->get();
        return view('backoffice.holiday.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'country_id'   => 'required|exists:countries,id',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;
        $data['is_annual'] = $request->has('is_annual') ? 1 : 0;

        Holiday::create($data);

        return redirect()->route('holiday.index')->with('success', 'Registro creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Holiday $holiday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Holiday $holiday)
    {
        $countries = \App\Models\Country::where('active', 1)->get();
        return view('backoffice.holiday.edit', compact('holiday', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'country_id'   => 'required|exists:countries,id',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;
        $data['is_annual'] = $request->has('is_annual') ? 1 : 0;

        $holiday->update($data);

        return redirect()->route('holiday.index')->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holiday.index')->with('success', 'Registro eliminado correctamente.');
    }
}
