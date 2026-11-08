{{ csrf_field() }}
<fieldset>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="name" name="name" placeholder="Nombre" value="{{ $user->name ?? old('name') }}" required="required">
			{!! $errors->first('name', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="email">Email</label>
			<input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled>
			<div class="form-text text-muted">El correo electrónico no puede ser modificado.</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="password">Contraseña {{ isset($user) ? '(Déjalo en blanco si no deseas cambiarla)' : '*' }}</label>
			<input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
			{!! $errors->first('password', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
			<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmar Contraseña">
		</div>
	</div>

	<div class="form-actions">
		<a class="btn btn-danger" href="{{ route('backoffice') }}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>