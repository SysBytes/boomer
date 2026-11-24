@extends('layouts.induction')

@section('content')
@php
    $totalContents = $contents->count();
    $overallPercent = $totalContents > 0 ? round(($completedCount / $totalContents) * 100) : 100;
@endphp

<div class="content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <p class="mb-0"><i class="fa fa-check-circle me-1"></i> {{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <p class="mb-0"><i class="fa fa-exclamation-circle me-1"></i> {{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="block block-rounded mb-4">
        <div class="block-content py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2">
                <div>
                    <h2 class="h4 fw-bold mb-1" style="text-wrap: balance;">
                        <i class="fa-solid fa-graduation-cap text-primary me-2" aria-hidden="true"></i>Inducción Corporativa Obligatoria
                    </h2>
                    <p class="text-muted fs-sm mb-0">
                        Revisa cada material obligatorio en orden secuencial y certifica tu cumplimiento para acceder al sistema.
                    </p>
                </div>
                <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-2">
                    <span class="badge bg-primary fs-sm">
                        {{ $completedCount }} de {{ $totalContents }} contenidos completados
                    </span>
                    <button
                        class="btn btn-sm btn-outline-secondary d-lg-none"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#inductionRouteCollapse"
                        aria-expanded="false"
                        aria-controls="inductionRouteCollapse"
                    >
                        <i class="fa fa-list-ol me-1" aria-hidden="true"></i> Ver ruta
                    </button>
                </div>
            </div>

            <div class="progress" style="height: 10px;" role="progressbar" aria-label="Progreso de inducción" aria-valuenow="{{ $overallPercent }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-success" style="width: {{ $overallPercent }}%;"></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Lista secuencial de pasos (colapsable en móvil, visible desde lg) -->
        <div class="col-12 col-lg-4 order-2 order-lg-1">
            <div id="inductionRouteCollapse" class="collapse d-lg-block">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Ruta de Inducción</h3>
                    </div>
                    <div class="block-content p-0">
                        <div class="list-group list-group-flush">
                            @foreach($contents as $idx => $item)
                                @php
                                    $itemProgress = $progressMap->get($item->id);
                                    $isDone = $itemProgress && $itemProgress->status === 'completed'
                                        && (int) $itemProgress->view_time_seconds >= max(0, (int) $item->required_time_seconds);
                                    $isCurrent = $currentContent && (int) $currentContent->id === (int) $item->id;
                                @endphp
                                <div class="list-group-item {{ $isCurrent ? 'bg-body-light border-start border-primary border-3' : '' }}">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <div>
                                            <div class="fs-xs text-muted mb-1">
                                                Paso {{ $idx + 1 }} · {{ $item->subcategory->name ?? 'General' }}
                                            </div>
                                            <div class="fw-semibold {{ $isDone ? 'text-success' : ($isCurrent ? 'text-primary' : 'text-muted') }}">
                                                {{ $item->title }}
                                            </div>
                                            @if($isDone && $itemProgress->completed_at)
                                                <div class="fs-xs text-success mt-1">
                                                    <i class="fa fa-check-circle me-1" aria-hidden="true"></i>Completado {{ $itemProgress->completed_at->format('d/m/Y H:i') }}
                                                </div>
                                            @elseif($isCurrent)
                                                <div class="fs-xs text-primary mt-1">
                                                    <i class="fa fa-play-circle me-1" aria-hidden="true"></i>En curso
                                                    @if($item->required_time_seconds > 0)
                                                        ({{ $item->required_time_seconds }}s requeridos)
                                                    @endif
                                                </div>
                                            @else
                                                <div class="fs-xs text-muted mt-1">
                                                    <i class="fa fa-lock me-1" aria-hidden="true"></i>Bloqueado hasta completar el anterior
                                                </div>
                                            @endif
                                        </div>
                                        <span class="badge {{ $isDone ? 'bg-success' : ($isCurrent ? 'bg-primary' : 'bg-secondary') }}">
                                            @if($isDone)
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            @elseif($isCurrent)
                                                Actual
                                            @else
                                                <i class="fa fa-lock" aria-hidden="true"></i>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach

                            <div class="list-group-item {{ $allContentsCompleted ? 'bg-body-light border-start border-success border-3' : '' }}">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <div class="fs-xs text-muted mb-1">Paso Final</div>
                                        <div class="fw-semibold {{ $allContentsCompleted ? 'text-success' : 'text-muted' }}">
                                            Certificación y Evidencia
                                        </div>
                                    </div>
                                    <span class="badge {{ $allContentsCompleted ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fa {{ $allContentsCompleted ? 'fa-camera' : 'fa-lock' }}" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área principal: Contenido actual o Certificación final (primero en móvil) -->
        <div class="col-12 col-lg-8 order-1 order-lg-2">
            @if(!$allContentsCompleted && $currentContent)
                @php
                    $requiredSeconds = max(0, (int) $currentContent->required_time_seconds);
                    $initialElapsed = min($requiredSeconds, max(0, (int) ($currentProgress->view_time_seconds ?? 0)));
                    if ($requiredSeconds > 0 && (int) ($currentProgress->view_time_seconds ?? 0) >= $requiredSeconds) {
                        $initialElapsed = (int) $currentProgress->view_time_seconds;
                    } else {
                        $initialElapsed = max(0, (int) ($currentProgress->view_time_seconds ?? 0));
                    }
                    $canCompleteInitially = $initialElapsed >= $requiredSeconds;
                    $remainingInitially = max(0, $requiredSeconds - $initialElapsed);
                    $timerPercent = $requiredSeconds > 0 ? min(100, round(($initialElapsed / $requiredSeconds) * 100)) : 100;
                @endphp

                <div class="block block-rounded mb-4"
                     id="inductionStepContainer"
                     data-required-seconds="{{ $requiredSeconds }}"
                     data-elapsed-seconds="{{ $initialElapsed }}"
                     data-heartbeat-url="{{ route('induction.heartbeat', $currentContent->id) }}"
                     data-is-external="{{ $currentContent->is_external ? '1' : '0' }}"
                     data-token-url="{{ $currentContent->is_external ? '' : route('dashboard.corporate-contents.token', $currentContent->id) }}"
                     data-title="{{ $currentContent->title }}"
                     data-type="{{ $currentContent->type }}">
                    <div class="block-header block-header-default">
                        <div>
                            <span class="badge bg-secondary me-1">Paso {{ $currentStepNumber }} de {{ $totalContents }}</span>
                            @if($currentContent->is_external)
                                <span class="badge bg-warning text-dark"><i class="fa fa-link me-1"></i>Enlace Externo</span>
                            @elseif($currentContent->type === 'pdf')
                                <span class="badge bg-danger"><i class="fa fa-file-pdf me-1"></i>PDF</span>
                            @else
                                <span class="badge bg-info"><i class="fa fa-video me-1"></i>Video</span>
                            @endif
                            <h3 class="block-title d-inline-block ms-1">{{ $currentContent->title }}</h3>
                        </div>
                    </div>

                    <div class="block-content pb-4">
                        <!-- Visor del contenido -->
                        @if($currentContent->is_external)
                            <div class="ratio ratio-16x9 rounded overflow-hidden bg-dark mb-2">
                                <iframe
                                    src="{{ $currentContent->embed_url }}"
                                    title="{{ $currentContent->title }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                    referrerpolicy="strict-origin-when-cross-origin"
                                ></iframe>
                            </div>
                            <div class="fs-xs text-muted">
                                Si el reproductor externo no carga por restricciones del proveedor, puedes
                                <a href="{{ $currentContent->url }}" target="_blank" rel="noopener noreferrer">abrir el enlace en una ventana auxiliar</a>
                                y volver a esta pestaña para completar el tiempo requerido.
                            </div>
                        @else
                            <div class="secure-viewer-standalone secure-viewer-protected" id="inductionViewerContainer">
                                <div class="secure-viewer-header">
                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                        <i class="fa fa-shield-halved text-info"></i>
                                        <h1 id="secureViewerTitle" class="h6 mb-0 text-truncate text-white">{{ $currentContent->title }}</h1>
                                    </div>

                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span id="secureViewerPageIndicator" class="badge bg-secondary d-none">Página 1 de 1</span>

                                        <div id="secureViewerZoomControls" class="btn-group btn-group-sm d-none" role="group" aria-label="Controles de zoom">
                                            <button type="button" class="btn btn-outline-light" id="secureZoomOut" title="Alejar" aria-label="Alejar">
                                                <i class="fa fa-magnifying-glass-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-light" id="secureZoomReset" title="Restablecer zoom">
                                                <span id="secureZoomLabel">100%</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-light" id="secureZoomIn" title="Acercar" aria-label="Acercar">
                                                <i class="fa fa-magnifying-glass-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="secureViewerBody" class="secure-viewer-body">
                                    <div id="secureViewerStage" class="secure-pdf-stage"></div>
                                </div>
                            </div>

                            <div class="secure-print-blocked">
                                La impresión de este documento corporativo no está permitida.
                            </div>
                        @endif

                        <!-- Barra de validación de tiempo y confirmación (después del documento) -->
                        <div class="p-3 rounded border bg-body-light mt-3">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-2">
                                <div>
                                    <span class="fw-semibold" id="timerStatusText">
                                        @if($canCompleteInitially)
                                            ¡Tiempo cumplido! Ya puedes avanzar al siguiente paso.
                                        @else
                                            Permanece en este contenido para habilitar el avance.
                                        @endif
                                    </span>
                                    <span id="timerPauseBadge" class="badge bg-warning text-dark ms-2 d-none">
                                        Pausado (pestaña inactiva)
                                    </span>
                                </div>
                                <div class="fw-bold text-nowrap">
                                    <i class="fa fa-clock me-1 text-primary"></i>
                                    <span id="timerCountdownText">
                                        @if($canCompleteInitially)
                                            00:00
                                        @else
                                            {{ sprintf('%02d:%02d', intdiv($remainingInitially, 60), $remainingInitially % 60) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div id="timerProgressBar" class="progress-bar {{ $canCompleteInitially ? 'bg-success' : 'bg-primary' }}" style="width: {{ $timerPercent }}%;"></div>
                            </div>

                            <form id="completeStepForm" action="{{ route('induction.complete', $currentContent->id) }}" method="POST" class="d-flex justify-content-end">
                                @csrf
                                <input type="hidden" name="view_time_seconds" id="completeViewTimeInput" value="{{ $initialElapsed }}">
                                <button type="submit" id="completeStepBtn" class="btn w-100 w-sm-auto py-2 {{ $canCompleteInitially ? 'btn-success' : 'btn-primary' }}" style="min-height: 44px;" {{ $canCompleteInitially ? '' : 'disabled' }}>
                                    <i class="fa fa-check me-1" aria-hidden="true"></i> Marcar como completado y continuar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Paso Final: Certificación y Captura de Evidencia -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-certificate text-success me-2" aria-hidden="true"></i>Certificación de Cumplimiento y Captura de Evidencia
                        </h3>
                    </div>
                    <div class="block-content pb-4">
                        @include('induction._evidence_capture', [
                            'formAction' => route('induction.certify'),
                            'user' => $user,
                            'introMessage' => 'Has completado todos los contenidos obligatorios. Para habilitar tu acceso completo al sistema, captura una fotografía selfie en tiempo real, dibuja tu firma digital y confirma tu declaración de cumplimiento.',
                            'termsLabel' => 'Confirmo que he leído, visualizado y comprendido todos los documentos y lineamientos corporativos de Boomer Elecomm presentados en esta inducción.',
                            'submitLabel' => 'Finalizar Inducción y Certificar Cumplimiento',
                        ])
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfEl = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfEl ? csrfEl.getAttribute('content') : '';

        // ------------------------------------------------------------------
        // Motor de tiempos y visor secuencial para el contenido obligatorio actual
        // ------------------------------------------------------------------
        const stepEl = document.getElementById('inductionStepContainer');
        if (stepEl) {
            const requiredSeconds = parseInt(stepEl.dataset.requiredSeconds || '0', 10);
            let elapsedSeconds = parseInt(stepEl.dataset.elapsedSeconds || '0', 10);
            const heartbeatUrl = stepEl.dataset.heartbeatUrl;
            const isExternal = stepEl.dataset.isExternal === '1';

            const countdownText = document.getElementById('timerCountdownText');
            const statusText = document.getElementById('timerStatusText');
            const pauseBadge = document.getElementById('timerPauseBadge');
            const progressBar = document.getElementById('timerProgressBar');
            const completeBtn = document.getElementById('completeStepBtn');
            const viewTimeInput = document.getElementById('completeViewTimeInput');

            let unsentSeconds = 0;
            let unlocked = elapsedSeconds >= requiredSeconds;
            let heartbeatInFlight = false;

            if (!isExternal && window.BoomerSecureViewer) {
                const viewerContainer = document.getElementById('inductionViewerContainer');
                window.BoomerSecureViewer.open({
                    tokenUrl: stepEl.dataset.tokenUrl,
                    title: stepEl.dataset.title,
                    type: stepEl.dataset.type,
                    controls: {
                        titleEl: document.getElementById('secureViewerTitle'),
                        zoomControlsEl: document.getElementById('secureViewerZoomControls'),
                        zoomOutBtn: document.getElementById('secureZoomOut'),
                        zoomInBtn: document.getElementById('secureZoomIn'),
                        zoomResetBtn: document.getElementById('secureZoomReset'),
                        zoomLabelEl: document.getElementById('secureZoomLabel'),
                        pageIndicatorEl: document.getElementById('secureViewerPageIndicator'),
                        bodyEl: document.getElementById('secureViewerBody'),
                        stageEl: document.getElementById('secureViewerStage'),
                        activeScopeEl: viewerContainer,
                    },
                });
            }

            function formatDuration(totalSeconds) {
                const mins = Math.floor(totalSeconds / 60);
                const secs = totalSeconds % 60;
                return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
            }

            function refreshTimerUI() {
                const remaining = Math.max(0, requiredSeconds - elapsedSeconds);
                const pct = requiredSeconds > 0 ? Math.min(100, Math.round((elapsedSeconds / requiredSeconds) * 100)) : 100;

                if (countdownText) {
                    countdownText.textContent = formatDuration(remaining);
                }
                if (progressBar) {
                    progressBar.style.width = pct + '%';
                }
                if (viewTimeInput) {
                    viewTimeInput.value = String(elapsedSeconds);
                }
            }

            function unlockCompletion() {
                unlocked = true;
                if (completeBtn) {
                    completeBtn.disabled = false;
                    completeBtn.classList.remove('btn-primary');
                    completeBtn.classList.add('btn-success');
                }
                if (progressBar) {
                    progressBar.classList.remove('bg-primary');
                    progressBar.classList.add('bg-success');
                }
                if (statusText) {
                    statusText.textContent = '¡Tiempo cumplido! Ya puedes avanzar al siguiente paso.';
                }
            }

            async function sendHeartbeat(force) {
                if (!heartbeatUrl || heartbeatInFlight || unsentSeconds <= 0) {
                    return;
                }
                if (!force && unsentSeconds < 5) {
                    return;
                }

                const increment = Math.min(60, unsentSeconds);
                unsentSeconds = Math.max(0, unsentSeconds - increment);
                heartbeatInFlight = true;

                try {
                    const res = await fetch(heartbeatUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        cache: 'no-store',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ increment_seconds: increment }),
                    });

                    if (res.ok) {
                        const data = await res.json();
                        if (typeof data.view_time_seconds === 'number') {
                            elapsedSeconds = Math.max(elapsedSeconds, data.view_time_seconds);
                            refreshTimerUI();
                        }
                        if (data.can_complete) {
                            unlockCompletion();
                        }
                    }
                } catch (_) {
                    unsentSeconds += increment;
                } finally {
                    heartbeatInFlight = false;
                }
            }

            document.addEventListener('visibilitychange', function () {
                if (pauseBadge) {
                    pauseBadge.classList.toggle('d-none', !document.hidden);
                }
            });

            if (requiredSeconds > 0 && !unlocked) {
                const intervalId = window.setInterval(function () {
                    if (document.hidden) {
                        return;
                    }

                    elapsedSeconds++;
                    unsentSeconds++;
                    refreshTimerUI();

                    if (elapsedSeconds >= requiredSeconds) {
                        sendHeartbeat(true);
                        unlockCompletion();
                        window.clearInterval(intervalId);
                    } else if (unsentSeconds >= 5) {
                        sendHeartbeat(false);
                    }
                }, 1000);
            }
        }
    });
</script>
@endsection

@include('dashboard.corporate_contents._secure_viewer_assets')
