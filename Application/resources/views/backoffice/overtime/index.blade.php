@extends('layouts.backoffice')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="content-heading mb-0">Gestión de Horas Extras</h2>
            @if(Auth::user()->hasAnyRole(['developer']))
                <form action="{{ route('overtimes.recalculate') }}" method="POST" class="d-inline-block">
                    @csrf
                    <!-- Se envían los mismos filtros de la URL para recalcular masivamente -->
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                    <input type="hidden" name="work_id" value="{{ request('work_id') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-warning" onclick="return confirm('¿Está seguro de recalcular las horas extras de los registros filtrados actualmente?');">
                        <i class="fa fa-sync-alt me-1"></i> Recalcular Filtrados
                    </button>
                </form>
            @endif
        </div>

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
                <form action="{{ route('overtimes.index') }}" method="GET" class="row gx-2 gy-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" for="user_id">Técnico</label>
                        <select class="js-select2 form-select" id="user_id" name="user_id" style="width: 100%;" data-placeholder="Todos">
                            <option></option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="work_id">Obra</label>
                        <select class="js-select2 form-select" id="work_id" name="work_id" style="width: 100%;" data-placeholder="Todos">
                            <option></option>
                            @foreach($works as $w)
                                <option value="{{ $w->work_id }}" {{ request('work_id') == $w->work_id ? 'selected' : '' }}>
                                    {{ $w->location->location_name ?? 'N/A' }} - {{ $w->work_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="status">Estado</label>
                        <select class="js-select2 form-select" id="status" name="status" style="width: 100%;" data-placeholder="Todos">
                            <option></option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprobada</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="start_date">Desde</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="end_date">Hasta</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12 mt-3 text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter me-1"></i> Filtrar</button>
                        <a href="{{ route('overtimes.index') }}" class="btn btn-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla -->
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-striped table-vcenter js-dataTable-responsive">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Técnico</th>
                                <th>Obra</th>
                                <th class="text-center">Horas</th>
                                <th>Motivo</th>
                                <th class="text-center">Estado</th>
                                <th>Aprobado por</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overtimes as $overtime)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($overtime->date)->format('d/m/Y') }}</td>
                                    <td class="fw-semibold">{{ ucwords(strtolower($overtime->user->name)) ?? 'N/A' }}</td>
                                    <td>{{ $overtime->work->location->location_name ?? 'N/A' }} - {{ $overtime->work->work_name ?? 'N/A' }}</td>
                                    <td class="text-center fw-bold text-success">+{{ $overtime->hours }}</td>
                                    <td>{{ $overtime->type_formatted }}</td>
                                    <td class="text-center">{!! $overtime->status_badge !!}</td>
                                    <td class="fs-sm">{{ $overtime->approver->name ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($overtime->status == 'pending')
                                            <form action="{{ route('overtimes.status', $overtime->overtime_id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-alt-success" data-bs-toggle="tooltip" title="Aprobar" onclick="return confirm('¿Aprobar hora extra?');">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('overtimes.status', $overtime->overtime_id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-alt-danger" data-bs-toggle="tooltip" title="Rechazar" onclick="return confirm('¿Rechazar hora extra?');">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('overtimes.status', $overtime->overtime_id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Revertir a Pendiente" onclick="return confirm('¿Devolver a estado pendiente?');">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(auth()->user()->hasRole('developer'))
                                            <form action="{{ route('overtimes.destroy', $overtime->overtime_id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-alt-danger ms-1" data-bs-toggle="tooltip" title="Eliminar Registro" onclick="return confirm('¿Está seguro de eliminar este registro de hora extra? Esta acción no se puede deshacer.');">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
    <script>
        Codebase.helpersOnLoad(['jq-select2']);
    </script>
@endsection