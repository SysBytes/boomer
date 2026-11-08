<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Client;
use Illuminate\Http\Request;

class LocationController extends Controller
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
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::with('client')->withCount('works')->get();
        return view('backoffice.location.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('active', 1)->get();
        return view('backoffice.location.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id'            => 'required|exists:clients,client_id',
            'location_name'        => 'required|string|max:255',
            'location_description' => 'nullable|string',
            'location_city'        => 'nullable|string|max:255',
            'location_address'     => 'required|string',
            'location_reference'   => 'nullable|string',
            'location_lat'         => 'nullable|string|max:255',
            'location_lng'         => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        Location::create($data);

        return redirect()->route('location.index')->with('success', 'Locación creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        $clients = Client::where('active', 1)->get();
        return view('backoffice.location.edit', compact('location', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $request->validate([
            'client_id'            => 'required|exists:clients,client_id',
            'location_name'        => 'required|string|max:255',
            'location_description' => 'nullable|string',
            'location_city'        => 'nullable|string|max:255',
            'location_address'     => 'required|string',
            'location_reference'   => 'nullable|string',
            'location_lat'         => 'nullable|string|max:255',
            'location_lng'         => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        $location->update($data);

        return redirect()->route('location.index')->with('success', 'Locación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        if ($location->works()->count() > 0) {
            return redirect()->route('location.index')->with('error', 'No se puede eliminar la locación porque tiene obras asociadas.');
        }

        $location->delete();
        return redirect()->route('location.index')->with('success', 'Locación eliminada correctamente.');
    }
}
