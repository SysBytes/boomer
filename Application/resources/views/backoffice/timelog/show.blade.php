@extends('layouts.backoffice')

@section('content')
    <div class="content">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="content-heading mb-0">Detalles de Registro de Horas</h2>
            <a href="{{ route('timelogs.index') }}" class="btn btn-alt-secondary">
                <i class="fa fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <!-- Info del Técnico y Obra -->
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                <div class="row text-center">
                    <div class="col-sm-4 py-3">
                        <div class="fs-1 fw-light text-primary mb-1">
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="fs-4 fw-semibold">{{ $timeLog->user->name ?? 'Técnico Eliminado' }}</div>
                        <div class="text-muted">Usuario</div>
                    </div>
                    <div class="col-sm-4 py-3 border-start border-end">
                        <div class="fs-1 fw-light text-info mb-1">
                            <i class="fa fa-building"></i>
                        </div>
                        <div class="fs-4 fw-semibold">{{ $timeLog->work->work_name ?? 'Obra Eliminada' }}</div>
                        <div class="text-muted">{{ $timeLog->work->client->client_name ?? 'Sin Cliente' }}</div>
                    </div>
                    <div class="col-sm-4 py-3">
                        <div class="fs-1 fw-light text-success mb-1">
                            <i class="fa fa-clock"></i>
                        </div>
                        <div class="fs-4 fw-semibold">{{ $timeLog->hours_worked ?? '-' }}</div>
                        <div class="text-muted">Horas Totales</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Tarjeta de Check-In -->
            <div class="col-md-6">
                <div class="block block-rounded text-center">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-sign-in-alt text-success me-1"></i> Check-In (Entrada)
                        </h3>
                    </div>
                    <div class="block-content">
                        <div class="fs-4 fw-bold mb-1">
                            {{ $timeLog->check_in_time ? $timeLog->check_in_time->format('d/m/Y h:i:s A') : 'N/A' }}
                        </div>

                        @if($timeLog->check_in_photo)
                            <div class="my-4">
                                <a href="{{ asset($timeLog->check_in_photo) }}" target="_blank">
                                    <img src="{{ asset($timeLog->check_in_photo) }}" class="img-fluid rounded" alt="Foto Check-In" style="max-height: 300px; border: 2px solid #e2e8f0; object-fit: cover;">
                                </a>
                            </div>
                        @else
                            <div class="my-4 text-muted">
                                <i class="fa fa-image fa-3x mb-2"></i><br>Sin foto registrada
                            </div>
                        @endif

                        <!-- Comparativa de ubicación -->
                        <div class="p-3 bg-body-light rounded text-start mb-4">
                            <h4 class="h6 mb-2 fw-bold text-uppercase text-muted">Datos de Ubicación</h4>
                            <div class="mb-1">
                                <i class="fa fa-map-marker-alt text-danger me-1"></i>
                                <strong>Ubicación tomada:</strong> <a href="https://maps.google.com/?q={{ $timeLog->check_in_lat }},{{ $timeLog->check_in_lng }}" target="_blank">{{ $timeLog->check_in_lat }}, {{ $timeLog->check_in_lng }}</a>
                            </div>
                            @if($timeLog->work && $timeLog->work->location && $timeLog->work->location->location_lat && $timeLog->work->location->location_lng)
                                <p class="mb-0">
                                    <i class="fa fa-building text-primary me-1"></i>
                                    <strong>Ubicación Proyecto:</strong> <a href="https://maps.google.com/?q={{ $timeLog->work->location->location_lat }},{{ $timeLog->work->location->location_lng }}"
                                        target="_blank">{{ $timeLog->work->location->location_lat }}, {{ $timeLog->work->location->location_lng }}</a>
                                </p>

                                @if(!is_null($inDistance))
                                    <hr class="my-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-semibold">Distancia al proyecto:</span>
                                        @if($inDistance <= 500)
                                            <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i> Aprox. {{ $inDistance }} metros</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fa fa-exclamation-triangle me-1"></i> Aprox. {{ $inDistance }} metros (Lejos)</span>
                                        @endif
                                    </div>
                                @endif
                            @else
                                <div class="text-warning mt-2 fs-sm">
                                    <i class="fa fa-exclamation-circle"></i> El proyecto no tiene coordenadas registradas para comparar.
                                </div>
                            @endif
                        </div>

                        @if($timeLog->check_in_registered_by)
                            <div class="p-3 bg-body-light rounded text-start mb-4 border border-info">
                                <h4 class="h6 mb-2 fw-bold text-uppercase text-info"><i class="fa fa-clipboard-check me-1"></i> Registro Manual</h4>
                                <div class="mb-1">
                                    <i class="fa fa-user-shield text-dark me-1"></i>
                                    <strong>Registrado por:</strong> {{ $timeLog->checkInAdmin->name ?? 'Desconocido' }}
                                </div>
                                <div class="mb-1 mt-2">
                                    <i class="fa fa-comment-dots text-dark me-1"></i>
                                    <strong>Observación:</strong>
                                    <p class="mb-0 mt-1 fst-italic text-muted">{{ $timeLog->check_in_observation }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Check-Out -->
            <div class="col-md-6">
                <div class="block block-rounded text-center">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-sign-out-alt text-danger me-1"></i> Check-Out (Salida)
                        </h3>
                    </div>
                    <div class="block-content">
                        @if($timeLog->check_out_time)
                            <div class="fs-4 fw-bold mb-1">
                                {{ $timeLog->check_out_time->format('d/m/Y h:i:s A') }}
                            </div>

                            @if($timeLog->check_out_photo)
                                <div class="my-4">
                                    <a href="{{ asset($timeLog->check_out_photo) }}" target="_blank">
                                        <img src="{{ asset($timeLog->check_out_photo) }}" class="img-fluid rounded" alt="Foto Check-Out" style="max-height: 300px; border: 2px solid #e2e8f0; object-fit: cover;">
                                    </a>
                                </div>
                            @else
                                <div class="my-4 text-muted">
                                    <i class="fa fa-image fa-3x mb-2"></i><br>Sin foto registrada
                                </div>
                            @endif

                            <!-- Comparativa de ubicación -->
                            <div class="p-3 bg-body-light rounded text-start mb-4">
                                <h4 class="h6 mb-2 fw-bold text-uppercase text-muted">Datos de Ubicación</h4>
                                <div class="mb-1">
                                    <i class="fa fa-map-marker-alt text-danger me-1"></i>
                                    <strong>Ubicación tomada:</strong> <a href="https://maps.google.com/?q={{ $timeLog->check_out_lat }},{{ $timeLog->check_out_lng }}" target="_blank">{{ $timeLog->check_out_lat }}, {{ $timeLog->check_out_lng }}</a>
                                </div>
                                @if($timeLog->work && $timeLog->work->location && $timeLog->work->location->location_lat && $timeLog->work->location->location_lng)
                                    <p class="mb-0">
                                        <i class="fa fa-building text-primary me-1"></i>
                                        <strong>Ubicación Proyecto:</strong> <a href="https://maps.google.com/?q={{ $timeLog->work->location->location_lat }},{{ $timeLog->work->location->location_lng }}"
                                            target="_blank">{{ $timeLog->work->location->location_lat }}, {{ $timeLog->work->location->location_lng }}</a>
                                    </p>

                                    @if(!is_null($outDistance))
                                        <hr class="my-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-semibold">Distancia al proyecto:</span>
                                            @if($outDistance <= 500)
                                                <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i> Aprox. {{ $outDistance }} metros</span>
                                            @else
                                                <span class="badge bg-danger"><i class="fa fa-exclamation-triangle me-1"></i> Aprox. {{ $outDistance }} metros (Lejos)</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="text-warning mt-2 fs-sm">
                                        <i class="fa fa-exclamation-circle"></i> El proyecto no tiene coordenadas registradas para comparar.
                                    </div>
                                @endif
                            </div>

                            @if($timeLog->check_out_registered_by)
                                <div class="p-3 bg-body-light rounded text-start mb-4 border border-info">
                                    <h4 class="h6 mb-2 fw-bold text-uppercase text-info"><i class="fa fa-clipboard-check me-1"></i> Registro Manual</h4>
                                    <div class="mb-1">
                                        <i class="fa fa-user-shield text-dark me-1"></i>
                                        <strong>Registrado por:</strong> {{ $timeLog->checkOutAdmin->name ?? 'Desconocido' }}
                                    </div>
                                    <div class="mb-1 mt-2">
                                        <i class="fa fa-comment-dots text-dark me-1"></i>
                                        <strong>Observación:</strong>
                                        <p class="mb-0 mt-1 fst-italic text-muted">{{ $timeLog->check_out_observation }}</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="fs-4 fw-bold mb-1 text-muted">En Curso</div>
                            <div class="my-5 py-4 text-muted">
                                <i class="fa fa-hourglass-half fa-3x mb-3 text-warning"></i>
                                <p>El turno sigue abierto, aún no se registra la salida.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection