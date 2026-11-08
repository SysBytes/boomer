{{ csrf_field() }}
<fieldset>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="client_name">Nombre <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="client_name" name="client_name" placeholder="Nombre de cliente" value="{{ $client->client_name ?? old('client_name') }}" required="required">
			{!! $errors->first('client_name', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="client_email">Email <span class="text-danger">*</span></label>
			<input type="email" class="form-control" id="client_email" name="client_email" placeholder="Email" value="{{ $client->client_email ?? old('client_email') }}" required="required">
			{!! $errors->first('client_email', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="client_phone">Teléfono</label>
			<input type="text" class="form-control" id="client_phone" name="client_phone" placeholder="Teléfono" value="{{ $client->client_phone ?? old('client_phone') }}">
			{!! $errors->first('client_phone', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="country_id">País <span class="text-danger">*</span></label>
			<select class="form-select" id="country_id" name="country_id" required>
				<option value="">Seleccione un país</option>
				@foreach($countries as $country)
				<option value="{{ $country->id }}" {{ (isset($client) && $client->country_id == $country->id) || old('country_id') == $country->id ? 'selected' : '' }}>
					{{ $country->name }}
				</option>
				@endforeach
			</select>
			{!! $errors->first('country_id', '<span class=error>:message</span>') !!}
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<div class="form-check form-switch py-2">
				<input type="checkbox" class="form-check-input" id="active" name="active" {{ isset($client) && $client->active === 1 || old('active') === 1 ? 'checked="checked"' : '' }}>
				<label class="form-check-label" for="active">Activo</label>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<a class="btn btn-danger" href="{{route('client.index')}}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>
