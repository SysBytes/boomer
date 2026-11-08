<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use App\Models\DocumentSubcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
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
        $subcategories = DocumentSubcategory::with('category')->withCount('contents')->orderBy('name')->get();
        return view('backoffice.induction.subcategory.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = DocumentCategory::orderBy('name')->get();
        return view('backoffice.induction.subcategory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:document_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        DocumentSubcategory::create($data);

        return redirect()->route('induction.subcategories.index')->with('success', 'Subcategoría creada correctamente.');
    }

    public function edit(DocumentSubcategory $subcategory)
    {
        $categories = DocumentCategory::orderBy('name')->get();
        return view('backoffice.induction.subcategory.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, DocumentSubcategory $subcategory)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:document_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $subcategory->update($data);

        return redirect()->route('induction.subcategories.index')->with('success', 'Subcategoría actualizada correctamente.');
    }

    public function destroy(DocumentSubcategory $subcategory)
    {
        if ($subcategory->contents()->exists()) {
            return redirect()->route('induction.subcategories.index')
                ->with('error', 'No se puede eliminar la subcategoría porque tiene contenidos asociados.');
        }

        $subcategory->delete();

        return redirect()->route('induction.subcategories.index')->with('success', 'Subcategoría eliminada correctamente.');
    }
}

