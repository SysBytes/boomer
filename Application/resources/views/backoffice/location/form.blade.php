{{ csrf_field() }}
<fieldset>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="client_id">Cliente <span class="text-danger">*</span></label>
			<select class="form-select" id="client_id" name="client_id" required>
				<option value="">Seleccione un cliente</option>
				@foreach($clients as $client)
					<option value="{{ $client->client_id }}" {{ (isset($location) && $location->client_id == $client->client_id) || old('client_id') == $client->client_id ? 'selected' : '' }}>
						{{ $client->client_name }}
					</option>
				@endforeach
			</select>
			{!! $errors->first('client_id', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="location_name">Nombre de la locación <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="location_name" name="location_name" placeholder="Nombre" value="{{ $location->location_name ?? old('location_name') }}" required="required">
			{!! $errors->first('location_name', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="location_description">Descripción</label>
			<textarea class="form-control" id="location_description" name="location_description" rows="3" placeholder="Descripción">{{ $location->location_description ?? old('location_description') }}</textarea>
			{!! $errors->first('location_description', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="location_city">Ciudad</label>
			<input type="text" class="form-control" id="location_city" name="location_city" placeholder="Ciudad" value="{{ $location->location_city ?? old('location_city') }}">
			{!! $errors->first('location_city', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="location_address">Dirección <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="location_address" name="location_address" placeholder="Escribe para buscar la dirección..." value="{{ $location->location_address ?? old('location_address') }}" required autocomplete="off">
			{!! $errors->first('location_address', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="location_reference">Referencia</label>
			<input type="text" class="form-control" id="location_reference" name="location_reference" placeholder="Referencia" value="{{ $location->location_reference ?? old('location_reference') }}">
			{!! $errors->first('location_reference', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-md-5 mb-3">
				<label class="form-label" for="location_lat">Latitud</label>
				<input type="text" class="form-control" id="location_lat" name="location_lat" placeholder="Latitud" value="{{ $location->location_lat ?? old('location_lat') }}">
				{!! $errors->first('location_lat', '<span class=error>:message</span>') !!}
			</div>
			<div class="col-md-5 mb-3">
				<label class="form-label" for="location_lng">Longitud</label>
				<input type="text" class="form-control" id="location_lng" name="location_lng" placeholder="Longitud" value="{{ $location->location_lng ?? old('location_lng') }}">
				{!! $errors->first('location_lng', '<span class=error>:message</span>') !!}
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<div class="form-check form-switch py-2">
				<input type="checkbox" class="form-check-input" id="active" name="active" {{ isset($location) && $location->active === 1 || old('active') === 1 ? 'checked="checked"' : '' }}>
				<label class="form-check-label" for="active">Activo</label>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<a class="btn btn-danger" href="{{route('location.index')}}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		var input = document.getElementById('location_address');
		if (input) {
			var autocomplete = new google.maps.places.Autocomplete(input);

			// OPTIMIZACIÓN DE COSTOS: Solo solicitar la geometría (lat/lng) para no facturar datos extra de Places
			autocomplete.setFields(['geometry']);

			autocomplete.addListener('place_changed', function () {
				var place = autocomplete.getPlace();

				if (!place.geometry) {
					return;
				}

				// Actualizar latitud y longitud
				var latInput = document.getElementById('location_lat');
				var lngInput = document.getElementById('location_lng');

				if (latInput && lngInput) {
					latInput.value = place.geometry.location.lat();
					lngInput.value = place.geometry.location.lng();
				}
			});

			// Prevenir que al presionar 'Enter' se envíe el formulario
			input.addEventListener('keydown', function (e) {
				if (e.key === 'Enter') {
					e.preventDefault();
				}
			});
		}
	});
</script>
