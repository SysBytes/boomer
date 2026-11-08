{{ csrf_field() }}
<fieldset>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="material_category_name">Nombre <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="material_category_name" name="material_category_name" placeholder="Nombre" value="{{ $materialCategory->material_category_name ?? old('material_category_name') }}" required="required">
			{!! $errors->first('material_category_name', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="material_category_description">Descripción <span class="text-danger">*</span></label>
			<textarea class="form-control" id="material_category_description" name="material_category_description" rows="4" placeholder="Descripción"
				required>{{ $materialCategory->material_category_description ?? old('material_category_description') }}</textarea>
			{!! $errors->first('material_category_description', '<span class=error>:message</span>') !!}
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<div class="form-check form-switch py-2">
				<input type="checkbox" class="form-check-input" id="active" name="active" {{ isset($materialCategory) && $materialCategory->active === 1 || old('active') === 1 ? 'checked="checked"' : '' }}>
				<label class="form-check-label" for="active">Activo</label>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<a class="btn btn-danger" href="{{route('material_category.index')}}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>