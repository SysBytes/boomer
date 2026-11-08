@extends('layouts.backoffice')

@section('content')
    <div class="content">
        <h2 class="content-heading">Importar Materiales desde CSV</h2>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Cargar Archivo</h3>
            </div>
            <div class="block-content">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p class="text-muted fs-sm mb-3">
                    El archivo CSV debe contener exactamente <strong>4 columnas</strong> en este orden:
                    <code>Categoría, Material, Unidad de Medida, Stock</code>. La primera fila debe ser el encabezado.
                </p>

                <form action="{{ route('import.material.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row mb-4">
                        <label class="col-md-2 col-form-label" for="csv_file">Archivo CSV <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="file" id="csv_file" name="csv_file" class="form-control" accept=".csv" required>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-md-8 text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload mr-1"></i> Procesar Archivo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($inserted))
            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="block block-rounded text-center border-success" style="border: 2px solid;">
                        <div class="block-content py-4">
                            <p class="fs-1 fw-bold text-success mb-1">{{ count($inserted) }}</p>
                            <p class="text-muted mb-0">Materiales Insertados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block block-rounded text-center border-warning" style="border: 2px solid;">
                        <div class="block-content py-4">
                            <p class="fs-1 fw-bold text-warning mb-1">{{ count($updated) }}</p>
                            <p class="text-muted mb-0">Materiales Actualizados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block block-rounded text-center border-secondary" style="border: 2px solid;">
                        <div class="block-content py-4">
                            <p class="fs-1 fw-bold text-secondary mb-1">{{ $skipped_rows }}</p>
                            <p class="text-muted mb-0">Filas Omitidas</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inserted Table --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default bg-success-light">
                    <h3 class="block-title"><i class="fa fa-plus-circle text-success me-1"></i> Materiales Insertados</h3>
                    <div class="block-options">
                        <span class="badge bg-success">{{ count($inserted) }} registros</span>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    @if(count($inserted) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Material</th>
                                        <th>Unidad</th>
                                        <th class="text-center" style="width: 10%;">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inserted as $row)
                                        <tr>
                                            <td>{{ $row['category'] }}</td>
                                            <td>{{ $row['material'] }}</td>
                                            <td>{{ $row['unit'] }}</td>
                                            <td class="text-center fw-bold text-success">{{ $row['stock'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4 mb-0">No se insertó ningún material nuevo.</p>
                    @endif
                </div>
            </div>

            {{-- Updated Table --}}
            <div class="block block-rounded mt-2">
                <div class="block-header block-header-default bg-warning-light">
                    <h3 class="block-title"><i class="fa fa-pencil-alt text-warning me-1"></i> Materiales Actualizados</h3>
                    <div class="block-options">
                        <span class="badge bg-warning text-dark">{{ count($updated) }} registros</span>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    @if(count($updated) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Material</th>
                                        <th>Unidad</th>
                                        <th class="text-center" style="width: 12%;">Stock Anterior</th>
                                        <th class="text-center" style="width: 12%;">Stock Nuevo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($updated as $row)
                                        <tr>
                                            <td>{{ $row['category'] }}</td>
                                            <td>{{ $row['material'] }}</td>
                                            <td>{{ $row['unit'] }}</td>
                                            <td class="text-center text-muted">{{ $row['old_stock'] }}</td>
                                            <td class="text-center fw-bold text-warning">{{ $row['new_stock'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4 mb-0">No se actualizó ningún material (el stock del CSV no supera al stock actual en todos los casos).</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection