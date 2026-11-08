@csrf
<div class="mb-4">
    <label class="form-label" for="holiday_name">Nombre del Feriado <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="holiday_name" name="holiday_name" value="{{ isset($holiday) ? $holiday->holiday_name : old('holiday_name') }}" required>
    @error('holiday_name')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="country_id">País <span class="text-danger">*</span></label>
    <select class="form-select" id="country_id" name="country_id" required>
        <option value="">Seleccione un país</option>
        @foreach($countries as $country)
        <option value="{{ $country->id }}" {{ (isset($holiday) && $holiday->country_id == $country->id) || old('country_id') == $country->id ? 'selected' : '' }}>
            {{ $country->name }}
        </option>
        @endforeach
    </select>
    @error('country_id')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label" for="holiday_date">Fecha <span class="text-danger">*</span></label>
    <input type="date" class="form-control" id="holiday_date" name="holiday_date" value="{{ isset($holiday) ? \Carbon\Carbon::parse($holiday->holiday_date)->format('Y-m-d') : old('holiday_date') }}" required>
    @error('holiday_date')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_annual" name="is_annual" {{ isset($holiday) ? ($holiday->is_annual ? 'checked' : '') : 'checked' }}>
        <label class="form-check-label" for="is_annual">Se repite anualmente</label>
    </div>
</div>

<div class="mb-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="active" name="active" {{ isset($holiday) ? ($holiday->active ? 'checked' : '') : 'checked' }}>
        <label class="form-check-label" for="active">Estatus activo</label>
    </div>
</div>

<div class="form-actions mt-4">
    <a class="btn btn-danger" href="{{route('holiday.index')}}">
        <i class="fa-solid fa-angle-left"></i> Atrás
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> Guardar
    </button>
</div>
