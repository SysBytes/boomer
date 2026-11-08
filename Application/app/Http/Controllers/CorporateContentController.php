<?php

namespace App\Http\Controllers;

use App\Models\CorporateContent;
use App\Models\DocumentSubcategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CorporateContentController extends Controller
{
    /** Disco donde se almacenan los archivos físicos (PDF / video local). */
    private const DISK = 'public';

    /** Carpeta dentro del disco. */
    private const FOLDER = 'induction';

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
        $contents = CorporateContent::with(['subcategory.category', 'roles', 'targetUser'])
            ->orderByDesc('id')
            ->get();

        return view('backoffice.induction.content.index', compact('contents'));
    }

    public function create()
    {
        return view('backoffice.induction.content.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $payload = $this->buildPayload($request, $data);

        $content = CorporateContent::create($payload);

        if ($data['target_type'] === 'roles') {
            $this->syncRoles($content, $data['roles']);
        } else {
            $content->roles()->sync([]);
        }

        return redirect()->route('induction.contents.index')->with('success', 'Contenido creado correctamente.');
    }

    public function edit(CorporateContent $content)
    {
        $content->load(['roles', 'targetUser']);

        return view('backoffice.induction.content.edit', array_merge(
            $this->formData(),
            ['content' => $content]
        ));
    }

    public function update(Request $request, CorporateContent $content)
    {
        $data = $this->validated($request, $content);

        $payload = $this->buildPayload($request, $data, $content);

        $content->update($payload);

        if ($data['target_type'] === 'roles') {
            $this->syncRoles($content, $data['roles'], true);
        } else {
            $content->roles()->sync([]);
        }

        return redirect()->route('induction.contents.index')->with('success', 'Contenido actualizado correctamente.');
    }

    /**
     * Elimina el contenido. Si ya tiene progreso de usuarios registrado,
     * no se borra: se inactiva para conservar el histórico de cumplimiento.
     */
    public function destroy(CorporateContent $content)
    {
        if ($content->userProgress()->exists()) {
            $content->update(['is_active' => false]);

            return redirect()->route('induction.contents.index')
                ->with('success', 'El contenido tiene progreso de usuarios registrado, por lo que fue inactivado en lugar de eliminado.');
        }

        $this->deleteStoredFile($content->file_path);
        $content->delete();

        return redirect()->route('induction.contents.index')->with('success', 'Contenido eliminado correctamente.');
    }

    /**
     * Activa o inactiva un contenido sin borrarlo.
     */
    public function toggle(CorporateContent $content)
    {
        $content->update(['is_active' => !$content->is_active]);

        return redirect()->route('induction.contents.index')
            ->with('success', $content->is_active ? 'Contenido activado.' : 'Contenido inactivado.');
    }

    // ------------------------------------------------------------------

    private function getRoles()
    {
        $query = Role::orderBy('name');
        if (auth()->check() && !auth()->user()->hasRole('developer')) {
            $query->where('slug', '!=', 'developer');
        }
        return $query->get();
    }

    private function getUsers()
    {
        $query = User::with('roles')->where('active', 1)->orderBy('name');
        if (auth()->check() && !auth()->user()->hasRole('developer')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('slug', 'developer');
            });
        }
        return $query->get();
    }

    private function formData(): array
    {
        return [
            'subcategories' => DocumentSubcategory::with('category')->orderBy('name')->get(),
            'roles' => $this->getRoles(),
            'users' => $this->getUsers(),
        ];
    }

    /**
     * Sincroniza los roles asignados al contenido, filtrando el rol developer
     * si el usuario autenticado no tiene el rol developer.
     */
    private function syncRoles(CorporateContent $content, array $roleIds, bool $isUpdate = false): void
    {
        $isDeveloper = auth()->check() && auth()->user()->hasRole('developer');

        if (!$isDeveloper) {
            $devRoleIds = Role::where('slug', 'developer')->pluck('id')->toArray();
            $roleIds = array_values(array_diff($roleIds, $devRoleIds));

            // Si estamos editando y el contenido ya tenía el rol developer, lo conservamos
            if ($isUpdate) {
                $existingDevIds = $content->roles()->where('slug', 'developer')->pluck('roles.id')->toArray();
                $roleIds = array_values(array_unique(array_merge($roleIds, $existingDevIds)));
            }
        }

        $content->roles()->sync($roleIds);
        $content->update(['target_role_id' => $roleIds[0] ?? null]);
    }

    /**
     * Valida según el origen del contenido y tipo de destinatario.
     */
    private function validated(Request $request, ?CorporateContent $content = null): array
    {
        $rules = [
            'subcategory_id' => 'required|exists:document_subcategories,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,video',
            'source' => 'required|in:file,url',
            'required_time_seconds' => 'required|integer|min:0|max:86400',
            'target_type' => 'required|in:roles,user',
        ];

        if ($request->input('target_type') === 'user') {
            $rules['target_user_id'] = 'required|exists:users,id';
        } else {
            $rules['roles'] = 'required|array|min:1';
            $rules['roles.*'] = 'exists:roles,id';
        }

        if ($request->input('source') === 'url') {
            // Los enlaces externos solo aplican a videos (YouTube / Vimeo)
            $rules['type'] = 'required|in:video';
            $rules['url'] = [
                'required',
                'url',
                'max:500',
                'regex:/^https?:\/\/(www\.|m\.)?(youtube\.com|youtu\.be|youtube-nocookie\.com|vimeo\.com|player\.vimeo\.com)\//i',
            ];
        } else {
            $hasExistingFile = $content && !empty($content->file_path) && $request->input('type') === $content->type;
            $mimes = $request->input('type') === 'pdf' ? 'mimes:pdf' : 'mimes:mp4,webm,ogg,mov';
            $rules['file'] = [$hasExistingFile ? 'nullable' : 'required', 'file', $mimes, 'max:204800']; // 200 MB
        }

        return $request->validate($rules, [
            'url.regex' => 'El enlace debe pertenecer a YouTube o Vimeo.',
            'type.in' => 'Los enlaces externos solo pueden ser de tipo Video.',
            'roles.required' => 'Debe seleccionar al menos un rol destino.',
            'target_user_id.required' => 'Debe seleccionar el usuario específico.',
        ]);
    }

    /**
     * Construye los datos a persistir y procesa el archivo/enlace de forma condicional.
     */
    private function buildPayload(Request $request, array $data, ?CorporateContent $content = null): array
    {
        $isUserTarget = $data['target_type'] === 'user';

        $payload = [
            'subcategory_id' => $data['subcategory_id'],
            'title' => $data['title'],
            'type' => $data['type'],
            'required_time_seconds' => $data['required_time_seconds'],
            'target_user_id' => $isUserTarget ? $data['target_user_id'] : null,
            'target_role_id' => (!$isUserTarget && !empty($data['roles'])) ? $data['roles'][0] : null,
            'is_mandatory' => $request->boolean('is_mandatory'),
            'is_active' => $request->boolean('is_active'),
        ];

        if ($data['source'] === 'url') {
            // Enlace externo: guardar URL y eliminar archivo físico previo si existía
            $payload['url'] = $data['url'];
            $payload['file_path'] = null;
            if ($content) {
                $this->deleteStoredFile($content->file_path);
            }
        } else {
            // Archivo físico: almacenar en disco y limpiar la URL
            $payload['url'] = null;
            if ($request->hasFile('file')) {
                $payload['file_path'] = $request->file('file')->store(self::FOLDER . '/' . $data['type'], self::DISK);
                if ($content) {
                    $this->deleteStoredFile($content->file_path);
                }
            } elseif ($content) {
                $payload['file_path'] = $content->file_path;
            }
        }

        return $payload;
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
