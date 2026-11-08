{{ csrf_field() }}
<fieldset>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="material_category_id">Categoría <span class="text-danger">*</span></label>
			<select class="form-select" id="material_category_id" name="material_category_id" required>
				<option value="">Seleccione una categoría</option>
				@foreach($categories as $category)
					<option value="{{ $category->material_category_id }}" {{ (isset($material) && $material->material_category_id == $category->material_category_id) || old('material_category_id') == $category->material_category_id ? 'selected' : '' }}>
						{{ $category->material_category_name }}
					</option>
				@endforeach
			</select>
			{!! $errors->first('material_category_id', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="material_name">Nombre <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="material_name" name="material_name" placeholder="Nombre" value="{{ $material->material_name ?? old('material_name') }}" required="required">
			{!! $errors->first('material_name', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="material_description">Descripción</label>
			<textarea class="form-control" id="material_description" name="material_description" rows="4" placeholder="Descripción">{{ $material->material_description ?? old('material_description') }}</textarea>
			{!! $errors->first('material_description', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="material_unit">Unidad Métrica</label>
			<input type="text" class="form-control" id="material_unit" name="material_unit" placeholder="Ej: Unidad, Metro, Kg, Litro..." value="{{ $material->material_unit ?? old('material_unit') }}">
			{!! $errors->first('material_unit', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="stock">Stock <span class="text-danger">*</span></label>
			<input type="number" class="form-control" id="stock" name="stock" placeholder="Stock" value="{{ $material->stock ?? old('stock') }}" required="required">
			{!! $errors->first('stock', '<span class=error>:message</span>') !!}
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<div class="form-check form-switch py-2">
				<input type="checkbox" class="form-check-input" id="active" name="active" {{ isset($material) && $material->active === 1 || old('active') === 1 ? 'checked="checked"' : '' }}>
				<label class="form-check-label" for="active">Activo</label>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<a class="btn btn-danger" href="{{route('material.index')}}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>