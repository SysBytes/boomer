@php
    $targetUser = $user ?? Auth::user();
    $introMessage = $introMessage ?? 'Captura tu selfie en tiempo real, dibuja tu firma digital y confirma tu declaración.';
    $termsLabel = $termsLabel ?? 'Confirmo que he leído, visualizado y comprendido el material corporativo presentado.';
    $submitLabel = $submitLabel ?? 'Confirmar y Guardar Evidencia';
    $initialLocked = $initialLocked ?? false;
    $lockedHint = $lockedHint ?? 'Espera a que finalice el temporizador de lectura para habilitar el envío.';
@endphp

<style>
    .evidence-camera-frame {
        aspect-ratio: 4 / 3;
        max-height: 55dvh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .evidence-camera-frame video,
    .evidence-camera-frame img {
        width: 100%;
        height: 100%;
        max-height: 55dvh;
    }

    .signature-pad-box {
        position: relative;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        background: #ffffff;
        overflow: hidden;
    }

    .signature-pad-baseline {
        position: absolute;
        left: 1rem;
        right: 1rem;
        bottom: 2.25rem;
        border-bottom: 1px dashed #cbd5e1;
        pointer-events: none;
    }

    @media (pointer: coarse) {
        #inductionCertifyForm button,
        #inductionCertifyForm .evidence-terms-card {
            min-block-size: 44px;
        }
    }
</style>

<div class="alert alert-info mb-4 fs-sm">
    <i class="fa fa-info-circle me-1" aria-hidden="true"></i>
    {{ $introMessage }}
</div>

<form id="inductionCertifyForm" action="{{ $formAction }}" method="POST">
    @csrf
    @if(isset($viewTimeSeconds) && $viewTimeSeconds !== null)
        <input type="hidden" name="view_time_seconds" id="evidenceViewTimeInput" value="{{ (int) $viewTimeSeconds }}">
    @endif
    <input type="hidden" name="evidence_photo_base64" id="evidencePhotoBase64" value="{{ old('evidence_photo_base64') }}">
    <input type="hidden" name="signature_base64" id="signatureBase64" value="{{ old('signature_base64') }}">

    <!-- Paso A: Captura de Selfie en tiempo real -->
    <div class="border rounded p-3 bg-body-light mb-4">
        <div class="row g-3 g-md-4 align-items-center">
            <div class="col-12 col-md-7">
                <div class="border rounded bg-white p-2 text-center evidence-camera-frame">
                    <div id="cameraIdleBox" class="px-3 py-4 text-muted">
                        <i class="fa fa-camera fa-2x mb-2 d-block mx-auto" aria-hidden="true"></i>
                        <p class="mb-0 fs-sm">Presiona <strong>"Activar Cámara"</strong> para tomar tu selfie en tiempo real.</p>
                    </div>

                    <video id="inductionWebcam" autoplay playsinline muted class="rounded bg-dark d-none" style="object-fit: cover;"></video>
                    <canvas id="inductionWebcamCanvas" class="d-none"></canvas>
                    <img id="inductionSnapshotImg" class="rounded d-none" style="object-fit: contain;" alt="Fotografía selfie de evidencia">
                </div>
            </div>

            <div class="col-12 col-md-5 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-semibold mb-2">
                        <i class="fa fa-user-check text-primary me-1" aria-hidden="true"></i>Captura de Selfie
                        <span class="badge bg-danger fw-normal ms-1">Obligatorio</span>
                    </h5>
                    <p id="cameraStatusMsg" class="text-muted fs-sm mb-3" role="status">
                        Se solicitará permiso al navegador para usar tu cámara frontal en tiempo real.
                    </p>

                    <div class="d-grid gap-2">
                        <button type="button" id="startCameraBtn" class="btn btn-primary py-2">
                            <i class="fa fa-video me-1" aria-hidden="true"></i> Activar Cámara
                        </button>
                        <button type="button" id="capturePhotoBtn" class="btn btn-success py-2 d-none">
                            <i class="fa fa-camera me-1" aria-hidden="true"></i> Capturar Selfie
                        </button>
                        <button type="button" id="retakePhotoBtn" class="btn btn-outline-secondary py-2 d-none">
                            <i class="fa fa-rotate-left me-1" aria-hidden="true"></i> Repetir Selfie
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aviso si no se concedió acceso o no se detecta cámara -->
    <div id="cameraErrorBox" class="alert alert-warning mb-4 d-none" role="alert">
        <h6 class="fw-semibold mb-1">
            <i class="fa fa-triangle-exclamation me-1" aria-hidden="true"></i>La selfie en tiempo real desde la cámara es obligatoria
        </h6>
        <p id="cameraErrorReason" class="fs-sm mb-2">
            Habilita el permiso de cámara en tu navegador, asegúrate de estar bajo HTTPS y verifica que tu dispositivo cuente con cámara frontal.
        </p>
        <button type="button" id="retryCameraBtn" class="btn btn-sm btn-warning">
            <i class="fa fa-rotate-right me-1" aria-hidden="true"></i> Reintentar activar cámara
        </button>
    </div>

    <!-- Paso B: Firma Digital (Obligatorio) -->
    <div class="border rounded p-3 bg-body-light mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h5 id="signaturePadLabel" class="fw-semibold mb-0">
                <i class="fa fa-signature text-primary me-1" aria-hidden="true"></i>Firma Digital
                <span class="badge bg-danger fw-normal ms-1">Obligatorio</span>
            </h5>
            <button type="button" id="clearSignatureBtn" class="btn btn-sm btn-outline-secondary px-3" disabled>
                <i class="fa fa-eraser me-1" aria-hidden="true"></i> Limpiar firma
            </button>
        </div>

        <p id="signaturePadHint" class="fs-sm text-muted mb-2">
            Dibuja tu firma dentro del recuadro usando tu dedo, lápiz táctil o mouse.
        </p>

        <div class="signature-pad-box">
            <canvas
                id="signaturePad"
                role="img"
                aria-labelledby="signaturePadLabel"
                aria-describedby="signaturePadHint"
                class="d-block w-100"
                style="height: 190px; touch-action: none; cursor: crosshair;"
            ></canvas>
            <div class="signature-pad-baseline" aria-hidden="true"></div>
            <span
                id="signaturePadPlaceholder"
                class="position-absolute top-50 start-50 translate-middle text-muted fs-sm user-select-none pe-none"
                aria-hidden="true"
            >Firme aquí</span>
        </div>

        <p id="signatureStatusMsg" class="fs-xs text-muted mt-2 mb-0" role="status">
            Pendiente de firma digital (obligatoria).
        </p>
    </div>

    <div id="evidenceValidationError" class="alert alert-danger d-none" role="alert"></div>

    <div id="evidenceLockBanner" class="alert alert-warning {{ $initialLocked ? '' : 'd-none' }}" role="status">
        <i class="fa fa-hourglass-half me-1" aria-hidden="true"></i>
        <span>{{ $lockedHint }}</span>
    </div>

    <!-- Declaración de cumplimiento en tarjeta táctil -->
    <label for="accept_terms" class="d-flex align-items-start gap-3 border rounded p-3 bg-white mb-4 evidence-terms-card" style="cursor: pointer;">
        <input class="form-check-input flex-shrink-0 mt-1" style="width: 1.35rem; height: 1.35rem;" type="checkbox" id="accept_terms" name="accept_terms" value="1" {{ old('accept_terms') ? 'checked' : '' }} required>
        <span class="fw-semibold fs-sm">{{ $termsLabel }}</span>
    </label>

    <button type="submit" id="certifySubmitBtn" class="btn btn-success btn-lg w-100 py-3" {{ $initialLocked ? 'disabled' : '' }}>
        <i class="fa fa-check-double me-1" aria-hidden="true"></i> {{ $submitLabel }}
    </button>
</form>

<script>
    (function () {
        document.addEventListener('DOMContentLoaded', function () {
            const certifyForm = document.getElementById('inductionCertifyForm');
            if (!certifyForm || certifyForm.dataset.initialized === '1') {
                return;
            }
            certifyForm.dataset.initialized = '1';

            const videoEl = document.getElementById('inductionWebcam');
            const canvasEl = document.getElementById('inductionWebcamCanvas');
            const snapshotImg = document.getElementById('inductionSnapshotImg');
            const idleBox = document.getElementById('cameraIdleBox');
            const base64Input = document.getElementById('evidencePhotoBase64');
            const startBtn = document.getElementById('startCameraBtn');
            const captureBtn = document.getElementById('capturePhotoBtn');
            const retakeBtn = document.getElementById('retakePhotoBtn');
            const retryCameraBtn = document.getElementById('retryCameraBtn');
            const cameraErrorBox = document.getElementById('cameraErrorBox');
            const cameraErrorReason = document.getElementById('cameraErrorReason');
            const statusMsg = document.getElementById('cameraStatusMsg');
            const validationErrorBox = document.getElementById('evidenceValidationError');
            const lockBanner = document.getElementById('evidenceLockBanner');
            const viewTimeHiddenInput = document.getElementById('evidenceViewTimeInput');
            const submitBtn = document.getElementById('certifySubmitBtn');

            const signatureCanvas = document.getElementById('signaturePad');
            const signatureBase64Input = document.getElementById('signatureBase64');
            const clearSignatureBtn = document.getElementById('clearSignatureBtn');
            const signaturePlaceholder = document.getElementById('signaturePadPlaceholder');
            const signatureStatusMsg = document.getElementById('signatureStatusMsg');

            let isLocked = {{ $initialLocked ? 'true' : 'false' }};
            let mediaStream = null;

            const sigCtx = signatureCanvas ? signatureCanvas.getContext('2d') : null;
            let hasSignature = false;
            let isDrawing = false;
            let lastPoint = null;
            let lastCanvasWidth = 0;

            window.BoomerEvidenceForm = {
                unlock: function () {
                    isLocked = false;
                    if (lockBanner) {
                        lockBanner.classList.add('d-none');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                },
                setViewTime: function (seconds) {
                    if (viewTimeHiddenInput) {
                        viewTimeHiddenInput.value = String(seconds);
                    }
                },
            };

            function fillCanvasWhite() {
                if (!sigCtx || !signatureCanvas) {
                    return;
                }
                sigCtx.save();
                sigCtx.setTransform(1, 0, 0, 1, 0, 0);
                sigCtx.fillStyle = '#ffffff';
                sigCtx.fillRect(0, 0, signatureCanvas.width, signatureCanvas.height);
                sigCtx.restore();
            }

            function setSignatureRecordedState(recorded) {
                hasSignature = recorded;
                if (clearSignatureBtn) {
                    clearSignatureBtn.disabled = !recorded;
                }
                if (signaturePlaceholder) {
                    signaturePlaceholder.classList.toggle('d-none', recorded);
                }
                if (signatureStatusMsg) {
                    signatureStatusMsg.textContent = recorded
                        ? 'Firma digital registrada.'
                        : 'Pendiente de firma digital (obligatoria).';
                }
                if (!recorded && signatureBase64Input) {
                    signatureBase64Input.value = '';
                }
            }

            function setupCanvas(preserveDrawing) {
                if (!signatureCanvas || !sigCtx) {
                    return;
                }
                const rect = signatureCanvas.getBoundingClientRect();
                if (rect.width <= 0 || rect.height <= 0) {
                    return;
                }

                const snapshot = preserveDrawing && hasSignature && signatureCanvas.width > 0 && signatureCanvas.height > 0
                    ? signatureCanvas.toDataURL('image/png')
                    : null;

                const dpr = Math.min(window.devicePixelRatio || 1, 2);
                signatureCanvas.width = Math.round(rect.width * dpr);
                signatureCanvas.height = Math.round(rect.height * dpr);
                sigCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
                sigCtx.strokeStyle = '#0f172a';
                sigCtx.fillStyle = '#0f172a';
                sigCtx.lineWidth = 2.4;
                sigCtx.lineCap = 'round';
                sigCtx.lineJoin = 'round';

                fillCanvasWhite();
                lastCanvasWidth = rect.width;

                if (snapshot) {
                    const img = new Image();
                    img.onload = function () {
                        sigCtx.drawImage(img, 0, 0, rect.width, rect.height);
                    };
                    img.src = snapshot;
                }
            }

            function getCanvasPoint(event) {
                const rect = signatureCanvas.getBoundingClientRect();
                return {
                    x: event.clientX - rect.left,
                    y: event.clientY - rect.top,
                };
            }

            if (signatureCanvas && sigCtx) {
                setupCanvas(false);

                const initialSignature = signatureBase64Input ? signatureBase64Input.value.trim() : '';
                if (initialSignature.startsWith('data:image/')) {
                    const initialImg = new Image();
                    initialImg.onload = function () {
                        const rect = signatureCanvas.getBoundingClientRect();
                        if (rect.width > 0 && rect.height > 0) {
                            fillCanvasWhite();
                            sigCtx.drawImage(initialImg, 0, 0, rect.width, rect.height);
                            setSignatureRecordedState(true);
                        }
                    };
                    initialImg.src = initialSignature;
                }

                signatureCanvas.addEventListener('pointerdown', function (event) {
                    if (event.pointerType === 'mouse' && event.button !== 0) {
                        return;
                    }
                    event.preventDefault();
                    try {
                        signatureCanvas.setPointerCapture(event.pointerId);
                    } catch (_) {
                        // Ignorar en navegadores que no soportan setPointerCapture en canvas
                    }

                    isDrawing = true;
                    lastPoint = getCanvasPoint(event);
                    sigCtx.beginPath();
                    sigCtx.arc(lastPoint.x, lastPoint.y, 1.2, 0, Math.PI * 2);
                    sigCtx.fill();

                    if (!hasSignature) {
                        setSignatureRecordedState(true);
                    }
                });

                signatureCanvas.addEventListener('pointermove', function (event) {
                    if (!isDrawing || !lastPoint) {
                        return;
                    }
                    event.preventDefault();
                    const coalesced = typeof event.getCoalescedEvents === 'function' ? event.getCoalescedEvents() : [event];
                    const pointsList = coalesced && coalesced.length ? coalesced : [event];

                    sigCtx.beginPath();
                    sigCtx.moveTo(lastPoint.x, lastPoint.y);
                    for (let i = 0; i < pointsList.length; i++) {
                        const point = getCanvasPoint(pointsList[i]);
                        sigCtx.lineTo(point.x, point.y);
                        lastPoint = point;
                    }
                    sigCtx.stroke();
                });

                function finishSignatureStroke() {
                    if (!isDrawing) {
                        return;
                    }
                    isDrawing = false;
                    lastPoint = null;
                    if (signatureBase64Input && hasSignature) {
                        signatureBase64Input.value = signatureCanvas.toDataURL('image/png');
                    }
                }

                signatureCanvas.addEventListener('pointerup', finishSignatureStroke);
                signatureCanvas.addEventListener('pointercancel', finishSignatureStroke);

                if (clearSignatureBtn) {
                    clearSignatureBtn.addEventListener('click', function () {
                        fillCanvasWhite();
                        setSignatureRecordedState(false);
                    });
                }

                let resizeTimer = null;
                window.addEventListener('resize', function () {
                    window.clearTimeout(resizeTimer);
                    resizeTimer = window.setTimeout(function () {
                        const rect = signatureCanvas.getBoundingClientRect();
                        if (Math.abs(rect.width - lastCanvasWidth) > 2) {
                            setupCanvas(true);
                        }
                    }, 120);
                });
            }

            function stopCamera() {
                if (mediaStream) {
                    mediaStream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                    mediaStream = null;
                }
                if (videoEl) {
                    videoEl.srcObject = null;
                }
            }

            function showCameraError(reasonText) {
                if (cameraErrorBox) {
                    cameraErrorBox.classList.remove('d-none');
                }
                if (cameraErrorReason && reasonText) {
                    cameraErrorReason.textContent = reasonText;
                }
            }

            function hideCameraError() {
                if (cameraErrorBox) {
                    cameraErrorBox.classList.add('d-none');
                }
            }

            function showSnapshotPreview(srcUrl) {
                if (idleBox) idleBox.classList.add('d-none');
                if (videoEl) videoEl.classList.add('d-none');
                if (snapshotImg) {
                    snapshotImg.src = srcUrl;
                    snapshotImg.classList.remove('d-none');
                }
                if (captureBtn) captureBtn.classList.add('d-none');
                if (startBtn) startBtn.classList.add('d-none');
                if (retakeBtn) retakeBtn.classList.remove('d-none');
                if (validationErrorBox) validationErrorBox.classList.add('d-none');
                hideCameraError();
            }

            if (base64Input && base64Input.value.trim().startsWith('data:image/')) {
                showSnapshotPreview(base64Input.value.trim());
            }

            async function startCamera() {
                hideCameraError();

                if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                    const msg = 'Este navegador o conexión (requiere HTTPS) no permite acceder a la cámara en vivo. Abre la plataforma en HTTPS desde un dispositivo con cámara.';
                    if (statusMsg) statusMsg.textContent = msg;
                    showCameraError(msg);
                    return;
                }

                try {
                    stopCamera();
                    mediaStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'user' }, width: { ideal: 640 }, height: { ideal: 480 } },
                        audio: false,
                    });

                    videoEl.srcObject = mediaStream;
                    if (idleBox) idleBox.classList.add('d-none');
                    if (snapshotImg) snapshotImg.classList.add('d-none');
                    videoEl.classList.remove('d-none');
                    startBtn.classList.add('d-none');
                    captureBtn.classList.remove('d-none');
                    retakeBtn.classList.add('d-none');
                    if (statusMsg) statusMsg.textContent = 'Cámara activa. Ubícate frente al lente y presiona "Capturar Selfie".';
                } catch (err) {
                    const reason = err && err.name === 'NotAllowedError'
                        ? 'Permiso de cámara denegado. Debes habilitar el permiso de cámara en tu navegador y volver a intentar para tomar la selfie.'
                        : 'No se pudo acceder a la cámara en vivo de este dispositivo. Verifica que no esté siendo usada por otra aplicación e inténtalo nuevamente.';
                    if (statusMsg) statusMsg.textContent = reason;
                    showCameraError(reason);
                }
            }

            if (startBtn) {
                startBtn.addEventListener('click', startCamera);
            }

            if (retryCameraBtn) {
                retryCameraBtn.addEventListener('click', startCamera);
            }

            if (captureBtn) {
                captureBtn.addEventListener('click', function () {
                    if (!videoEl || !canvasEl) {
                        return;
                    }
                    const width = videoEl.videoWidth || 640;
                    const height = videoEl.videoHeight || 480;
                    canvasEl.width = width;
                    canvasEl.height = height;

                    const ctx = canvasEl.getContext('2d');
                    ctx.drawImage(videoEl, 0, 0, width, height);

                    const dataUrl = canvasEl.toDataURL('image/jpeg', 0.85);
                    base64Input.value = dataUrl;
                    stopCamera();
                    showSnapshotPreview(dataUrl);
                    if (statusMsg) statusMsg.textContent = 'Selfie capturada correctamente.';
                });
            }

            if (retakeBtn) {
                retakeBtn.addEventListener('click', function () {
                    base64Input.value = '';
                    startCamera();
                });
            }

            certifyForm.addEventListener('submit', function (event) {
                if (isLocked) {
                    event.preventDefault();
                    return;
                }

                if (signatureCanvas && signatureBase64Input) {
                    signatureBase64Input.value = hasSignature ? signatureCanvas.toDataURL('image/png') : '';
                }

                const hasBase64 = base64Input && base64Input.value.trim() !== '';
                const hasValidSignature = hasSignature && signatureBase64Input && signatureBase64Input.value.trim() !== '';

                if (!hasBase64) {
                    event.preventDefault();
                    if (validationErrorBox) {
                        validationErrorBox.textContent = 'Debes tomar tu selfie en tiempo real con la cámara antes de continuar.';
                        validationErrorBox.classList.remove('d-none');
                        validationErrorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                if (!hasValidSignature) {
                    event.preventDefault();
                    if (validationErrorBox) {
                        validationErrorBox.textContent = 'Debes dibujar tu firma digital en el recuadro antes de continuar.';
                        validationErrorBox.classList.remove('d-none');
                        validationErrorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                }
            });

            window.addEventListener('pagehide', function () {
                stopCamera();
            });
        });
    })();
</script>
