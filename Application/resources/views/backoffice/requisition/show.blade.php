@extends('layouts.backoffice')

@section('content')
    <div class="content">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('requisitions.index', ['status' => request('status') ?? $requisition->status]) }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Volver al Listado
                </a>
            </div>
        </div>

        <!-- Título y cabecera -->
        <div class="block block-rounded block-themed">
            <div class="block-header bg-primary-dark">
                <h3 class="block-title">Detalle de Solicitud N° {{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}</h3>
                <div class="block-options">
                    <span class="badge bg-light text-dark fs-sm border shadow-sm">
                        @if($requisition->status == 'pending')
                            <i class="fa fa-clock text-warning"></i> Pendiente
                        @elseif($requisition->status == 'approved')
                            <i class="fa fa-check text-success"></i> Aprobada
                        @elseif($requisition->status == 'purchased')
                            <i class="fa fa-shopping-cart text-info"></i> Comprada
                        @elseif($requisition->status == 'delivered')
                            <i class="fa fa-box-open text-primary"></i> Entregada
                        @elseif($requisition->status == 'cancelled')
                            <i class="fa fa-ban text-secondary"></i> Cancelada
                        @elseif($requisition->status == 'rejected')
                            <i class="fa fa-times text-danger"></i> Rechazada
                        @else
                            {{ $requisition->status }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="block-content block-content-full">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('stock_errors'))
                    <div class="alert alert-warning mb-4">
                        <h5 class="alert-heading mb-2">
                            <i class="fa fa-exclamation-triangle me-1"></i>
                            Stock insuficiente — No se puede entregar la requisición
                        </h5>
                        <p class="mb-2 fs-sm">Los siguientes materiales no tienen suficiente stock disponible para completar la entrega:</p>
                        <table class="table table-sm table-bordered mb-0" style="background:#fff;">
                            <thead class="table-warning">
                                <tr>
                                    <th>Material</th>
                                    <th class="text-center">Solicitado</th>
                                    <th class="text-center">Disponible en Stock</th>
                                    <th class="text-center text-danger">Faltante</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('stock_errors') as $err)
                                    <tr>
                                        <td>{{ $err['material'] }}</td>
                                        <td class="text-center">{{ $err['requested'] }}</td>
                                        <td class="text-center text-danger fw-bold">{{ $err['available'] }}</td>
                                        <td class="text-center text-danger fw-bold">-{{ $err['shortfall'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @if($requisition->status == 'pending')
                    @if(Auth::user()->hasAnyRole(['developer', 'admin']) || (Auth::user()->hasRole('supervisor') && $requisition->work->supervisor_id == Auth::id()))
                        <div class="mb-4 d-flex justify-content-end gap-2">
                            <form action="{{ route('requisitions.approve', $requisition->requisition_id) }}" method="POST" onsubmit="return handleApprove(this);">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check me-1"></i> Aprobar Solicitud
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-reject">
                                <i class="fa fa-times me-1"></i> Rechazar Solicitud
                            </button>
                        </div>
                    @endif
                @endif

                @if($requisition->status == 'approved')
                    <div class="mb-4 d-flex justify-content-end gap-2">
                        @if(Auth::user()->hasRole('developer'))
                            <form action="{{ route('requisitions.return_pending', $requisition->requisition_id) }}" method="POST" onsubmit="return confirm('¿Está seguro de devolver esta requisición a Pendiente?');">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa fa-undo me-1"></i> Devolver a Pendiente
                                </button>
                            </form>
                        @endif

                        @if(Auth::user()->hasAnyRole(['purchasing', 'developer', 'admin']))
                            <a href="{{ route('requisitions.print', $requisition->requisition_id) }}" target="_blank" class="btn btn-info">
                                <i class="fa fa-print me-1"></i> Imprimir Orden para Proveedor
                            </a>
                            <a href="{{ route('requisitions.purchase', $requisition->requisition_id) }}" class="btn btn-primary">
                                <i class="fa fa-boxes-packing me-1"></i> Confirmar Compra
                            </a>
                        @endif
                    </div>
                @endif

                @if($requisition->status == 'purchased')
                    @if(Auth::user()->hasAnyRole(['developer', 'admin']) || (Auth::user()->hasRole('supervisor') && $requisition->work->supervisor_id == Auth::id()))
                        <div class="mb-4 d-flex justify-content-end">
                            <form action="{{ route('requisitions.deliver', $requisition->requisition_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning" onclick="return confirm('¿Confirma que estos materiales se están entregando físicamente? Esto restará la cantidad solicitada del stock de almacén.')">
                                    <i class="fa fa-truck-fast me-1"></i> Entregar Requisición
                                </button>
                            </form>
                        </div>
                    @endif
                @endif

                @if($requisition->status == 'rejected' && $requisition->reject_reason)
                    <div class="alert alert-danger mb-4">
                        <h4 class="alert-heading fs-sm fw-bold"><i class="fa fa-exclamation-triangle me-1"></i> SOLICITUD RECHAZADA</h4>
                        <p class="mb-0">{{ $requisition->reject_reason }}</p>
                        <div class="text-end mt-2">
                            <small>Rechazado por: {{ \App\Models\User::find($requisition->rejected_by)->name ?? 'Administración' }} ({{ $requisition->date_rejected ? \Carbon\Carbon::parse($requisition->date_rejected)->format('d/m/Y h:i A') : '' }})</small>
                        </div>
                    </div>
                @endif

                <h2 class="content-heading pt-0">1. Datos Generales</h2>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fs-sm text-uppercase fw-bold">Técnico Solicitante</p>
                        <p class="fs-5">{{ $requisition->user ? $requisition->user->name : 'N/A' }}</p>
                    </div>
                    @if($requisition->status == 'approved' && $requisition->date_approved)
                        <div class="col-md-4">
                            <p class="mb-1 text-muted fs-sm text-uppercase fw-bold text-success">Fecha de Aprobación</p>
                            <p class="fs-5 text-success">{{ \Carbon\Carbon::parse($requisition->date_approved)->format('d/m/Y h:i A') }}</p>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fs-sm text-uppercase fw-bold">Nivel de Prioridad</p>
                        @if($requisition->status === 'pending' && (Auth::user()->hasAnyRole(['developer', 'admin']) || (Auth::user()->hasRole('supervisor') && $requisition->work->supervisor_id == Auth::id())))
                            <form action="{{ route('requisitions.priority', $requisition->requisition_id) }}" method="POST">
                                @csrf
                                <select name="priority"
                                    class="form-select w-75 {{ $requisition->priority == 'high' ? 'bg-danger-light text-danger border-danger' : ($requisition->priority == 'low' ? 'bg-info-light text-info border-info' : 'bg-warning-light text-warning border-warning') }}"
                                    onchange="this.form.submit()">
                                    <option value="low" {{ $requisition->priority == 'low' ? 'selected' : '' }}>Prioridad Baja</option>
                                    <option value="medium" {{ $requisition->priority == 'medium' ? 'selected' : '' }}>Prioridad Media</option>
                                    <option value="high" {{ $requisition->priority == 'high' ? 'selected' : '' }}>Prioridad Alta</option>
                                </select>
                            </form>
                        @else
                            <p class="fs-5 mb-0 fw-bold {{ $requisition->priority == 'high' ? 'text-danger' : ($requisition->priority == 'low' ? 'text-info' : 'text-warning') }}">
                                @if($requisition->priority == 'high')
                                    <i class="fa fa-flag me-1"></i> Alta
                                @elseif($requisition->priority == 'low')
                                    <i class="fa fa-arrow-down me-1"></i> Baja
                                @else
                                    <i class="fa fa-minus me-1"></i> Media
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fs-sm text-uppercase fw-bold">Cliente</p>
                        <p class="fs-5">{{ $requisition->work?->location?->client?->client_name ?? 'Cliente N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fs-sm text-uppercase fw-bold">Obra / Proyecto</p>
                        <p class="fs-5">{{ $requisition->work?->work_name ?? 'Obra Eliminada' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fs-sm text-uppercase fw-bold">Fecha de Elaboración</p>
                        <p class="fs-5">{{ $requisition->created_at->format('d/m/Y h:i A') }}</p>
                    </div>
                </div>

                <h2 class="content-heading">2. Lista de Materiales Solicitados</h2>
                @php
                    $canSeeStock = Auth::user()->hasAnyRole(['purchasing', 'admin', 'developer'])
                        && $requisition->status === 'approved';
                @endphp
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr class="bg-body-dark">
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Material</th>
                                <th>Categoría</th>
                                <th class="text-center" style="width: 15%;">Cantidad Solicitada</th>
                                @if($canSeeStock)
                                    <th class="text-center" style="width: 15%;">Stock Actual</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->materials as $index => $mat)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $mat->material_name }}</td>
                                    <td>{{ $mat->category->material_category_name ?? 'N/A' }}</td>
                                    <td class="text-center font-w600 text-primary fs-5">{{ $mat->pivot->quantity }}</td>
                                    @if($canSeeStock)
                                        @php
                                            $stockColor = $mat->stock <= $mat->pivot->quantity ? 'text-danger' : 'text-success';
                                        @endphp
                                        <td class="text-center font-w600 fs-5 {{ $stockColor }}">
                                            {{ $mat->stock }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h2 class="content-heading">3. Historial de Observaciones</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-4">
                            @forelse($requisition->comments as $comment)
                                <div class="block block-rounded block-bordered mb-2 {{ $comment->user_id == Auth::id() ? 'bg-info-light border-info' : 'bg-body-light' }}">
                                    <div class="block-content block-content-full p-2 p-md-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="{{ $comment->user_id == Auth::id() ? 'text-info-dark' : 'text-primary' }}">
                                                @if($comment->user_id == Auth::id())
                                                    Tú <span class="text-muted fw-normal">(Administración)</span>
                                                @else
                                                    {{ $comment->user->name }}
                                                @endif
                                            </strong>
                                            <span class="text-muted fs-xs">{{ $comment->created_at->format('d/m/Y h:i A') }}</span>
                                        </div>
                                        <p class="mb-0">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted fst-italic">No hay comentarios en esta solicitud.</p>
                            @endforelse
                        </div>

                        <!-- Agregar comentario -->
                        <form action="{{ route('requisitions.comment_backoffice', $requisition->requisition_id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" name="comment" rows="3" placeholder="Añadir una observación gerencial a la conversación..." required></textarea>
                                @error('comment')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-reply me-1"></i> Enviar Comentario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="modal-reject" tabindex="-1" role="dialog" aria-labelledby="modal-reject" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-white">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Indique el Motivo del Rechazo</h5>
                    <button type="button" class="btn-close btn-close-white text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('requisitions.reject', $requisition->requisition_id) }}" method="POST">
                    @csrf
                    <div class="modal-body pb-1">
                        <div class="mb-4">
                            <label class="form-label" for="reject_reason">Causa / Razón <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reject_reason" name="reject_reason" rows="4" placeholder="Escriba aquí los motivos detallados..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END Modal Reject -->

    <script>
        function handleApprove(form) {
            if (confirm('¿Está seguro de Aprobar esta requisición? Todos los de compras serán notificados.')) {
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Aprobando...';
                return true;
            }
            return false;
        }
    </script>
@endsection