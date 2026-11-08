{{ csrf_field() }}
<fieldset>
    <div class="form-group">
        <div class="col-md-10 mb-3">
            <label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Nombre de la categoría" value="{{ old('name', $category->name ?? '') }}" required maxlength="255">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-10 mb-3">
            <label class="form-label" for="description">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3" maxlength="1000" placeholder="Descripción">{{ old('description', $category->description ?? '') }}</textarea>
        </div>
    </div>
    <div class="form-actions">
        <a class="btn btn-danger" href="{{ route('induction.categories.index') }}">
            <i class="fa-solid fa-angle-left"></i> Atrás
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
        </button>
    </div>
</fieldset>

