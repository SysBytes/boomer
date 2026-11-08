@extends('layouts.dashboard')

@section('content')
    <!-- Page Content -->
    <div class="content">
        @if(!Auth::user()->hasRole('assistant'))
            <h2 class="content-heading">Mis Solicitudes de Materiales</h2>
            <div class="block block-rounded">
                <div class="block-content block-content-full">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filtros -->
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                        <div class="row gx-2 align-items-center">
                            <label class="col-form-label col-auto fw-bold" for="status_filter">Filtrar por Estatus:</label>
                            <div class="col-auto">
                                <select name="status" id="status_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Mostrar Todas</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprobada</option>
                                    <option value="purchased" {{ request('status') == 'purchased' ? 'selected' : '' }}>Comprada</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregada</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazada</option>
                                </select>
                            </div>
                            @if(request()->filled('status'))
                                <div class="col-auto">
                                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-alt-danger px-2" data-bs-toggle="tooltip" title="Quitar Filtro">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Cliente / Obra</th>
                                    <th class="text-center">Estatus</th>
                                    <th>Fecha de Solicitud</th>
                                    <th class="text-center" style="width: 100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requisitions as $requisition)
                                    <tr>
                                        <td>{{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            @if($requisition->work)
                                                <strong>{{ $requisition->work->location->client?->client_name ?? 'Cliente N/A' }}</strong><br>
                                                <span class="fs-sm">{{ $requisition->work->work_name }}</span>
                                            @else
                                                Obra Eliminada
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($requisition->status == 'pending')
                                                <span class="badge bg-warning">Pendiente</span>
                                            @elseif($requisition->status == 'approved')
                                                <span class="badge bg-success">Aprobada</span>
                                            @elseif($requisition->status == 'purchased')
                                                <span class="badge bg-info">Comprada</span>
                                            @elseif($requisition->status == 'delivered')
                                                <span class="badge bg-primary">Entregada</span>
                                            @elseif($requisition->status == 'rejected')
                                                <span class="badge bg-danger">Rechazada</span>
                                            @else
                                                <span class="badge bg-dark">{{ $requisition->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $requisition->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('requisitions.show', $requisition->requisition_id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Ver Detalles">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Aún no has realizado ninguna solicitud de materiales.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="block block-rounded text-center">
                <div class="block-content block-content-full py-5">
                    <i class="fa fa-clock fa-3x text-muted mb-3"></i>
                    <h3 class="fs-4 fw-bold">Bienvenido al Área de Trabajo</h3>
                    <p class="text-muted mb-0">Utiliza la opción de Check-in / Check-out en el menú superior para registrar tu asistencia.</p>
                </div>
            </div>
        @endif
    </div>
    <!-- END Page Content -->
@endsection