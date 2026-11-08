<?php

namespace App\Http\Controllers;

use App\Models\MaterialCategory;
use Illuminate\Http\Request;

class MaterialCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasAnyRole(['admin', 'developer', 'purchasing'])) {
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
        $categories = MaterialCategory::all();
        return view('backoffice.material_category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backoffice.material_category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'material_category_name' => 'required|string|max:255',
            'material_category_description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        MaterialCategory::create($data);

        return redirect()->route('material_category.index')->with('success', 'Categoría agregada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialCategory $materialCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialCategory $materialCategory)
    {
        return view('backoffice.material_category.edit', compact('materialCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialCategory $materialCategory)
    {
        $request->validate([
            'material_category_name' => 'required|string|max:255',
            'material_category_description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        $materialCategory->update($data);

        return redirect()->route('material_category.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialCategory $materialCategory)
    {
        $materialCategory->delete();
        return redirect()->route('material_category.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
