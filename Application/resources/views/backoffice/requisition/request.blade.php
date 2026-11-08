@extends('layouts.dashboard')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Select2
            jQuery('.js-select2').select2();

            let materialCounter = 0;
            const $addBtn = document.getElementById('add-material-btn');
            const $materialSelect = document.getElementById('material_select');
            const $quantityInput = document.getElementById('material_quantity');
            const $itemsTableBody = document.getElementById('materials-list-body');
            const $emptyMessage = document.getElementById('empty-message');

            $addBtn.addEventListener('click', function() {
                const materialId = $materialSelect.value;
                // Para obtener el texto seleccionado en Select2 con JS nativo/jQuery
                const materialName = jQuery('#material_select option:selected').text();
                const quantity = parseInt($quantityInput.value);

                if(!materialId) {
                    alert('Debe seleccionar un material.');
                    return;
                }

                if(!quantity || quantity < 1) {
                    alert('Debe ingresar una cantidad válida mayor a 0.');
                    return;
                }

                // Verificar si el material ya fue agregado
                if(document.querySelector(`input[data-mat-id="${materialId}"]`)) {
                    alert('Este material ya ha sido agregado a la lista. Puede eliminarlo e ingresarlo nuevamente si desea corregir la cantidad.');
                    return;
                }

                // Crear fila
                const tr = document.createElement('tr');
                tr.setAttribute('id', `row-mat-${materialCounter}`);
                
                tr.innerHTML = `
                    <td class="text-center">
                        <i class="fa fa-check text-success"></i>
                        <input type="hidden" name="materials[${materialCounter}][id]" value="${materialId}" data-mat-id="${materialId}">
                        <input type="hidden" name="materials[${materialCounter}][quantity]" value="${quantity}">
                    </td>
                    <td>${materialName}</td>
                    <td class="text-center font-w600">${quantity}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-alt-danger remove-material" data-row="row-mat-${materialCounter}">
                            <i class="fa fa-times"></i>
                        </button>
                    </td>
                `;

                $itemsTableBody.appendChild(tr);
                $emptyMessage.style.display = 'none';

                // Reset inputs
                jQuery('#material_select').val(null).trigger('change');
                $quantityInput.value = '';
                materialCounter++;
            });

            // Delegación de eventos para botón eliminar
            $itemsTableBody.addEventListener('click', function(e) {
                const target = e.target.closest('.remove-material');
                if(target) {
                    const rowId = target.getAttribute('data-row');
                    document.getElementById(rowId).remove();
                    
                    if($itemsTableBody.children.length === 0) {
                        $emptyMessage.style.display = 'table-row';
                    }
                }
            });

            // Pre-submit validation
            document.getElementById('requisition-form').addEventListener('submit', function(e) {
                if($itemsTableBody.children.length === 0) {
                    e.preventDefault();
                    alert('Debe agregar al menos un material a la solicitud.');
                    return;
                }
                
                // Efecto de carga
                const submitBtn = document.getElementById('btn-submit');
                const btnIcon = document.getElementById('btn-icon');
                const btnText = document.getElementById('btn-text');
                
                submitBtn.disabled = true;
                btnIcon.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>';
                btnText.textContent = 'Enviando...';
            });
        });
    </script>
@endsection

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
            <h3 class="block-title">Nueva Solicitud de Materiales</h3>
        </div>
        <div class="block-content block-content-full">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('requisitions.store_request') }}" method="POST" id="requisition-form">
                @csrf
                
                <h2 class="content-heading pt-0">1. Datos Generales</h2>
                <div class="row form-group mb-4">
                    <label class="col-md-2 col-form-label" for="work_id">Obra <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <select class="js-select2 form-select" id="work_id" name="work_id" style="width: 100%;" required>
                            <option value="">Seleccione una obra...</option>
                            @foreach($works as $work)
                                <option value="{{ $work->work_id }}">{{ $work->location->location_name ?? 'N/A' }} - {{ $work->work_name }} ({{ $work->location->client->client_name ?? 'Sin Cliente' }})</option>
                            @endforeach
                        </select>
                        @error('work_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h2 class="content-heading">2. Selección de Materiales</h2>
                <div class="row form-group mb-4 align-items-end p-3 bg-body-light rounded">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <label class="form-label" for="material_select">Buscar Material</label>
                        <select class="js-select2 form-select" id="material_select" style="width: 100%;">
                            <option value="">Seleccione un material...</option>
                            @foreach($categories as $category)
                                <optgroup label="{{ $category->material_category_name }}">
                                    @foreach($category->materials as $material)
                                        <option value="{{ $material->material_id }}">{{ $material->material_name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label" for="material_quantity">Cantidad</label>
                        <input type="number" class="form-control" id="material_quantity" min="1" placeholder="Ej: 5">
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="add-material-btn" class="btn btn-primary w-100">
                            <i class="fa fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>

                <h2 class="content-heading">3. Lista de Solicitud</h2>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr class="bg-body-dark">
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Material</th>
                                <th class="text-center" style="width: 15%;">Cantidad</th>
                                <th class="text-center" style="width: 100px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="materials-list-body">
                            <!-- Filas dinámicas -->
                        </tbody>
                        <tbody>
                            <tr id="empty-message">
                                <td colspan="4" class="text-center text-muted py-4">No ha agregado ningún material a la lista. Utilice el formulario de arriba para añadir items.</td>
                            </tr>
                        </tbody>
                    </table>
                    @error('materials')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <h2 class="content-heading">4. Información Adicional</h2>
                <div class="row form-group mb-5">
                    <label class="col-md-2 col-form-label" for="comment">Comentarios</label>
                    <div class="col-md-10">
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Añada cualquier observación o detalle adicional reelevante para la requisición (Opcional)...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-12 text-center text-md-end">
                        <button type="submit" id="btn-submit" class="btn btn-lg btn-success">
                            <span id="btn-icon"><i class="fa fa-paper-plane me-1"></i></span> <span id="btn-text">Enviar Solicitud a Supervisor</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
