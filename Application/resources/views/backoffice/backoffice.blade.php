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
    <!-- Page Content -->
    <div class="content">
        <h2 class="content-heading">Panel de Control</h2>

        <div class="row items-push">

            <!-- Clientes -->
            <div class="col-sm-6 col-xl-3">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('client.index') }}">
                    <div class="block-content block-content-full py-5">
                        <div class="item item-2x item-circle bg-info-light mx-auto mb-4">
                            <i class="fa-regular fa-building fa-2x text-info"></i>
                        </div>
                        <div class="fs-1 fw-bold text-info">{{ $totalClients }}</div>
                        <div class="text-muted fw-semibold mt-1">Clientes Activos</div>
                    </div>
                </a>
            </div>

            <!-- Proyectos -->
            <div class="col-sm-6 col-xl-3">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('work.index') }}">
                    <div class="block-content block-content-full py-5">
                        <div class="item item-2x item-circle bg-primary-light mx-auto mb-4">
                            <i class="fa-solid fa-diagram-project fa-2x text-primary"></i>
                        </div>
                        <div class="fs-1 fw-bold text-primary">{{ $totalWorks }}</div>
                        <div class="text-muted fw-semibold mt-1">Obras Activas</div>
                    </div>
                </a>
            </div>

            <!-- Materiales -->
            <div class="col-sm-6 col-xl-3">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('material.index') }}">
                    <div class="block-content block-content-full py-5">
                        <div class="item item-2x item-circle bg-success-light mx-auto mb-4">
                            <i class="fa-solid fa-boxes-packing fa-2x text-success"></i>
                        </div>
                        <div class="fs-1 fw-bold text-success">{{ $totalMaterials }}</div>
                        <div class="text-muted fw-semibold mt-1">Materiales en Catálogo</div>
                    </div>
                </a>
            </div>

            <!-- Requisiciones Pendientes -->
            <div class="col-sm-6 col-xl-3">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('requisitions.index', ['status' => 'pending']) }}">
                    <div class="block-content block-content-full py-5">
                        <div class="item item-2x item-circle mx-auto mb-4 {{ $totalPendingRequisitions > 0 ? 'bg-warning-light' : 'bg-body-light' }}">
                            <i class="fa-solid fa-clipboard-list fa-2x {{ $totalPendingRequisitions > 0 ? 'text-warning' : 'text-muted' }}"></i>
                        </div>
                        <div class="fs-1 fw-bold {{ $totalPendingRequisitions > 0 ? 'text-warning' : 'text-muted' }}">{{ $totalPendingRequisitions }}</div>
                        <div class="text-muted fw-semibold mt-1">Requisiciones Pendientes</div>
                    </div>
                </a>
            </div>

        </div>

        {{-- ── Fila 2: Semáforos operacionales ────────────────────────────── --}}
        <div class="row items-push">

            <!-- Sin Stock -->
            <div class="col-sm-6 col-xl-4">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('reports.inventory', ['filter' => 'no_stock']) }}">
                    <div class="block-content block-content-full py-4">
                        <div class="item item-2x item-circle mx-auto mb-3 {{ $totalNoStock > 0 ? 'bg-danger-light' : 'bg-success-light' }}">
                            <i class="fa-solid fa-triangle-exclamation fa-2x {{ $totalNoStock > 0 ? 'text-danger' : 'text-success' }}"></i>
                        </div>
                        <div class="fs-1 fw-bold {{ $totalNoStock > 0 ? 'text-danger' : 'text-success' }}">{{ $totalNoStock }}</div>
                        <div class="text-muted fw-semibold mt-1">Materiales Sin Stock</div>
                        <div class="fs-xs text-muted mt-1">{{ $totalNoStock > 0 ? 'Requieren reposición' : 'Inventario completo ✓' }}</div>
                    </div>
                </a>
            </div>

            <!-- Aprobadas (esperando compra) -->
            <div class="col-sm-6 col-xl-4">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('requisitions.index', ['status' => 'approved']) }}">
                    <div class="block-content block-content-full py-4">
                        <div class="item item-2x item-circle mx-auto mb-3 {{ $totalApproved > 0 ? 'bg-info-light' : 'bg-body-light' }}">
                            <i class="fa-solid fa-cart-shopping fa-2x {{ $totalApproved > 0 ? 'text-info' : 'text-muted' }}"></i>
                        </div>
                        <div class="fs-1 fw-bold {{ $totalApproved > 0 ? 'text-info' : 'text-muted' }}">{{ $totalApproved }}</div>
                        <div class="text-muted fw-semibold mt-1">Aprobadas · Por Comprar</div>
                        <div class="fs-xs text-muted mt-1">{{ $totalApproved > 0 ? 'Pendientes en compras' : 'Sin órdenes en espera' }}</div>
                    </div>
                </a>
            </div>

            <!-- Compradas (esperando entrega) -->
            <div class="col-sm-6 col-xl-4">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('requisitions.index', ['status' => 'purchased']) }}">
                    <div class="block-content block-content-full py-4">
                        <div class="item item-2x item-circle mx-auto mb-3 {{ $totalPurchased > 0 ? 'bg-primary-light' : 'bg-body-light' }}">
                            <i class="fa-solid fa-truck fa-2x {{ $totalPurchased > 0 ? 'text-primary' : 'text-muted' }}"></i>
                        </div>
                        <div class="fs-1 fw-bold {{ $totalPurchased > 0 ? 'text-primary' : 'text-muted' }}">{{ $totalPurchased }}</div>
                        <div class="text-muted fw-semibold mt-1">Compradas · Por Entregar</div>
                        <div class="fs-xs text-muted mt-1">{{ $totalPurchased > 0 ? 'Esperando entrega al supervisor' : 'Sin entregas pendientes' }}</div>
                    </div>
                </a>
            </div>

        </div>

        {{-- ── Tabla: Últimas requisiciones ───────────────────────────────── --}}
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title"><i class="fa-solid fa-clock-rotate-left me-1 text-muted"></i> Últimas Requisiciones</h3>
                <div class="block-options">
                    <a href="{{ route('requisitions.index') }}" class="btn btn-sm btn-alt-primary">Ver todas</a>
                </div>
            </div>
            <div class="block-content block-content-full">
                <table class="table table-striped table-vcenter js-dataTable-responsive mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Obra / Cliente</th>
                            <th>Creada por</th>
                            <th class="text-center" style="width: 160px;">Fecha</th>
                            <th class="text-center" style="width: 130px;">Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestRequisitions as $req)
                            <tr>
                                <td>
                                    <a href="{{ route('requisitions.status', ['requisition' => $req->requisition_id, 'status' => $req->status]) }}" class="fw-semibold">
                                        {{ str_pad($req->requisition_id, 4, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $req->work?->work_name ?? 'N/A' }}</div>
                                    <div class="fs-xs text-muted">{{ $req->work?->location?->client?->client_name ?? '—' }}</div>
                                </td>
                                <td>{{ $req->user?->name ?? '—' }}</td>
                                <td class="text-center text-muted fs-sm">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    @php
                                        $badge = match ($req->status) {
                                            'pending' => 'warning',
                                            'approved' => 'info',
                                            'purchased' => 'primary',
                                            'delivered' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary',
                                        };
                                        $label = match ($req->status) {
                                            'pending' => 'Pendiente',
                                            'approved' => 'Aprobada',
                                            'purchased' => 'Comprada',
                                            'delivered' => 'Entregada',
                                            'rejected' => 'Rechazada',
                                            default => $req->status,
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                                </td>
                            </tr>
                        @empty
                            <!-- DataTables will handle the empty state -->
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <!-- END Page Content -->
@endsection