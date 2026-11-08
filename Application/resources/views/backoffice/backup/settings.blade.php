@extends('layouts.backoffice')

@section('css_after')
<style>
    .settings-card {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        transition: box-shadow 0.2s ease;
    }
    .settings-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .settings-card .card-header {
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
        border-radius: 0.75rem 0.75rem 0 0;
    }
    .settings-card .card-body {
        padding: 1.25rem;
    }
    .settings-card .card-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
    }
    .setting-toggle {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    .setting-toggle.enabled {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
    }
    .setting-toggle.disabled {
        background: #fef2f2;
        border: 1px solid #fecaca;
    }
    .last-run-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.75em;
    }
    .form-label-sm {
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
    }
    .conditional-field {
        transition: opacity 0.2s, max-height 0.3s;
        overflow: hidden;
    }
    .conditional-field.hidden {
        opacity: 0;
        max-height: 0;
        margin: 0 !important;
        padding: 0 !important;
    }
    .conditional-field.visible {
        opacity: 1;
        max-height: 200px;
    }
    .retention-info {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="content-heading mb-0 pb-0">
                    <i class="fa fa-cog text-primary me-2"></i>Configuración de Respaldos
                </h2>
                <p class="text-muted fs-sm mt-1 mb-0">Configure la frecuencia y retención de los respaldos automáticos</p>
            </div>
            <a href="{{ route('backups.index') }}" class="btn btn-alt-secondary">
                <i class="fa fa-arrow-left me-1"></i> Volver a Respaldos
            </a>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <p class="mb-0"><i class="fa fa-check-circle me-1"></i> {{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('backups.update_settings') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Configuración de Archivos -->
                <div class="col-lg-6 mb-4">
                    <div class="settings-card">
                        <div class="card-header bg-light">
                            <h5>
                                <i class="fa fa-file-zipper text-info me-2"></i>Respaldo de Archivos
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Toggle Habilitado -->
                            <div class="setting-toggle {{ $filesSetting && $filesSetting->enabled ? 'enabled' : 'disabled' }}" id="files-toggle-box">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="files_enabled" name="files_enabled" value="1"
                                           {{ $filesSetting && $filesSetting->enabled ? 'checked' : '' }}
                                           onchange="toggleSettingBox('files')">
                                    <label class="form-check-label fw-bold" for="files_enabled">
                                        {{ $filesSetting && $filesSetting->enabled ? 'Habilitado' : 'Deshabilitado' }}
                                    </label>
                                </div>
                                @if($filesSetting && $filesSetting->last_run_at)
                                    <span class="badge bg-info last-run-badge ms-auto">
                                        <i class="fa fa-clock me-1"></i>Último: {{ $filesSetting->last_run_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Frecuencia -->
                            <div class="mb-3">
                                <label class="form-label form-label-sm" for="files_frequency">Frecuencia</label>
                                <select class="form-select" id="files_frequency" name="files_frequency" onchange="updateConditionalFields('files')">
                                    @foreach($frequencies as $value => $label)
                                        <option value="{{ $value }}" {{ $filesSetting && $filesSetting->frequency === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Hora -->
                            <div class="mb-3">
                                <label class="form-label form-label-sm" for="files_time">Hora de ejecución</label>
                                <input type="time" class="form-control" id="files_time" name="files_time"
                                       value="{{ $filesSetting ? $filesSetting->time : '02:00' }}">
                            </div>

                            <!-- Día de la semana (condicional: weekly) -->
                            <div class="mb-3 conditional-field {{ $filesSetting && $filesSetting->frequency === 'weekly' ? 'visible' : 'hidden' }}" id="files_dow_container">
                                <label class="form-label form-label-sm" for="files_day_of_week">Día de la semana</label>
                                <select class="form-select" id="files_day_of_week" name="files_day_of_week">
                                    @foreach($daysOfWeek as $value => $label)
                                        <option value="{{ $value }}" {{ $filesSetting && $filesSetting->day_of_week === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Día del mes (condicional: monthly) -->
                            <div class="mb-3 conditional-field {{ $filesSetting && $filesSetting->frequency === 'monthly' ? 'visible' : 'hidden' }}" id="files_dom_container">
                                <label class="form-label form-label-sm" for="files_day_of_month">Día del mes</label>
                                <select class="form-select" id="files_day_of_month" name="files_day_of_month">
                                    @for($i = 1; $i <= 28; $i++)
                                        <option value="{{ $i }}" {{ $filesSetting && $filesSetting->day_of_month === $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Retención -->
                            <div class="mb-0">
                                <label class="form-label form-label-sm" for="files_retention_days">Días de retención</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="files_retention_days" name="files_retention_days"
                                           value="{{ $filesSetting ? $filesSetting->retention_days : 30 }}"
                                           min="1" max="365" required>
                                    <span class="input-group-text">días</span>
                                </div>
                                <div class="retention-info">
                                    Los respaldos más antiguos que este período serán eliminados automáticamente.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Base de Datos -->
                <div class="col-lg-6 mb-4">
                    <div class="settings-card">
                        <div class="card-header bg-light">
                            <h5>
                                <i class="fa fa-database text-success me-2"></i>Respaldo de Base de Datos
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Toggle Habilitado -->
                            <div class="setting-toggle {{ $dbSetting && $dbSetting->enabled ? 'enabled' : 'disabled' }}" id="database-toggle-box">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="database_enabled" name="database_enabled" value="1"
                                           {{ $dbSetting && $dbSetting->enabled ? 'checked' : '' }}
                                           onchange="toggleSettingBox('database')">
                                    <label class="form-check-label fw-bold" for="database_enabled">
                                        {{ $dbSetting && $dbSetting->enabled ? 'Habilitado' : 'Deshabilitado' }}
                                    </label>
                                </div>
                                @if($dbSetting && $dbSetting->last_run_at)
                                    <span class="badge bg-success last-run-badge ms-auto">
                                        <i class="fa fa-clock me-1"></i>Último: {{ $dbSetting->last_run_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Frecuencia -->
                            <div class="mb-3">
                                <label class="form-label form-label-sm" for="database_frequency">Frecuencia</label>
                                <select class="form-select" id="database_frequency" name="database_frequency" onchange="updateConditionalFields('database')">
                                    @foreach($frequencies as $value => $label)
                                        <option value="{{ $value }}" {{ $dbSetting && $dbSetting->frequency === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Hora -->
                            <div class="mb-3">
                                <label class="form-label form-label-sm" for="database_time">Hora de ejecución</label>
                                <input type="time" class="form-control" id="database_time" name="database_time"
                                       value="{{ $dbSetting ? $dbSetting->time : '02:00' }}">
                            </div>

                            <!-- Día de la semana (condicional: weekly) -->
                            <div class="mb-3 conditional-field {{ $dbSetting && $dbSetting->frequency === 'weekly' ? 'visible' : 'hidden' }}" id="database_dow_container">
                                <label class="form-label form-label-sm" for="database_day_of_week">Día de la semana</label>
                                <select class="form-select" id="database_day_of_week" name="database_day_of_week">
                                    @foreach($daysOfWeek as $value => $label)
                                        <option value="{{ $value }}" {{ $dbSetting && $dbSetting->day_of_week === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Día del mes (condicional: monthly) -->
                            <div class="mb-3 conditional-field {{ $dbSetting && $dbSetting->frequency === 'monthly' ? 'visible' : 'hidden' }}" id="database_dom_container">
                                <label class="form-label form-label-sm" for="database_day_of_month">Día del mes</label>
                                <select class="form-select" id="database_day_of_month" name="database_day_of_month">
                                    @for($i = 1; $i <= 28; $i++)
                                        <option value="{{ $i }}" {{ $dbSetting && $dbSetting->day_of_month === $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Retención -->
                            <div class="mb-0">
                                <label class="form-label form-label-sm" for="database_retention_days">Días de retención</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="database_retention_days" name="database_retention_days"
                                           value="{{ $dbSetting ? $dbSetting->retention_days : 30 }}"
                                           min="1" max="365" required>
                                    <span class="input-group-text">días</span>
                                </div>
                                <div class="retention-info">
                                    Los respaldos más antiguos que este período serán eliminados automáticamente.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guardar -->
            <div class="row">
                <div class="col-12">
                    <div class="block block-rounded">
                        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                            <div class="text-muted fs-sm">
                                <i class="fa fa-info-circle me-1"></i>
                                Los respaldos automáticos requieren que el <strong>scheduler de Laravel</strong> esté configurado en el servidor
                                (<code>crontab</code>).
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-1"></i> Guardar Configuración
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- END Page Content -->
@endsection

@section('js_after')
<script>
    function toggleSettingBox(type) {
        var checkbox = document.getElementById(type + '_enabled');
        var box = document.getElementById(type + '-toggle-box');
        var label = checkbox.nextElementSibling;

        if (checkbox.checked) {
            box.classList.remove('disabled');
            box.classList.add('enabled');
            label.textContent = 'Habilitado';
        } else {
            box.classList.remove('enabled');
            box.classList.add('disabled');
            label.textContent = 'Deshabilitado';
        }
    }

    function updateConditionalFields(type) {
        var frequency = document.getElementById(type + '_frequency').value;
        var dowContainer = document.getElementById(type + '_dow_container');
        var domContainer = document.getElementById(type + '_dom_container');

        // Ocultar todos
        dowContainer.classList.remove('visible');
        dowContainer.classList.add('hidden');
        domContainer.classList.remove('visible');
        domContainer.classList.add('hidden');

        // Mostrar el relevante
        if (frequency === 'weekly') {
            dowContainer.classList.remove('hidden');
            dowContainer.classList.add('visible');
        } else if (frequency === 'monthly') {
            domContainer.classList.remove('hidden');
            domContainer.classList.add('visible');
        }
    }

    // Inicializar campos condicionales al cargar
    document.addEventListener('DOMContentLoaded', function() {
        updateConditionalFields('files');
        updateConditionalFields('database');
    });
</script>
@endsection
