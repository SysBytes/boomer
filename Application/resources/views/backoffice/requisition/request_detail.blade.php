@extends('layouts.dashboard')

@section('content')
    <div class="content">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('dashboard') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Volver al Área de Trabajo
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

                <h2 class="content-heading pt-0">1. Datos Generales</h2>

                @if($requisition->status == 'rejected' && $requisition->reject_reason)
                    <div class="alert alert-danger mb-4">
                        <h4 class="alert-heading fs-sm fw-bold"><i class="fa fa-exclamation-triangle me-1"></i> SOLICITUD RECHAZADA</h4>
                        <p class="mb-0">{{ $requisition->reject_reason }}</p>
                        <div class="text-end mt-2">
                            <small>Rechazado por: {{ \App\Models\User::find($requisition->rejected_by)->name ?? 'Administración' }} ({{ $requisition->date_rejected ? \Carbon\Carbon::parse($requisition->date_rejected)->format('d/m/Y h:i A') : '' }})</small>
                        </div>
                    </div>
                @endif

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

                @if($requisition->status == 'approved' && $requisition->date_approved)
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="mb-1 text-muted fs-sm text-uppercase fw-bold text-success">Fecha de Aprobación</p>
                            <p class="fs-5 text-success">{{ \Carbon\Carbon::parse($requisition->date_approved)->format('d/m/Y h:i A') }}</p>
                        </div>
                    </div>
                @endif

                <h2 class="content-heading">2. Lista de Materiales Solicitados</h2>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr class="bg-body-dark">
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Material</th>
                                <th>Categoría</th>
                                <th class="text-center" style="width: 15%;">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisitions->materials ?? $requisition->materials as $index => $mat)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $mat->material_name }}</td>
                                    <td>{{ $mat->category->material_category_name ?? 'N/A' }}</td>
                                    <td class="text-center font-w600 text-primary fs-5">{{ $mat->pivot->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h2 class="content-heading">3. Comentarios y Observaciones</h2>
                <div class="row">
                    <div class="col-md-12">
                        <!-- Historial de comentarios -->
                        <div class="mb-4">
                            @forelse($requisition->comments as $comment)
                                <div class="block block-rounded block-bordered mb-2 {{ $comment->user_id == Auth::id() ? 'bg-info-light border-info' : 'bg-body-light' }}">
                                    <div class="block-content block-content-full p-2 p-md-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="{{ $comment->user_id == Auth::id() ? 'text-info-dark' : 'text-primary' }}">
                                                @if($comment->user_id == Auth::id())
                                                    Tú <span class="text-muted fw-normal">(Técnico)</span>
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
                        <form action="{{ route('requisitions.comment', $requisition->requisition_id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" name="comment" rows="3" placeholder="Añadir algo a la conversación..." required></textarea>
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
@endsection