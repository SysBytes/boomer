<?php

namespace App\Http\Controllers;

use App\Models\CorporateContent;
use App\Models\DocumentCategory;
use Illuminate\Support\Facades\Storage;

class UserContentLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Biblioteca de contenidos corporativos del usuario autenticado,
     * agrupada por Categoría -> Subcategoría y filtrada por rol/usuario.
     */
    public function index()
    {
        $user = auth()->user();

        $categories = DocumentCategory::query()
            ->whereHas('subcategories.contents', function ($q) use ($user) {
                $q->visibleTo($user);
            })
            ->with(['subcategories' => function ($sub) use ($user) {
                $sub->whereHas('contents', function ($q) use ($user) {
                    $q->visibleTo($user);
                })
                ->with(['contents' => function ($q) use ($user) {
                    $q->visibleTo($user)->orderBy('title');
                }])
                ->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('dashboard.corporate_contents.index', compact('categories'));
    }

    /**
     * Muestra o transmite el archivo corporativo asegurando permisos y compatibilidad con hosting.
     */
    public function viewFile(CorporateContent $content)
    {
        $user = auth()->user();

        // Los administradores y desarrolladores siempre pueden previsualizar cualquier contenido
        if (!$user->hasAnyRole(['admin', 'developer'])) {
            $canView = CorporateContent::visibleTo($user)->where('corporate_contents.id', $content->id)->exists();
            if (!$canView) {
                abort(403, 'No tienes permiso para acceder a este contenido.');
            }
        }

        if ($content->is_external) {
            return redirect()->away($content->url);
        }

        if (!$content->file_path || !Storage::disk('public')->exists($content->file_path)) {
            abort(404, 'El archivo solicitado no existe o fue removido.');
        }

        return Storage::disk('public')->response($content->file_path);
    }
}
