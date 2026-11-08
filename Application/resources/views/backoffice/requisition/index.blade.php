@extends('layouts.backoffice')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection

@section('content')
<div class="content">
    <h2 class="content-heading">Requisiciones @if(request('status')) - Filtradas @endif</h2>

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Listado General</h3>
        </div>
        <div class="block-content block-content-full">

            <table class="table table-bordered table-striped table-vcenter js-dataTable-responsive">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">N°</th>
                        <th>Técnico Solicitante</th>
                        <th class="all">Cliente / Obra</th>
                        <th>Fecha de Solicitud</th>
                        <th class="text-center all" style="width: 15%;">Estatus</th>
                        <th class="text-center all" style="width: 10%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requisitions as $req)
                        <tr>
                            <td class="text-center">
                                <span class="fw-bold">{{ str_pad($req->requisition_id, 4, '0', STR_PAD_LEFT) }}</span><br>
                                @if($req->priority == 'high')
                                    <i class="fa fa-flag text-danger" title="Prioridad Alta"></i>
                                @elseif($req->priority == 'low')
                                    <i class="fa fa-arrow-down text-info" title="Prioridad Baja"></i>
                                @else
                                    <i class="fa fa-minus text-secondary" title="Prioridad Media"></i>
                                @endif
                            </td>
                            <td>{{ $req->user ? $req->user->name : 'N/A' }}</td>
                            <td>
                                @if($req->work)
                                    <strong>{{ $req->work->location->client?->client_name ?? 'Cliente N/A' }}</strong><br>
                                    <span class="fs-sm">{{ $req->work->work_name }}</span>
                                @else
                                    Obra Eliminada
                                @endif
                            </td>
                            <td>{{ $req->created_at->format('d/m/Y h:i A') }}</td>
                            <td class="text-center">
                                @if($req->status == 'pending')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($req->status == 'approved')
                                    <span class="badge bg-success">Aprobada</span>
                                @elseif($req->status == 'purchased')
                                    <span class="badge bg-info">Comprada</span>
                                @elseif($req->status == 'delivered')
                                    <span class="badge bg-primary">Entregada</span>
                                @elseif($req->status == 'cancelled')
                                    <span class="badge bg-secondary">Cancelada</span>
                                @elseif($req->status == 'rejected')
                                    <span class="badge bg-danger">Rechazada</span>
                                @else
                                    <span class="badge bg-dark">{{ $req->status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('requisitions.status', ['requisition' => $req->requisition_id, 'status' => request('status') ?? $req->status]) }}" type="button" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop