<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\Request;

class MaterialController extends Controller
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
        $materials = Material::with('category')->where('active', 1)->get();
        return view('backoffice.material.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = MaterialCategory::all();
        return view('backoffice.material.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'material_category_id' => 'required|exists:material_categories,material_category_id',
            'material_name'        => 'required|string|max:255',
            'material_description' => 'nullable|string',
            'material_unit'        => 'nullable|string|max:100',
            'stock'                => 'required|integer',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        Material::create($data);

        return redirect()->route('material.index')->with('success', 'Registro creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        $categories = MaterialCategory::where('active', 1)->get();
        return view('backoffice.material.edit', compact('material', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'material_category_id' => 'required|exists:material_categories,material_category_id',
            'material_name'        => 'required|string|max:255',
            'material_description' => 'nullable|string',
            'material_unit'        => 'nullable|string|max:100',
            'stock'                => 'required|integer',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active') ? 1 : 0;

        $material->update($data);

        return redirect()->route('material.index')->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('material.index')->with('success', 'Registro eliminado correctamente.');
    }

    /**
     * Show the view for importing materials.
     */
    public function import()
    {
        return view('backoffice.material.import');
    }

    /**
     * Process the imported CSV file.
     * Expected columns: category, material, unit, stock
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');

        $inserted      = [];
        $updated       = [];
        $skipped_rows  = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Skip header row
            fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Require at least 4 columns
                if (count($row) < 4) {
                    $skipped_rows++;
                    continue;
                }

                $categoryName = trim($row[0]);
                $materialName = trim($row[1]);
                $unit         = trim($row[2]);
                $csvStock     = (int) trim($row[3]);

                if (empty($categoryName) || empty($materialName)) {
                    $skipped_rows++;
                    continue;
                }

                // 1. Find or create category
                $category = MaterialCategory::firstOrCreate(
                    ['material_category_name' => $categoryName],
                    ['active' => 1]
                );

                // 2. Check if material already exists
                $material = Material::where('material_category_id', $category->material_category_id)
                    ->where('material_name', $materialName)
                    ->first();

                if (!$material) {
                    // INSERT new material
                    Material::create([
                        'material_category_id' => $category->material_category_id,
                        'material_name'        => $materialName,
                        'material_unit'        => $unit,
                        'stock'                => $csvStock,
                        'active'               => 1,
                    ]);

                    $inserted[] = [
                        'category' => $categoryName,
                        'material' => $materialName,
                        'unit'     => $unit,
                        'stock'    => $csvStock,
                    ];
                } else {
                    // UPDATE stock only if CSV stock is greater than current DB stock
                    if ($csvStock > $material->stock) {
                        $oldStock = $material->stock;
                        $material->stock         = $csvStock;
                        $material->material_unit = $unit;
                        $material->save();

                        $updated[] = [
                            'category'  => $categoryName,
                            'material'  => $materialName,
                            'unit'      => $unit,
                            'old_stock' => $oldStock,
                            'new_stock' => $csvStock,
                        ];
                    }
                    // If CSV stock <= DB stock, do nothing (no update, no insert)
                }
            }

            fclose($handle);
        }

        return view('backoffice.material.import', compact('inserted', 'updated', 'skipped_rows'))
            ->with('success', 'Archivo procesado exitosamente.');
    }
}
