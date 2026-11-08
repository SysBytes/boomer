<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('developer')) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $roles = Role::all();
        return view('backoffice.role.index', compact('roles'));
    }

    public function create()
    {
        return view('backoffice.role.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
        ]);

        Role::create($request->all());

        return redirect()->route('role.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        return view('backoffice.role.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
        ]);

        $role->update($request->all());

        return redirect()->route('role.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('role.index')->with('success', 'Rol eliminado correctamente.');
    }
}
