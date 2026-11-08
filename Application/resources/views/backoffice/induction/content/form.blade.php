@php
    $hasOld = count(session()->getOldInput()) > 0;
    $source = old('source', isset($content) && $content->is_external ? 'url' : 'file');
    $isMandatory = $hasOld ? old('is_mandatory') !== null : ($content->is_mandatory ?? true);
    $isActive = $hasOld ? old('is_active') !== null : ($content->is_active ?? true);
    $defaultTargetType = isset($content) && $content->target_user_id ? 'user' : 'roles';
    $targetType = old('target_type', $defaultTargetType);
@endphp
{{ csrf_field() }}
<fieldset>
    <div class="form-group">
        <div class="col-md-10 mb-3">
            <label class="form-label" for="subcategory_id">Subcategoría <span class="text-danger">*</span></label>
            <select class="form-select" id="subcategory_id" name="subcategory_id" required>
                <option value="">Seleccione una subcategoría</option>
                @foreach($subcategories as $subcategory)
                    <option value="{{ $subcategory->id }}" {{ (string) old('subcategory_id', $content->subcategory_id ?? '') === (string) $subcategory->id ? 'selected' : '' }}>
                        {{ $subcategory->category->name ?? '' }} / {{ $subcategory->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 mb-3">
            <label class="form-label" for="title">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Título del contenido" value="{{ old('title', $content->title ?? '') }}" required maxlength="255">
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 mb-3">
            <label class="form-label" for="type">Tipo <span class="text-danger">*</span></label>
            <select class="form-select" id="type" name="type" required>
                <option value="pdf" {{ old('type', $content->type ?? 'pdf') === 'pdf' ? 'selected' : '' }}>PDF</option>
                <option value="video" {{ old('type', $content->type ?? '') === 'video' ? 'selected' : '' }}>Video</option>
            </select>
        </div>
        <div class="col-md-5 mb-3">
            <label class="form-label" for="required_time_seconds">Tiempo requerido (segundos) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="required_time_seconds" name="required_time_seconds" min="0" max="86400" value="{{ old('required_time_seconds', $content->required_time_seconds ?? 0) }}" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 mb-3">
            <label class="form-label d-block">Origen del contenido <span class="text-danger">*</span></label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="source" id="source_file" value="file" {{ $source === 'file' ? 'checked' : '' }}>
                <label class="form-check-label" for="source_file">Archivo físico</label>
            </div>
            <div class="form-check form-check-inline" id="source_url_wrapper">
                <input class="form-check-input" type="radio" name="source" id="source_url" value="url" {{ $source === 'url' ? 'checked' : '' }}>
                <label class="form-check-label" for="source_url">Enlace externo (YouTube / Vimeo)</label>
            </div>
        </div>
    </div>

    <div class="form-group" id="file_group">
        <div class="col-md-10 mb-3">
            <label class="form-label" for="file">Archivo <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="file" name="file">
            <div class="form-text">PDF o video (mp4, webm, ogg, mov). Máximo 200 MB (sujeto al límite de subida del servidor).</div>
            @if(isset($content) && $content->file_path)
                <div class="form-text">
                    Archivo actual:
                    <a href="{{ route('dashboard.corporate-contents.view', $content->id) }}" target="_blank">{{ basename($content->file_path) }}</a>
                    (déjalo vacío para conservarlo)
                </div>
            @endif
        </div>
    </div>

    <div class="form-group" id="url_group">
        <div class="col-md-10 mb-3">
            <label class="form-label" for="url">Enlace del video <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="url" name="url" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('url', $content->url ?? '') }}" maxlength="500">
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 mb-3">
            <label class="form-label d-block">Destinatarios <span class="text-danger">*</span></label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="target_type" id="target_type_roles" value="roles" {{ $targetType === 'roles' ? 'checked' : '' }}>
                <label class="form-check-label" for="target_type_roles">Por Roles</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="target_type" id="target_type_user" value="user" {{ $targetType === 'user' ? 'checked' : '' }}>
                <label class="form-check-label" for="target_type_user">Usuario Específico</label>
            </div>
        </div>
    </div>

    <div class="form-group" id="roles_group">
        <div class="col-md-10 mb-3">
            <label class="form-label" for="roles">Roles <span class="text-danger">*</span></label>
            <select class="js-select2 form-select" id="roles" name="roles[]" multiple="multiple" data-placeholder="Seleccione roles" style="width: 100%;">
                <option></option>
                @foreach($roles as $role)
                    @if($role->slug !== 'developer' || Auth::user()->hasRole('developer'))
                        <option value="{{ $role->id }}"
                            {{ (isset($content) && $content->roles->contains($role->id)) || (is_array(old('roles')) && in_array($role->id, old('roles'))) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endif
                @endforeach
            </select>
            {!! $errors->first('roles', '<span class=error>:message</span>') !!}
        </div>
    </div>

    <div class="form-group" id="target_user_group">
        <div class="col-md-10 mb-3">
            <label class="form-label" for="target_user_id">Usuario Específico <span class="text-danger">*</span></label>
            <select class="js-select2 form-select" id="target_user_id" name="target_user_id" data-placeholder="Seleccione un usuario" style="width: 100%;">
                <option value="">Seleccione un usuario</option>
                @foreach($users as $user)
                    @if(!$user->hasRole('developer') || Auth::user()->hasRole('developer'))
                        <option value="{{ $user->id }}"
                            {{ (string) old('target_user_id', $content->target_user_id ?? '') === (string) $user->id ? 'selected' : '' }}>
                            {{ ucwords(mb_strtolower($user->name)) }} ({{ $user->email }})
                        </option>
                    @endif
                @endforeach
            </select>
            {!! $errors->first('target_user_id', '<span class=error>:message</span>') !!}
            <div class="form-text text-muted">Este contenido solo podrá ser visualizado por el usuario seleccionado.</div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 mb-3">
            <div class="form-check form-switch py-2">
                <input type="checkbox" class="form-check-input" id="is_mandatory" name="is_mandatory" value="1" {{ $isMandatory ? 'checked' : '' }}>
                <label class="form-check-label" for="is_mandatory">Obligatorio (el usuario deberá verlo al iniciar sesión)</label>
            </div>
            <div class="form-check form-switch py-2">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ $isActive ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Activo</label>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a class="btn btn-danger" href="{{ route('induction.contents.index') }}">
            <i class="fa-solid fa-angle-left"></i> Atrás
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
        </button>
    </div>
</fieldset>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const sourceFile = document.getElementById('source_file');
        const sourceUrl = document.getElementById('source_url');
        const urlWrapper = document.getElementById('source_url_wrapper');
        const fileGroup = document.getElementById('file_group');
        const urlGroup = document.getElementById('url_group');
        const fileInput = document.getElementById('file');

        function refreshSource() {
            // Los enlaces externos solo aplican a video
            const isPdf = typeSelect.value === 'pdf';
            urlWrapper.style.display = isPdf ? 'none' : '';
            if (isPdf && sourceUrl.checked) {
                sourceFile.checked = true;
            }

            const useUrl = sourceUrl.checked;
            fileGroup.style.display = useUrl ? 'none' : '';
            urlGroup.style.display = useUrl ? '' : 'none';
            fileInput.accept = isPdf ? '.pdf' : '.mp4,.webm,.ogg,.mov';
        }

        typeSelect.addEventListener('change', refreshSource);
        sourceFile.addEventListener('change', refreshSource);
        sourceUrl.addEventListener('change', refreshSource);
        refreshSource();

        // Control de tipo de destinatario (Roles vs Usuario específico)
        const targetTypeRoles = document.getElementById('target_type_roles');
        const targetTypeUser = document.getElementById('target_type_user');
        const rolesGroup = document.getElementById('roles_group');
        const targetUserGroup = document.getElementById('target_user_group');

        function refreshTarget() {
            const isUser = targetTypeUser && targetTypeUser.checked;
            if (rolesGroup) rolesGroup.style.display = isUser ? 'none' : '';
            if (targetUserGroup) targetUserGroup.style.display = isUser ? '' : 'none';
        }

        if (targetTypeRoles && targetTypeUser) {
            targetTypeRoles.addEventListener('change', refreshTarget);
            targetTypeUser.addEventListener('change', refreshTarget);
            refreshTarget();
        }
    });
</script>
