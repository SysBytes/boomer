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
			<label class="form-label" for="email">Email <span class="text-danger">*</span></label>
			<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ $user->email ?? old('email') }}" required="required">
			{!! $errors->first('email', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="phone">Teléfono</label>
            <div class="input-group">
                <span class="input-group-text" id="phone_prefix"><i class="fa fa-phone"></i></span>
			    <input type="text" class="form-control" id="phone" name="phone" placeholder="Ej: 3001234567" value="{{ $user->phone ?? old('phone') }}">
            </div>
			{!! $errors->first('phone', '<span class=error>:message</span>') !!}
            <small class="text-muted">El código del país se guardará automáticamente según los países seleccionados.</small>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="countries">Países <span class="text-danger">*</span></label>
			<select class="js-select2 form-select" id="countries" name="countries[]" multiple="multiple" data-placeholder="Seleccione países" style="width: 100%;" required>
				<option></option>
				@foreach($countries as $country)
				<option value="{{ $country->id }}" data-code="{{ $country->phone_code }}"
					{{ (isset($user) && $user->countries->contains($country->id)) || (is_array(old('countries')) && in_array($country->id, old('countries'))) ? 'selected' : '' }}>
					{{ $country->name }} ({{ $country->phone_code }})
				</option>
				@endforeach
			</select>
			{!! $errors->first('countries', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="roles">Roles <span class="text-danger">*</span></label>
			<select class="js-select2 form-select" id="roles" name="roles[]" multiple="multiple" data-placeholder="Seleccione roles" style="width: 100%;" required>
				<option></option>
				@foreach($roles as $role)
				<option value="{{ $role->id }}" 
					{{ (isset($user) && $user->roles->contains($role->id)) || (is_array(old('roles')) && in_array($role->id, old('roles'))) ? 'selected' : '' }}>
					{{ $role->name }}
				</option>
				@endforeach
			</select>
			{!! $errors->first('roles', '<span class=error>:message</span>') !!}
		</div>
	</div>

    <div class="row">
        <div class="form-group col-md-5">
            <div class="col-md-10 mb-3">
                <label class="form-label" for="password">Contraseña @if(!isset($user))<span class="text-danger">*</span>@endif</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" {{ !isset($user) ? 'required' : '' }} style="padding-right: 40px;">
                    <span class="position-absolute d-flex align-items-center justify-content-center" 
                          style="top: 0; right: 0; height: 100%; width: 40px; cursor: pointer; z-index: 5;"
                          onmouseenter="document.getElementById('password').type = 'text'" 
                          onmouseleave="document.getElementById('password').type = 'password'">
                        <i class="fa fa-eye text-muted"></i>
                    </span>
                </div>
                {!! $errors->first('password', '<span class=error>:message</span>') !!}
                @if(isset($user))
                <small class="text-muted">Déjalo en blanco si no quieres cambiarla.</small>
                @endif
            </div>
        </div>
        <div class="form-group col-md-5">
            <div class="col-md-10 mb-3">
                <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmar" style="padding-right: 40px;">
                    <span class="position-absolute d-flex align-items-center justify-content-center" 
                          style="top: 0; right: 0; height: 100%; width: 40px; cursor: pointer; z-index: 5;"
                          onmouseenter="document.getElementById('password_confirmation').type = 'text'" 
                          onmouseleave="document.getElementById('password_confirmation').type = 'password'">
                        <i class="fa fa-eye text-muted"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<div class="form-check form-switch py-2">
				<input type="checkbox" class="form-check-input" id="active" name="active" {{ isset($user) && $user->active === 1 || old('active') === 1 ? 'checked="checked"' : '' }}>
				<label class="form-check-label" for="active">Activo</label>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<a class="btn btn-danger" href="{{route('user.index')}}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countriesSelect = document.getElementById('countries');
    const phonePrefixSpan = document.getElementById('phone_prefix');

    function updatePrefix() {
        if (!countriesSelect) return;
        const selectedOptions = Array.from(countriesSelect.selectedOptions);
        if (selectedOptions.length === 0) {
            phonePrefixSpan.innerHTML = '<i class="fa fa-phone"></i>';
            return;
        }

        let prefix = '';
        let hasColombia = selectedOptions.some(opt => opt.value === "1");
        let hasArgentina = selectedOptions.some(opt => opt.value === "2");

        if (hasColombia) {
            prefix = '+57';
        } else if (hasArgentina) {
            prefix = '+54';
        } else {
            prefix = selectedOptions[0].getAttribute('data-code');
        }

        phonePrefixSpan.innerText = prefix;
    }

    if (typeof jQuery !== 'undefined') {
        $('#countries').on('change', updatePrefix);
    } else {
        countriesSelect.addEventListener('change', updatePrefix);
    }
    
    setTimeout(updatePrefix, 200);
});
</script>
