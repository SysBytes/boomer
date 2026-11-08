<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
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
        $clients = Client::all();
        $countries = \App\Models\Country::where('active', 1)->get();
        return view('backoffice.client.index', compact('clients', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = \App\Models\Country::where('active', 1)->get();
        return view('backoffice.client.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255|unique:clients,client_name',
            'client_email' => 'required|email|max:255|unique:clients,client_email',
            'client_phone' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        Client::create($data);

        return redirect()->route('client.index')->with('success', 'Registro creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $countries = \App\Models\Country::where('active', 1)->get();
        return view('backoffice.client.edit', compact('client', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'client_name' => 'required|string|max:255|unique:clients,client_name,' . $client->client_id . ',client_id',
            'client_email' => 'required|email|max:255|unique:clients,client_email,' . $client->client_id . ',client_id',
            'client_phone' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        $client->update($data);

        return redirect()->route('client.index')->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('client.index')->with('success', 'Registro eliminado correctamente.');
    }
}
