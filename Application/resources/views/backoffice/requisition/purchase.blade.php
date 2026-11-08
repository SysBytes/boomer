@extends('layouts.backoffice')

@section('content')
<div class="content">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('requisitions.status', ['requisition' => $requisition->requisition_id, 'status' => 'approved']) }}" class="btn btn-alt-secondary">
                <i class="fa fa-arrow-left me-1"></i> Cancelar Recepción
            </a>
        </div>
    </div>

    <!-- Título y cabecera -->
    <div class="block block-rounded block-themed">
        <div class="block-header bg-xsmooth-dark">
            <h3 class="block-title">Confirmar Recepción de Compra N° {{ str_pad($requisition->requisition_id, 4, '0', STR_PAD_LEFT) }}</h3>
        </div>
        <div class="block-content block-content-full">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <div class="alert alert-info">
                <h4 class="alert-heading fs-sm fw-bold"><i class="fa fa-info-circle me-1"></i> AVISO DE INVENTARIO</h4>
                <p class="mb-0">Revise cuidadosamente las cantidades físicas que ha recibido. Una vez presione Confirmar, este valor modificará el flujo real del sistema y **aumentará el stock** dentro del Almacén General por la cantidad que digite abajo. Modifique a libre albedrío las cajas.</p>
            </div>

            <h2 class="content-heading pt-0">1. Datos de Requisición Origen</h2>
            <div class="row mb-4">
                <div class="col-md-4">
                    <p class="mb-1 text-muted fs-sm text-uppercase fw-bold">Técnico Solicitante</p>
                    <p class="fs-5">{{ $requisition->user ? $requisition->user->name : 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted fs-sm text-uppercase fw-bold">Cliente / Obra</p>
                    <p class="fs-5">{{ $requisition->work?->location?->client?->client_name ?? 'Cliente N/A' }} - {{ $requisition->work?->work_name ?? 'Obra Eliminada' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted fs-sm text-uppercase fw-bold">Aprobado Por</p>
                    <p class="fs-5">{{ \App\Models\User::find($requisition->approved_by)->name ?? 'Administración' }}</p>
                </div>
            </div>

            <h2 class="content-heading">2. Ingreso al Almacén</h2>
            <form action="{{ route('requisitions.purchase_store', $requisition->requisition_id) }}" method="POST">
                @csrf
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr class="bg-body-dark">
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Descripción del Material</th>
                                <th>Categoría</th>
                                <th class="text-center" style="width: 25%;">Cantidad Recibida (Real)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisition->materials as $index => $mat)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $mat->material_name }}</td>
                                <td>{{ $mat->category->material_category_name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="input-group">
                                        <input type="number" 
                                               class="form-control text-center text-primary fw-bold" 
                                               name="quantities[{{ $mat->material_id }}]" 
                                               value="{{ old('quantities.'.$mat->material_id, $mat->pivot->quantity) }}" 
                                               min="0" 
                                               step="any" 
                                               required>
                                        <span class="input-group-text bg-light">Uds</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="form-group text-center mt-5">
                    <button type="submit" class="btn btn-lg btn-success" onclick="return confirm('¿Está confirmando de manera final que se han recibido estas cantidades fisicamente y procederá a añadirlas al stock de almacén?')">
                        <i class="fa fa-boxes-packing me-1"></i> Confirmar y Agregar a Inventario
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection
