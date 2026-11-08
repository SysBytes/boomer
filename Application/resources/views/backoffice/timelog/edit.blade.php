@extends('layouts.backoffice')

@section('title', 'Editar Registro de Horas')

@section('content')
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="content-heading mb-0">Editar Registro de Horas</h2>
            <a href="{{ route('timelogs.index') }}" class="btn btn-sm btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Volver al Historial
            </a>
        </div>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <i class="fas fa-edit me-1"></i> Modificando ID: {{ $timeLog->time_log_id }}
                </h3>
            </div>

            <div class="block-content block-content-full">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info d-flex align-items-center justify-content-between">
                    <div>
                        <strong><i class="fa fa-user me-1"></i> Usuario:</strong> {{ $timeLog->user->name ?? 'N/A' }}
                    </div>
                    <div>
                        <strong><i class="fa fa-building me-1"></i> Proyecto:</strong> {{ $timeLog->work->location->location_name ?? 'N/A' }} - {{ $timeLog->work->work_name ?? 'N/A' }}
                    </div>
                </div>

                <form action="{{ route('timelogs.update', $timeLog->time_log_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="check_in_time">Fecha y Hora Check-In <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="check_in_time" id="check_in_time" class="form-control" 
                                value="{{ $timeLog->check_in_time ? \Carbon\Carbon::parse($timeLog->check_in_time)->format('Y-m-d\TH:i') : '' }}" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="check_out_time">Fecha y Hora Check-Out</label>
                            <input type="datetime-local" name="check_out_time" id="check_out_time" class="form-control" 
                                value="{{ $timeLog->check_out_time ? \Carbon\Carbon::parse($timeLog->check_out_time)->format('Y-m-d\TH:i') : '' }}">
                            <small class="text-muted">Dejar vacío si el registro sigue en curso. Si asigna una fecha, se recalcularán o eliminarán las horas extras automáticamente según corresponda.</small>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar Cambios</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
