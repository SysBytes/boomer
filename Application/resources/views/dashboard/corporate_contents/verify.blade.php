@extends('layouts.dashboard')

@section('content')
    @php
        $requiredSeconds = max(0, (int) $content->required_time_seconds);
        $elapsedSeconds = max(0, (int) ($progress->view_time_seconds ?? 0));
        $canCompleteInitially = $elapsedSeconds >= $requiredSeconds;
        $remainingInitially = max(0, $requiredSeconds - $elapsedSeconds);
        $timerPercent = $requiredSeconds > 0 ? min(100, round(($elapsedSeconds / $requiredSeconds) * 100)) : 100;
    @endphp

    <div class="content mx-auto" style="max-width: 960px;">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
            <div>
                <div class="fs-xs text-muted mb-1">
                    {{ $content->subcategory->category->name ?? 'Corporativo' }} / {{ $content->subcategory->name ?? 'General' }}
                </div>
                <h2 class="h4 fw-bold mb-1" style="text-wrap: balance;">{{ $content->title }}</h2>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @if($content->is_external)
                        <span class="badge bg-warning text-dark"><i class="fa fa-link me-1" aria-hidden="true"></i>Enlace</span>
                    @elseif($content->type === 'pdf')
                        <span class="badge bg-danger"><i class="fa fa-file-pdf me-1" aria-hidden="true"></i>PDF</span>
                    @else
                        <span class="badge bg-info"><i class="fa fa-video me-1" aria-hidden="true"></i>Video</span>
                    @endif
                    <span class="badge bg-primary">Primera visualización · Validación requerida</span>
                </div>
            </div>

            <div class="flex-shrink-0">
                <a href="{{ route('dashboard.corporate-contents.index') }}" class="btn btn-sm btn-outline-secondary w-100 w-sm-auto py-2 px-3">
                    <i class="fa fa-arrow-left me-1" aria-hidden="true"></i> Volver a la biblioteca
                </a>
            </div>
        </div>

        @include('backoffice.induction._alerts')

        @if($requiredSeconds > 0)
            <div
                id="verifyStepTimer"
                class="block block-rounded mb-3 border"
                data-required-seconds="{{ $requiredSeconds }}"
                data-elapsed-seconds="{{ $elapsedSeconds }}"
                data-heartbeat-url="{{ route('dashboard.corporate-contents.heartbeat', $content->id) }}"
            >
                <div class="block-content py-3">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-2">
                        <div>
                            <span id="verifyTimerStatusText" class="fw-semibold fs-sm">
                                @if($canCompleteInitially)
                                    ¡Tiempo mínimo cumplido! Ya puedes enviar tu selfie y firma abajo.
                                @else
                                    Revisa el contenido hasta completar el tiempo requerido para habilitar la validación.
                                @endif
                            </span>
                            <span id="verifyTimerPauseBadge" class="badge bg-warning text-dark ms-1 d-none">
                                Pausado (pestaña inactiva)
                            </span>
                        </div>
                        <div class="fw-bold text-nowrap">
                            <i class="fa fa-clock text-primary me-1" aria-hidden="true"></i>
                            <span id="verifyTimerCountdown">
                                @if($canCompleteInitially)
                                    00:00
                                @else
                                    {{ sprintf('%02d:%02d', intdiv($remainingInitially, 60), $remainingInitially % 60) }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="progress" style="height: 8px;" role="progressbar" aria-label="Tiempo mínimo de visualización" aria-valuenow="{{ $timerPercent }}" aria-valuemin="0" aria-valuemax="100">
                        <div id="verifyTimerProgressBar" class="progress-bar {{ $canCompleteInitially ? 'bg-success' : 'bg-primary' }}" style="width: {{ $timerPercent }}%;"></div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Paso 1: Visor de contenido -->
        <div class="block block-rounded mb-4">
            <div class="block-header block-header-default">
                <h3 class="block-title fs-sm fw-semibold">
                    <span class="badge bg-primary me-2">Paso 1</span>Visualización del Contenido
                </h3>
            </div>
            <div class="block-content pb-4">
                @if($content->is_external)
                    <div class="ratio ratio-16x9 rounded overflow-hidden bg-dark mb-2">
                        <iframe
                            src="{{ $content->embed_url }}"
                            title="{{ $content->title }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                            referrerpolicy="strict-origin-when-cross-origin"
                        ></iframe>
                    </div>
                    <div class="fs-xs text-muted">
                        Si el reproductor externo no carga por restricciones del proveedor, puedes
                        <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer">abrir el enlace en una ventana auxiliar</a>
                        y regresar aquí para completar tu selfie y firma.
                    </div>
                @else
                    <div class="secure-viewer-standalone secure-viewer-protected" id="verifyViewerContainer">
                        <div class="secure-viewer-header">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <i class="fa fa-shield-halved text-info" aria-hidden="true"></i>
                                <span id="secureViewerTitle" class="h6 mb-0 text-truncate text-white">{{ $content->title }}</span>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span id="secureViewerPageIndicator" class="badge bg-secondary d-none">Página 1 de 1</span>

                                <div id="secureViewerZoomControls" class="btn-group btn-group-sm d-none" role="group" aria-label="Controles de zoom">
                                    <button type="button" class="btn btn-outline-light" id="secureZoomOut" title="Alejar" aria-label="Alejar">
                                        <i class="fa fa-magnifying-glass-minus" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-light" id="secureZoomReset" title="Restablecer zoom">
                                        <span id="secureZoomLabel">100%</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-light" id="secureZoomIn" title="Acercar" aria-label="Acercar">
                                        <i class="fa fa-magnifying-glass-plus" aria-hidden="true"></i>
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
            </div>
        </div>

        <!-- Paso 2: Validación de primera lectura (Selfie + Firma Digital) -->
        <div class="block block-rounded mb-4">
            <div class="block-header block-header-default">
                <h3 class="block-title fs-sm fw-semibold">
                    <span class="badge bg-success me-2">Paso 2</span>Validación con Selfie y Firma Digital
                </h3>
            </div>
            <div class="block-content pb-4">
                @include('induction._evidence_capture', [
                    'formAction' => route('dashboard.corporate-contents.verify.store', $content->id),
                    'user' => $user,
                    'introMessage' => 'Como es la primera vez que visualizas este contenido, tómate una selfie y dibuja tu firma digital para registrar tu lectura. Las próximas veces podrás abrirlo directamente en el visor.',
                    'termsLabel' => 'Confirmo que he leído, visualizado y comprendido el contenido corporativo "' . $content->title . '".',
                    'submitLabel' => 'Confirmar Lectura y Guardar Evidencia',
                    'initialLocked' => $requiredSeconds > 0 && !$canCompleteInitially,
                    'lockedHint' => 'Debes completar el tiempo mínimo de lectura arriba antes de enviar tu validación.',
                    'viewTimeSeconds' => $elapsedSeconds,
                ])
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfEl = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfEl ? csrfEl.getAttribute('content') : '';

            @if(!$content->is_external)
                const viewerContainer = document.getElementById('verifyViewerContainer');
                if (viewerContainer && window.BoomerSecureViewer) {
                    window.BoomerSecureViewer.open({
                        tokenUrl: "{{ route('dashboard.corporate-contents.token', $content->id) }}",
                        viewedUrl: null,
                        title: @json($content->title),
                        type: @json($content->type),
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
            @endif

            const timerBox = document.getElementById('verifyStepTimer');
            if (!timerBox) {
                return;
            }

            const requiredSeconds = parseInt(timerBox.dataset.requiredSeconds || '0', 10);
            let elapsedSeconds = parseInt(timerBox.dataset.elapsedSeconds || '0', 10);
            const heartbeatUrl = timerBox.dataset.heartbeatUrl;

            const countdownText = document.getElementById('verifyTimerCountdown');
            const statusText = document.getElementById('verifyTimerStatusText');
            const pauseBadge = document.getElementById('verifyTimerPauseBadge');
            const progressBar = document.getElementById('verifyTimerProgressBar');

            let unsentSeconds = 0;
            let unlocked = elapsedSeconds >= requiredSeconds;
            let heartbeatInFlight = false;

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
                if (window.BoomerEvidenceForm) {
                    window.BoomerEvidenceForm.setViewTime(elapsedSeconds);
                }
            }

            function unlockValidation() {
                unlocked = true;
                if (progressBar) {
                    progressBar.classList.remove('bg-primary');
                    progressBar.classList.add('bg-success');
                }
                if (statusText) {
                    statusText.textContent = '¡Tiempo mínimo cumplido! Ya puedes enviar tu selfie y firma abajo.';
                }
                if (window.BoomerEvidenceForm) {
                    window.BoomerEvidenceForm.unlock();
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
                            unlockValidation();
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
                        unlockValidation();
                        window.clearInterval(intervalId);
                    } else if (unsentSeconds >= 5) {
                        sendHeartbeat(false);
                    }
                }, 1000);
            }
        });
    </script>
@endsection

@include('dashboard.corporate_contents._secure_viewer_assets')
