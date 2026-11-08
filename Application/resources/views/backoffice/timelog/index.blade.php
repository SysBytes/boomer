@extends('layouts.backoffice')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/flatpickr/flatpickr.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
    <script>
        Codebase.helpersOnLoad(['jq-select2', 'js-flatpickr']);

        Codebase.onLoad(() => {
            jQuery('.js-dataTable-timelogs').DataTable({
                order: [[5, 'desc']],
                pagingType: "simple_numbers",
                layout: {
                    topStart: [
                        {
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="fa fa-file-excel me-1"></i> Excel',
                                    className: 'btn btn-sm btn-success',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6, 7] // Excluir columna "Acciones"
                                    }
                                }
                            ]
                        },
                        {
                            pageLength: {
                                menu: [30, 60, 100]
                            }
                        }
                    ]
                },
                pageLength: 30,
                autoWidth: false,
                responsive: true,
                initComplete: function () {
                    jQuery('.buttons-excel').removeClass('btn-secondary');
                }
            });

            // Dependent Filters Logic
            const $country = jQuery('#country_id');
            const $client = jQuery('#client_id');
            const $project = jQuery('#work_id');
            const $city = jQuery('#work_city');

            // Save original options
            const allClients = $client.find('option').clone();
            const allProjects = $project.find('option').clone();

            function updateClients(triggerCascade = true) {
                const countryId = $country.val();
                const selectedClient = $client.val();

                $client.empty();
                $client.append(allClients.first().clone());

                allClients.each(function () {
                    if (!jQuery(this).val()) return;
                    if (!countryId || jQuery(this).data('country') == countryId) {
                        $client.append(jQuery(this).clone());
                    }
                });

                if ($client.find(`option[value="${selectedClient}"]`).length) {
                    $client.val(selectedClient);
                } else {
                    $client.val('');
                }

                // Refresh Select2 UI without triggering infinite loops
                $client.trigger('change.select2');

                if (triggerCascade) {
                    updateProjects();
                }
            }

            function updateProjects() {
                const clientId = $client.val();
                const selectedProject = $project.val();

                $project.empty();
                $project.append(allProjects.first().clone());

                let validCities = new Set();

                allProjects.each(function () {
                    if (!jQuery(this).val()) return;
                    if (!clientId || jQuery(this).data('client') == clientId) {
                        $project.append(jQuery(this).clone());
                        let c = jQuery(this).data('city');
                        if (c) validCities.add(c);
                    }
                });

                if ($project.find(`option[value="${selectedProject}"]`).length) {
                    $project.val(selectedProject);
                } else {
                    $project.val('');
                }

                $project.trigger('change.select2');

                // Update City dropdown based on visible projects
                const selectedCity = $city.val();

                if (!clientId) {
                    allProjects.each(function () {
                        if (jQuery(this).val() && jQuery(this).data('city')) {
                            validCities.add(jQuery(this).data('city'));
                        }
                    });
                }

                $city.empty();
                $city.append('<option></option>');

                // Sort cities alphabetically
                Array.from(validCities).sort().forEach(c => {
                    $city.append(`<option value="${c}">${c}</option>`);
                });

                if (validCities.has(selectedCity)) {
                    $city.val(selectedCity);
                } else {
                    $city.val('');
                }
                $city.trigger('change.select2');
            }

            $country.on('change', function () {
                updateClients(true);
            });

            $client.on('change', function () {
                updateProjects();
            });

            // Initialize on load to filter options based on server-side pre-selected values
            updateClients(true);
        });
    </script>
@endsection

@section('content')
    <div class="content">
        <h2 class="content-heading">Control de Horas @if(request()->anyFilled(['user_id', 'client_id', 'work_id', 'start_date', 'end_date'])) - Filtrado @endif</h2>

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

        <!-- Filtros -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Filtros de Búsqueda</h3>
            </div>
            <div class="block-content block-content-full">
                <form action="{{ route('timelogs.index') }}" method="GET">
                    <div class="row">
                        <!-- País -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="country_id">País</label>
                            <select class="js-select2 form-select filter-trigger" id="country_id" name="country_id" style="width: 100%;" data-placeholder="Todos">
                                <option></option>
                                @foreach($countries as $id => $name)
                                    <option value="{{ $id }}" {{ request('country_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cliente -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="client_id">Cliente</label>
                            <select class="js-select2 form-select filter-trigger" id="client_id" name="client_id" style="width: 100%;" data-placeholder="Todos">
                                <option></option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->client_id }}" data-country="{{ $client->country_id }}" {{ request('client_id') == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Proyecto -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="work_id">Obra</label>
                            <select class="js-select2 form-select filter-trigger" id="work_id" name="work_id" style="width: 100%;" data-placeholder="Todos">
                                <option></option>
                                @foreach($works as $work)
                                    <option value="{{ $work->work_id }}" data-client="{{ $work->location->client_id ?? '' }}" data-city="{{ $work->location->location_city ?? '' }}" {{ request('work_id') == $work->work_id ? 'selected' : '' }}>
                                        {{ $work->location->location_name ?? 'N/A' }} - {{ $work->work_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ciudad -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="work_city">Ciudad</label>
                            <select class="js-select2 form-select filter-trigger" id="work_city" name="work_city" style="width: 100%;" data-placeholder="Todas">
                                <option></option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('work_city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Usuario -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="user_id">Usuario</label>
                            <select class="js-select2 form-select" id="user_id" name="user_id" style="width: 100%;" data-placeholder="Todos">
                                <option></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} @if($user->primary_role_name)({{ $user->primary_role_name }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fecha Inicio -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="start_date">Fecha Inicio</label>
                            <input type="text" class="js-flatpickr form-control bg-white" id="start_date" name="start_date" placeholder="Y-m-d" value="{{ request('start_date') }}">
                        </div>

                        <!-- Fecha Fin -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="end_date">Fecha Fin</label>
                            <input type="text" class="js-flatpickr form-control bg-white" id="end_date" name="end_date" placeholder="Y-m-d" value="{{ request('end_date') }}">
                        </div>

                        <!-- Botones -->
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>
                            <a href="{{ route('timelogs.index') }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Limpiar Filtros">
                                <i class="fa fa-sync"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resultados -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Listado General</h3>
            </div>
            <div class="block-content block-content-full">
                <table class="table table-bordered table-striped table-vcenter js-dataTable-timelogs">
                    <thead>
                        <tr>
                            <th>Técnico</th>
                            <th class="all">Obra</th>
                            <th>Ciudad</th>
                            <th>Cliente</th>
                            <th>País</th>
                            <th>Hora Entrada</th>
                            <th>Hora Salida</th>
                            <th class="text-center all" style="width: 10%;">Horas</th>
                            <th class="text-center all" style="width: 10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timelogs as $log)
                            <tr>
                                <td class="fw-semibold">{{ ucwords(strtolower($log->user->name)) ?? 'N/A' }}</td>
                                <td>
                                    @if($log->work)
                                        <strong>{{ $log->work->location->location_name ?? 'N/A' }} - {{ $log->work->work_name }}</strong>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $log->work->location->location_city ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $log->work->location && $log->work->location->client ? $log->work->location->client->client_name : 'N/A' }}
                                </td>
                                <td>
                                    {{ $log->work->location && $log->work->location->client ? ($countries[$log->work->location->client->country_id] ?? 'N/A') : 'N/A' }}
                                </td>
                                <td data-sort="{{ $log->check_in_time ? $log->check_in_time->timestamp : 0 }}">
                                    {{ $log->check_in_time ? $log->check_in_time->format('d/m/Y h:i A') : 'N/A' }}
                                </td>
                                <td data-sort="{{ $log->check_out_time ? $log->check_out_time->timestamp : 0 }}">
                                    {{ $log->check_out_time ? $log->check_out_time->format('d/m/Y h:i A') : 'En curso' }}
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $log->hours_worked ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('timelogs.show', $log->time_log_id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Ver Detalles">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->hasAnyRole(['admin', 'developer']) && !empty($log->check_out_time))
                                            <a href="{{ route('timelogs.edit', $log->time_log_id) }}" class="btn btn-sm btn-alt-warning" data-bs-toggle="tooltip" title="Editar Registro">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(auth()->user()->hasAnyRole(['admin', 'developer']) && empty($log->check_out_time) && \Carbon\Carbon::parse($log->check_in_time)->diffInHours(now()) >= 9)
                                            @if(env('WHATSAPP_NOTIFICATIONS_ENABLED', 'true') == 1)
                                                <form action="{{ route('timelogs.remind', $log->time_log_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-alt-success" data-bs-toggle="tooltip" title="Enviar Recordatorio de Check-Out por WhatsApp">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        @if(auth()->user()->hasRole('developer'))
                                            <form action="{{ route('timelogs.destroy', $log->time_log_id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro de horas? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-alt-danger" data-bs-toggle="tooltip" title="Eliminar Registro">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection