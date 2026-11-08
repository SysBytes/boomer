{{ csrf_field() }}
<fieldset>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="location_id">Locación <span class="text-danger">*</span></label>
			<select class="js-select2 form-select" id="location_id" name="location_id" style="width: 100%;" data-placeholder="Seleccione una locación" required>
				<option></option>
				@foreach($locations as $loc)
					<option value="{{ $loc->location_id }}" {{ (isset($work) && $work->location_id == $loc->location_id) || old('location_id') == $loc->location_id ? 'selected' : '' }}>
						{{ $loc->location_name }} ({{ $loc->client->client_name ?? 'Sin Cliente' }})
					</option>
				@endforeach
			</select>
			{!! $errors->first('location_id', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="work_name">Nombre de la obra <span class="text-danger">*</span></label>
			<input type="text" class="form-control" id="work_name" name="work_name" placeholder="Nombre" value="{{ $work->work_name ?? old('work_name') }}" required="required">
			{!! $errors->first('work_name', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="work_description">Descripción <span class="text-danger">*</span></label>
			<textarea class="form-control" id="work_description" name="work_description" rows="3" placeholder="Descripción">{{ $work->work_description ?? old('work_description') }}</textarea>
			{!! $errors->first('work_description', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="supervisor_id">Supervisor <span class="text-danger">*</span></label>
			<select class="form-select" id="supervisor_id" name="supervisor_id" required>
				<option value="">Seleccione un supervisor</option>
				@foreach($supervisors as $supervisor)
					<option value="{{ $supervisor->id }}" {{ (isset($work) && $work->supervisor_id == $supervisor->id) || old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
						{{ $supervisor->name }}
					</option>
				@endforeach
			</select>
			{!! $errors->first('supervisor_id', '<span class=error>:message</span>') !!}
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-md-5 mb-3">
				<label class="form-label" for="start_date">Fecha Inicio</label>
				<input type="text" class="js-flatpickr form-control bg-white" id="start_date" name="start_date" placeholder="Y-m-d" value="{{ isset($work) && $work->start_date ? $work->start_date->format('Y-m-d') : old('start_date') }}">
				{!! $errors->first('start_date', '<span class=error>:message</span>') !!}
			</div>
			<div class="col-md-5 mb-3">
				<label class="form-label" for="end_date">Fecha Fin</label>
				<input type="text" class="js-flatpickr form-control bg-white" id="end_date" name="end_date" placeholder="Y-m-d" value="{{ isset($work) && $work->end_date ? $work->end_date->format('Y-m-d') : old('end_date') }}">
				{!! $errors->first('end_date', '<span class=error>:message</span>') !!}
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-10 mb-3">
			<label class="form-label" for="work_status">Estado de la Obra <span class="text-danger">*</span></label>
			<select class="form-select" id="work_status" name="work_status" required>
				<option value="pending" {{ (isset($work) && $work->work_status == 'pending') || old('work_status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
				<option value="in_progress" {{ (isset($work) && $work->work_status == 'in_progress') || old('work_status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
				<option value="completed" {{ (isset($work) && $work->work_status == 'completed') || old('work_status') == 'completed' ? 'selected' : '' }}>Finalizada</option>
			</select>
			{!! $errors->first('work_status', '<span class=error>:message</span>') !!}
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-10 mb-3">
			<div class="form-check form-switch py-2">
				<input type="checkbox" class="form-check-input" id="active" name="active" {{ isset($work) && $work->active === 1 || old('active') === 1 ? 'checked="checked"' : '' }}>
				<label class="form-check-label" for="active">Activo</label>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<a class="btn btn-danger" href="{{route('work.index')}}">
			<i class="fa-solid fa-angle-left"></i> Atrás
		</a>
		<button type="submit" class="btn btn-primary">
			<i class="fa-solid fa-floppy-disk"></i> Guardar
		</button>
	</div>
</fieldset>