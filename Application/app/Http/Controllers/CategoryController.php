<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
        $categories = DocumentCategory::withCount('subcategories')->orderBy('name')->get();
        return view('backoffice.induction.category.index', compact('categories'));
    }

    public function create()
    {
        return view('backoffice.induction.category.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        DocumentCategory::create($data);

        return redirect()->route('induction.categories.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(DocumentCategory $category)
    {
        return view('backoffice.induction.category.edit', compact('category'));
    }

    public function update(Request $request, DocumentCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($data);

        return redirect()->route('induction.categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(DocumentCategory $category)
    {
        if ($category->subcategories()->exists()) {
            return redirect()->route('induction.categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene subcategorías asociadas.');
        }

        $category->delete();

        return redirect()->route('induction.categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}

