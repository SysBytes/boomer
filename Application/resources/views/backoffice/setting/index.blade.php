@extends('layouts.backoffice')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        Codebase.helpersOnLoad(['jq-select2']);
    </script>
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <h2 class="content-heading">Panel de Configuración</h2>

        <div class="row">
            <div class="col-md-8 col-lg-6">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Ajustes Generales del Sistema</h3>
                    </div>
                    <div class="block-content">
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

                        <form action="{{ route('setting.update') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label" for="WEEKLY_WORK_HOURS">Horas de trabajo semanales</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="WEEKLY_WORK_HOURS" name="WEEKLY_WORK_HOURS" value="{{ old('WEEKLY_WORK_HOURS', $weeklyWorkHours) }}" placeholder="Ej: 44" required min="1" max="168">
                                    <span class="input-group-text">horas</span>
                                </div>
                                <div class="form-text text-muted">
                                    Este valor se utilizará como base para cálculos de horas extras o reportes de asistencia en el sistema.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="DEFAULT_STAFF_PROJECT">Obra por defecto (Administrativos)</label>
                                <select class="js-select2 form-select" id="DEFAULT_STAFF_PROJECT" name="DEFAULT_STAFF_PROJECT" style="width: 100%;" data-placeholder="Seleccione un proyecto">
                                    <option></option>
                                    @foreach($works as $work)
                                        <option value="{{ $work->work_id }}" {{ old('DEFAULT_STAFF_PROJECT', $defaultStaffProject) == $work->work_id ? 'selected' : '' }}>
                                            {{ $work->work_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted">
                                    Este proyecto se asignará por defecto a los usuarios con rol administrativo.
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="WHATSAPP_NOTIFICATIONS_ENABLED" name="WHATSAPP_NOTIFICATIONS_ENABLED" {{ (old('WHATSAPP_NOTIFICATIONS_ENABLED', $whatsappEnabled) === 'true' || old('WHATSAPP_NOTIFICATIONS_ENABLED', $whatsappEnabled) === true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="WHATSAPP_NOTIFICATIONS_ENABLED">Habilitar notificaciones (WhatsApp o SMS)</label>
                                </div>
                                <div class="form-text text-muted">
                                    Si se desactiva, el sistema no enviará las alertas automáticas de Check-Outs pendientes.
                                </div>
                            </div>

                            <div class="mb-4" id="channel_selection_container" style="display: none;">
                                <label class="form-label" for="NOTIFICATION_METHOD">Canal de Notificaciones</label>
                                <select class="form-select" id="NOTIFICATION_METHOD" name="NOTIFICATION_METHOD">
                                    <option value="whatsapp" {{ old('NOTIFICATION_METHOD', $notificationMethod) === 'whatsapp' ? 'selected' : '' }}>WhatsApp (Meta API)</option>
                                    <option value="sms" {{ old('NOTIFICATION_METHOD', $notificationMethod) === 'sms' ? 'selected' : '' }}>SMS (LabsMobile)</option>
                                </select>
                                <div class="form-text text-muted">
                                    Selecciona el canal que deseas utilizar para enviar los recordatorios de Check-Out.
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const switchInput = document.getElementById('WHATSAPP_NOTIFICATIONS_ENABLED');
                                    const channelContainer = document.getElementById('channel_selection_container');

                                    function toggleChannelSelection() {
                                        if (switchInput.checked) {
                                            channelContainer.style.display = 'block';
                                        } else {
                                            channelContainer.style.display = 'none';
                                        }
                                    }

                                    // Check on load
                                    toggleChannelSelection();

                                    // Check on change
                                    switchInput.addEventListener('change', toggleChannelSelection);
                                });
                            </script>

                            <div class="mb-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection