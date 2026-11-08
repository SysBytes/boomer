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
    <script src="{{ asset('js/plugins/datatables-buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/flatpickr/flatpickr.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
    <script>
        Codebase.helpersOnLoad(['jq-select2', 'js-flatpickr']);

        Codebase.onLoad(() => {
            jQuery('.js-dataTable-worked-hours').DataTable({
                pagingType: "simple_numbers",
                layout: {
                    topStart: [
                        {
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="fa fa-file-excel me-1"></i> Excel',
                                    className: 'btn btn-sm btn-success',
                                    footer: true
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="fa fa-print me-1"></i> Imprimir',
                                    className: 'btn btn-sm btn-primary',
                                    footer: true,
                                    customize: function (win) {
                                        jQuery(win.document.body).find('table').css('white-space', 'nowrap');
                                    }
                                }
                            ]
                        },
                        {
                            pageLength: {
                                menu: [[30, 60, 100, -1], [30, 60, 100, "Todos"]]
                            }
                        }
                    ]
                },
                pageLength: -1, // Changed to show all rows by default so groups stay together
                ordering: false,
                autoWidth: false,
                responsive: true,
                initComplete: function () {
                    jQuery('.buttons-excel').removeClass('btn-secondary');
                    jQuery('.buttons-print').removeClass('btn-secondary');
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    var total = 0;

                    // Sumamos solo las filas que no son subtotales (no tienen la clase table-active)
                    api.rows({ search: 'applied' }).every(function () {
                        var rowNode = this.node();
                        if (!jQuery(rowNode).hasClass('table-active')) {
                            // En DataTables, this.data() es un array de los contenidos de la celda
                            var val = this.data()[6];

                            // Si el valor contiene HTML (como <span> o <strong>), lo limpiamos
                            if (typeof val === 'string') {
                                val = val.replace(/<[^>]*>?/gm, '');
                            }

                            total += intVal(val);
                        }
                    });

                    // Update footer
                    jQuery(api.column(6).footer()).html(
                        '<strong>' + total.toFixed(2) + ' Horas</strong>'
                    );
                }
            });
        });
    </script>
@endsection

@section('content')
    <div class="content">
        <h2 class="content-heading">Reporte de Horas Trabajadas</h2>

        <!-- Filtros -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Filtros de Búsqueda</h3>
            </div>
            <div class="block-content block-content-full">
                <form action="{{ route('reports.worked_hours') }}" method="GET">
                    <div class="row">
                        <!-- Usuario -->
                        <div class="col-md-3 mb-3">
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

                        <!-- Proyecto -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="work_id">Obra</label>
                            <select class="js-select2 form-select filter-trigger" id="work_id" name="work_id" style="width: 100%;" data-placeholder="Todos">
                                <option></option>
                                @foreach($works as $work)
                                    <option value="{{ $work->work_id }}" {{ request('work_id') == $work->work_id ? 'selected' : '' }}>
                                        {{ $work->location->location_name ?? 'N/A' }} - {{ $work->work_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fecha Inicio -->
                        <div class="col-md-2 mb-3">
                            <label class="form-label" for="start_date">Fecha Inicio</label>
                            <input type="text" class="js-flatpickr form-control bg-white" id="start_date" name="start_date" placeholder="Y-m-d" value="{{ request('start_date') }}">
                        </div>

                        <!-- Fecha Fin -->
                        <div class="col-md-2 mb-3">
                            <label class="form-label" for="end_date">Fecha Fin</label>
                            <input type="text" class="js-flatpickr form-control bg-white" id="end_date" name="end_date" placeholder="Y-m-d" value="{{ request('end_date') }}">
                        </div>

                        <!-- Botones -->
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>
                            <a href="{{ route('reports.worked_hours') }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Limpiar Filtros">
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
                <h3 class="block-title">Resultados del Reporte</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-worked-hours">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Técnico</th>
                                <th>Obra</th>
                                <th>Tipo</th>
                                <th class="text-end">Horas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedRows as $userName => $rows)
                                @php $userTotal = 0; @endphp
                                @foreach ($rows as $row)
                                    @php $userTotal += $row->hours; @endphp
                                    <tr>
                                        <td data-sort="{{ $row->sort_date }}">{{ $row->date }}</td>
                                        <td class="text-nowrap" style="white-space: nowrap;">{{ $row->check_in }}</td>
                                        <td class="text-nowrap" style="white-space: nowrap;">{{ $row->check_out }}</td>
                                        <td class="fw-semibold">{{ ucwords(strtolower($row->user_name)) }}</td>
                                        <td>{{ $row->work_name }}</td>
                                        <td>{!! $row->type_badge !!} {{ $row->type }}</td>
                                        <td class="text-end fw-bold">{{ number_format($row->hours, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end text-uppercase fw-bold"><strong>Total {{ $userName }}:</strong></td>
                                    <td class="text-end fw-bold text-primary"><strong>{{ number_format($userTotal, 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end text-uppercase fw-bold"><strong>Total:</strong></th>
                                <th class="text-end fw-bold"><strong>0.00</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection