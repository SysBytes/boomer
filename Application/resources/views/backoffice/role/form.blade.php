{{ csrf_field() }}
<fieldset>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="name" name="name" placeholder="Nombre del Rol" value="{{ $role->name ?? old('name') }}" required="required">
			{!! $errors->first('name', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="slug">Slug (Identificador) <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="slug" name="slug" placeholder="Ej: developer, admin, supervisor" value="{{ $role->slug ?? old('slug') }}" required="required">
			{!! $errors->first('slug', '<span class=error>:message</span>') !!}
		</div>
	</div>

	<div class="form-actions">
		<a class="btn btn-danger" href="{{route('role.index')}}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>
