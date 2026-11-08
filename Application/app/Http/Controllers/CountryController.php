<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
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

    public function index()
    {
        $countries = Country::all();
        return view('backoffice.country.index', compact('countries'));
    }

    public function create()
    {
        return view('backoffice.country.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'phone_code' => 'required|string|max:10',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        Country::create($data);

        return redirect()->route('country.index')->with('success', 'País creado correctamente.');
    }

    public function show(Country $country)
    {
        //
    }

    public function edit(Country $country)
    {
        return view('backoffice.country.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'phone_code' => 'required|string|max:10',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        $country->update($data);

        return redirect()->route('country.index')->with('success', 'País actualizado correctamente.');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('country.index')->with('success', 'País eliminado correctamente.');
    }
}
