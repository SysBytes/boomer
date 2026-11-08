@extends('layouts.backoffice')

@section('title', 'Registro Manual de Horas')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        Codebase.helpersOnLoad(['jq-select2']);

        var defaultStaffWork = {!! $defaultStaffWork ? json_encode($defaultStaffWork) : 'null' !!};

        $(document).ready(function () {
            if ($('.js-select2').length) {
                $('.js-select2').select2({
                    width: '100%'
                });
            }

            $('#user_id').on('change', function () {
                var userId = $(this).val();
                var workSelect = $('#work_id');
                workSelect.empty().append('<option></option>');

                $('#active_log_alert').addClass('d-none').html('');
                $('#staff_config_alert').addClass('d-none');
                $('button[type="submit"]').prop('disabled', false);

                if (userId) {
                    var selectedOption = $(this).find('option:selected');
                    var role = selectedOption.data('role');
                    var works = selectedOption.data('works');
                    var activeLog = selectedOption.data('activelog');

                    if (typeof works === 'string') {
                        try { works = JSON.parse(works); } catch (e) { works = []; }
                    }
                    if (typeof activeLog === 'string' && activeLog.trim() !== '') {
                        try { activeLog = JSON.parse(activeLog); } catch (e) { activeLog = null; }
                    }

                    if (role === 'staff') {
                        if (defaultStaffWork) {
                            workSelect.append('<option value="' + defaultStaffWork.work_id + '" selected>' + (defaultStaffWork.location ? defaultStaffWork.location.location_name : 'N/A') + ' - ' + defaultStaffWork.work_name + '</option>');
                        } else {
                            $('#staff_config_alert').removeClass('d-none');
                            $('button[type="submit"]').prop('disabled', true);
                        }
                    } else if (works && Array.isArray(works)) {
                        // Ordenar alfabéticamente por Locación - Obra
                        works.sort(function(a, b) {
                            var aName = ((a.location ? a.location.location_name : 'N/A') + ' - ' + a.work_name).toUpperCase();
                            var bName = ((b.location ? b.location.location_name : 'N/A') + ' - ' + b.work_name).toUpperCase();
                            return aName.localeCompare(bName);
                        });
                        
                        works.forEach(function (work) {
                            var displayName = (work.location ? work.location.location_name : 'N/A') + ' - ' + work.work_name;
                            // Convertir a mayúsculas para mantener consistencia con los reportes
                            displayName = displayName.toUpperCase();
                            workSelect.append('<option value="' + work.work_id + '">' + displayName + '</option>');
                        });
                    }

                    if (activeLog && activeLog.time_log_id) {
                        $('#action_input').val('checkout');
                        $('#action_display').removeClass('bg-success').addClass('bg-danger').html('<i class="fas fa-sign-out-alt me-1"></i> Check-Out');

                        // Format date for display and input min
                        // Handle both ISO strings and YYYY-MM-DD HH:MM:SS
                        var checkInStr = activeLog.check_in_time;
                        var displayDate = checkInStr;
                        var minDateStr = '';

                        if (checkInStr) {
                            var dateObj = new Date(checkInStr);
                            if (!isNaN(dateObj.getTime())) {
                                var dd = ('0' + dateObj.getDate()).slice(-2);
                                var mm = ('0' + (dateObj.getMonth() + 1)).slice(-2);
                                var yyyy = dateObj.getFullYear();
                                var hours = dateObj.getHours();
                                var hh = ('0' + hours).slice(-2);
                                var min = ('0' + dateObj.getMinutes()).slice(-2);
                                var ss = ('0' + dateObj.getSeconds()).slice(-2);

                                // YYYY-MM-DDTHH:MM (must be 24h for input min attribute)
                                minDateStr = yyyy + '-' + mm + '-' + dd + 'T' + hh + ':' + min;

                                // AM/PM conversion
                                var ampm = hours >= 12 ? 'PM' : 'AM';
                                var hours12 = hours % 12;
                                hours12 = hours12 ? hours12 : 12; // 0 becomes 12
                                var hh12 = ('0' + hours12).slice(-2);

                                // DD/MM/YYYY HH:MM:SS AM/PM
                                displayDate = dd + '/' + mm + '/' + yyyy + ' ' + hh12 + ':' + min + ':' + ss + ' ' + ampm;
                            } else {
                                // Fallback
                                minDateStr = checkInStr.replace(' ', 'T').substring(0, 16);
                            }
                        }

                        $('#record_time').attr('min', minDateStr);

                        var isManual = activeLog.check_in_registered_by ? 'Manual (por administrador)' : 'App (por el usuario)';
                        var projectName = activeLog.work ? ((activeLog.work.location ? activeLog.work.location.location_name : 'N/A') + ' - ' + activeLog.work.work_name) : 'Desconocido';

                        var alertHtml = '<i class="fa fa-info-circle me-1"></i> Este usuario tiene un Check-In abierto:<br>' +
                            '<ul class="mb-2 mt-2">' +
                            '<li><strong>Obra:</strong> ' + projectName + '</li>' +
                            '<li><strong>Fecha/Hora Check-In:</strong> ' + displayDate + '</li>' +
                            '<li><strong>Tipo de Registro:</strong> ' + isManual + '</li>' +
                            '</ul>' +
                            '<em>Solo es posible realizar el Check-Out. La fecha/hora seleccionada debe ser posterior al Check-In.</em>';

                        $('#active_log_alert').removeClass('d-none').html(alertHtml);

                        // Select the project
                        workSelect.val(activeLog.work_id);
                    } else {
                        $('#action_input').val('checkin');
                        $('#action_display').removeClass('bg-danger').addClass('bg-success').html('<i class="fas fa-sign-in-alt me-1"></i> Check-In');
                        $('#record_time').removeAttr('min');
                    }
                }

                if ($('#work_id').hasClass('select2-hidden-accessible')) {
                    $('#work_id').trigger('change.select2');
                }
            });

            $('form').on('submit', function (e) {
                if ($('#action_input').val() === 'checkout') {
                    var minVal = $('#record_time').attr('min');
                    var currVal = $('#record_time').val();
                    if (minVal && currVal && currVal < minVal) {
                        e.preventDefault();
                        alert('Error: La fecha y hora de Check-Out no puede ser anterior a la de Check-In.');
                    }
                }
            });
        });
    </script>
@endsection

@section('content')
    <div class="content">
        <h2 class="content-heading">Registro Manual de Check-In / Check-Out</h2>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title"><i class="fas fa-clock me-1"></i> Formulario de Registro</h3>
            </div>

            <div class="block-content block-content-full">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <p class="mb-0"><i class="fa fa-check-circle me-1"></i> {{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <p class="mb-0"><i class="fa fa-times-circle me-1"></i> {{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="active_log_alert" class="alert alert-info d-none" role="alert"></div>
                <div id="staff_config_alert" class="alert alert-warning d-none" role="alert">
                    <i class="fa fa-exclamation-triangle me-1"></i> El parámetro <strong>DEFAULT_STAFF_PROJECT</strong> no está configurado o es inválido en el archivo <code>.env</code>. <br>
                    <small>Por favor, configúrelo en <strong><a href="{{ route('setting.index') }}">Configuración</a></strong> para poder registrar horas al personal administrativo.</small>
                </div>

                <form action="{{ route('timelogs.manual.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="user_id">Usuario <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="js-select2 form-select" style="width: 100%;" data-placeholder="Seleccione un usuario..." required>
                                <option></option>
                                @foreach($users as $user)
                                    @php
                                        $activeLog = $user->timeLogs->first();

                                        $roleSlug = 'technician';
                                        $roleName = 'Técnico';

                                        if ($user->hasRole('staff')) {
                                            $roleSlug = 'staff';
                                            $roleName = 'Administrativo';
                                        } elseif ($user->hasRole('supervisor')) {
                                            $roleSlug = 'supervisor';
                                            $roleName = 'Supervisor';
                                        } elseif ($user->hasRole('assistant')) {
                                            $roleSlug = 'assistant';
                                            $roleName = 'Auxiliar';
                                        } elseif ($user->roles->count() > 0) {
                                            $roleSlug = $user->roles->first()->slug;
                                            $roleName = $user->primary_role_name;
                                        }

                                        if ($roleSlug === 'supervisor') {
                                            $worksJson = json_encode($user->supervisedWorks);
                                        } elseif ($roleSlug === 'staff') {
                                            $worksJson = '[]';
                                        } else {
                                            $worksJson = json_encode($user->works);
                                        }
                                    @endphp
                                    <option value="{{ $user->id }}" data-role="{{ $roleSlug }}" data-works='{{ $worksJson }}' data-activelog='{{ $activeLog ? json_encode($activeLog) : "" }}'>
                                        {{ mb_strtoupper($user->name) }} - {{ $roleName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="work_id">Obra <span class="text-danger">*</span></label>
                            <select name="work_id" id="work_id" class="js-select2 form-select" style="width: 100%;" data-placeholder="Seleccione un proyecto..." required>
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Proceso a Registrar</label>
                            <div class="pt-2">
                                <input type="hidden" id="action_input" name="action" value="checkin">
                                <span id="action_display" class="badge bg-success fs-base p-2"><i class="fas fa-sign-in-alt me-1"></i> Check-In</span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="record_time">Fecha y Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="record_time" id="record_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="form-label" for="observation">Observación <span class="text-danger">*</span></label>
                            <textarea name="observation" id="observation" rows="3" class="form-control" placeholder="Indique la justificación o detalle del registro manual..." required></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar Registro</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection