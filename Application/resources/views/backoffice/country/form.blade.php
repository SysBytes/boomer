{{ csrf_field() }}
<fieldset>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="name">Nombre del País <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="name" name="name" placeholder="Ej: Colombia" value="{{ $country->name ?? old('name') }}" required="required">
			{!! $errors->first('name', '<span class=error>:message</span>') !!}
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="phone_code">Código Telefónico <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="phone_code" name="phone_code" placeholder="Ej: +57" value="{{ $country->phone_code ?? old('phone_code') }}" required="required">
			{!! $errors->first('phone_code', '<span class=error>:message</span>') !!}
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<div class="form-check form-switch py-2">
				<input type="checkbox" class="form-check-input" id="active" name="active" {{ isset($country) ? ($country->active ? 'checked' : '') : 'checked' }}>
				<label class="form-check-label" for="active">Activo</label>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<a class="btn btn-danger" href="{{route('country.index')}}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>
