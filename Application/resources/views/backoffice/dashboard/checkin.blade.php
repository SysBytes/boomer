@extends('layouts.dashboard')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <style>
        /* ── Check-In / Check-Out — Estilos personalizados ── */
        .checkin-hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: #fff;
            margin-bottom: 1.5rem;
        }

        .checkin-hero .status-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .35rem .85rem;
            border-radius: 2rem;
        }

        .checkin-hero .status-badge.active {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, .3);
        }

        .checkin-hero .status-badge.idle {
            background: rgba(148, 163, 184, .1);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, .2);
        }

        .checkin-hero .elapsed-timer {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: .04em;
            font-variant-numeric: tabular-nums;
            color: #10b981;
            line-height: 1;
        }

        /* ── Selector de proyecto ── */
        .work-select-wrapper {
            position: relative;
        }

        .work-select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            padding: .85rem 3rem .85rem 1.1rem;
            font-size: 1rem;
            font-weight: 500;
            border: 2px solid #e5e7eb;
            border-radius: .75rem;
            width: 100%;
            background-color: #fff;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s;
            color: #1e293b;
        }

        .work-select-wrapper select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }

        .work-select-wrapper::after {
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6366f1;
            pointer-events: none;
            font-size: .85rem;
        }

        /* Info del proyecto seleccionado */
        #work-info-box {
            display: none;
            margin-top: .75rem;
            padding: .85rem 1.1rem;
            background: #f5f3ff;
            border: 1.5px solid #c4b5fd;
            border-radius: .75rem;
            font-size: .875rem;
        }

        #work-info-box .wi-name {
            font-weight: 700;
            color: #1e293b;
        }

        #work-info-box .wi-client {
            color: #64748b;
        }

        #work-info-box .wi-addr {
            color: #94a3b8;
            font-size: .8rem;
            margin-top: .2rem;
        }

        /* ── Cámara ── */
        .camera-wrapper {
            position: relative;
            border-radius: .75rem;
            overflow: hidden;
            background: #0f172a;
            aspect-ratio: 4/3;
            max-width: 520px;
            margin: 0 auto;
        }

        .camera-wrapper video,
        .camera-wrapper canvas {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .camera-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, .35);
            gap: .75rem;
            font-size: .875rem;
        }

        .camera-overlay {
            position: absolute;
            inset: 0;
            border: 2px dashed rgba(255, 255, 255, .2);
            border-radius: .75rem;
            pointer-events: none;
        }

        .camera-btn-row {
            display: flex;
            gap: .75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        /* ── Preview foto ── */
        .photo-preview-box {
            text-align: center;
            margin-top: .85rem;
            padding: .75rem;
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: .75rem;
        }

        .photo-preview-box img {
            max-height: 180px;
            border-radius: .5rem;
            width: auto;
        }

        /* ── GPS ── */
        .geo-status {
            font-size: .8rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: .4rem;
            justify-content: center;
        }

        .geo-status .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #64748b;
            flex-shrink: 0;
        }

        .geo-status.ok .dot {
            background: #10b981;
        }

        .geo-status.err .dot {
            background: #ef4444;
        }

        /* ── Paso visual (numeración) ── */
        .step-label {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #6366f1;
            margin-bottom: .75rem;
        }

        .step-label .step-num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #6366f1;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            flex-shrink: 0;
        }

        .step-divider {
            border: none;
            border-top: 1.5px dashed #e5e7eb;
            margin: 1.5rem 0;
        }

        /* Móvil: botones full-width */
        @media (max-width: 576px) {
            .camera-btn-row {
                flex-direction: column;
                align-items: center;
            }

            .camera-btn-row .btn {
                width: 100%;
                max-width: 320px;
            }

            .btn-submit-full {
                width: 100%;
            }

            .checkin-hero {
                padding: 1.25rem;
            }

            .checkin-hero .elapsed-timer {
                font-size: 2rem;
            }
        }
    </style>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        /* ─────────────────────────────────────────────────────────
           Datos de obras pasados desde PHP para el info-box
        ───────────────────────────────────────────────────────── */
        const WORKS_DATA = {
            @foreach($works as $w)
                                {{ $w->work_id }}: {
                    name: @json($w->work_name),
                    client: @json($w->location->client->client_name ?? '—'),
                    address: @json($w->location->location_address ?? '')
                },
            @endforeach
            };

        document.addEventListener('DOMContentLoaded', function () {
            jQuery('.js-select2').select2();

            /* ══════════════════════════════════════════════════
               1. GEOLOCALIZACIÓN
            ══════════════════════════════════════════════════ */
            const latFields = document.querySelectorAll('.geo-lat');
            const lngFields = document.querySelectorAll('.geo-lng');
            const geoEls = document.querySelectorAll('.geo-status');

            function setGeoStatus(ok, msg) {
                geoEls.forEach(el => {
                    el.classList.toggle('ok', ok);
                    el.classList.toggle('err', !ok);
                    el.querySelector('span').textContent = msg;
                });
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const lat = pos.coords.latitude.toFixed(8);
                        const lng = pos.coords.longitude.toFixed(8);
                        latFields.forEach(f => f.value = lat);
                        lngFields.forEach(f => f.value = lng);
                        setGeoStatus(true, `Ubicación obtenida (±${Math.round(pos.coords.accuracy)} m)`);
                    },
                    () => setGeoStatus(false, 'No se pudo obtener la ubicación. Actívala en el navegador.'),
                    { enableHighAccuracy: true, timeout: 12000 }
                );
            } else {
                setGeoStatus(false, 'Tu navegador no soporta geolocalización.');
            }

            /* ══════════════════════════════════════════════════
               2. INFO BOX AL CAMBIAR PROYECTO
            ══════════════════════════════════════════════════ */
            const workSelect = document.getElementById('work_id');
            const workInfoBox = document.getElementById('work-info-box');

            if (workSelect && workInfoBox) {
                workSelect.addEventListener('change', function () {
                    const id = parseInt(this.value);
                    const data = WORKS_DATA[id];
                    if (data) {
                        workInfoBox.style.display = 'block';
                        workInfoBox.querySelector('.wi-name').textContent = data.name;
                        workInfoBox.querySelector('.wi-client').textContent = data.client;
                        const addrEl = workInfoBox.querySelector('.wi-addr');
                        if (data.address) {
                            addrEl.textContent = '📍 ' + data.address;
                            addrEl.style.display = 'block';
                        } else {
                            addrEl.style.display = 'none';
                        }
                    } else {
                        workInfoBox.style.display = 'none';
                    }
                });
            }

            /* ══════════════════════════════════════════════════
               3. CÁMARA — función reutilizable por prefijo
            ══════════════════════════════════════════════════ */
            function initCamera(prefix) {
                const video = document.getElementById(`${prefix}-video`);
                const canvas = document.getElementById(`${prefix}-canvas`);
                const placeholder = document.getElementById(`${prefix}-placeholder`);
                const cameraWrap = document.getElementById(`${prefix}-camera-wrapper`);
                const startBtn = document.getElementById(`${prefix}-start`);
                const captureBtn = document.getElementById(`${prefix}-capture`);
                const retakeBtn = document.getElementById(`${prefix}-retake`);
                const photoField = document.getElementById(`${prefix}-photo-data`);
                const previewBox = document.getElementById(`${prefix}-preview-box`);
                const previewImg = document.getElementById(`${prefix}-preview-img`);

                if (!video) return;

                let stream = null;
                let currentFacingMode = 'user';

                function stopStream() {
                    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
                }

                async function startStream() {
                    stopStream();
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: { ideal: currentFacingMode },
                                width: { ideal: 1280 },
                                height: { ideal: 960 }
                            }
                        });
                        video.srcObject = stream;
                        
                        // Efecto espejo solo para previsualización si es cámara frontal
                        if (currentFacingMode === 'user') {
                            video.style.transform = 'scaleX(-1)';
                        } else {
                            video.style.transform = 'none';
                        }

                        if (cameraWrap) cameraWrap.style.display = 'block';
                        video.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                        startBtn.style.display = 'none';
                        captureBtn && (captureBtn.style.display = 'inline-flex');
                        previewBox.style.display = 'none';
                        photoField.value = '';
                    } catch (e) {
                        alert('No se pudo acceder a la cámara. Verifica que hayas concedido el permiso.');
                    }
                }

                startBtn && startBtn.addEventListener('click', startStream);

                captureBtn && captureBtn.addEventListener('click', function () {
                    const ctx = canvas.getContext('2d');
                    canvas.width = video.videoWidth || 1280;
                    canvas.height = video.videoHeight || 960;
                    
                    // Aplicar espejo en el canvas para que la foto quede igual que el preview
                    if (currentFacingMode === 'user') {
                        ctx.translate(canvas.width, 0);
                        ctx.scale(-1, 1);
                    }

                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    if (currentFacingMode === 'user') {
                        ctx.setTransform(1, 0, 0, 1, 0, 0);
                    }

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                    photoField.value = dataUrl;

                    previewImg.src = dataUrl;
                    previewBox.style.display = 'block';

                    if (cameraWrap) cameraWrap.style.display = 'none';
                    video.style.display = 'none';
                    captureBtn.style.display = 'none';
                    retakeBtn && (retakeBtn.style.display = 'inline-flex');
                    stopStream();
                });

                retakeBtn && retakeBtn.addEventListener('click', function () {
                    previewBox.style.display = 'none';
                    previewImg.src = '';
                    photoField.value = '';
                    retakeBtn.style.display = 'none';
                    startBtn.style.display = 'inline-flex';
                    if (cameraWrap) cameraWrap.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'flex';
                });
            }

            initCamera('checkin');
            initCamera('checkout');

            /* ══════════════════════════════════════════════════
               4. TIMER EN TIEMPO REAL (pantalla checkout)
            ══════════════════════════════════════════════════ */
            const timerEl = document.getElementById('elapsed-timer');
            const checkInTs = timerEl ? parseInt(timerEl.dataset.checkinTs, 10) : null;

            if (timerEl && checkInTs) {
                const pad = n => String(n).padStart(2, '0');
                const tick = () => {
                    const diff = Math.floor(Date.now() / 1000 - checkInTs);
                    timerEl.textContent = `${pad(Math.floor(diff / 3600))}:${pad(Math.floor(diff % 3600 / 60))}:${pad(diff % 60)}`;
                };
                tick();
                setInterval(tick, 1000);
            }

            /* ══════════════════════════════════════════════════
               5. VALIDACIÓN PREVIA AL SUBMIT
            ══════════════════════════════════════════════════ */
            document.querySelectorAll('.checkin-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    const photoField = this.querySelector('[id$="-photo-data"]');
                    const latField = this.querySelector('.geo-lat');

                    if (!photoField || !photoField.value.trim()) {
                        e.preventDefault();
                        alert('Debes tomar una foto antes de continuar.');
                        return;
                    }
                    if (!latField || !latField.value.trim()) {
                        e.preventDefault();
                        alert('No se pudo obtener tu ubicación. Activa el GPS e intenta de nuevo.');
                        return;
                    }
                    const btn = this.querySelector('[type=submit]');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Procesando...';
                    }
                });
            });
        });
    </script>
@endsection

@section('content')
    <div class="content">

        {{-- ── Alertas ── --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-check fa-lg"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ══════════════════════════════════════════
        HERO
        ══════════════════════════════════════════ --}}
        <div class="checkin-hero">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    @if($activeLog)
                        <span class="status-badge active mb-2">
                            <i class="fa-solid fa-circle-dot fa-xs"></i> Turno activo
                        </span>
                        <h2 class="mt-2 mb-1 fw-bold" style="font-size:1.4rem;">
                            {{ $activeLog->work->work_name ?? 'Obra' }}
                        </h2>
                        <p class="mb-0" style="font-size:.85rem; color:#94a3b8;">
                            {{ $activeLog->work->location->client->client_name ?? '' }}
                            &nbsp;·&nbsp;
                            Entrada: {{ $activeLog->check_in_time->format('h:i A') }}
                        </p>
                    @else
                        <span class="status-badge idle mb-2">
                            <i class="fa-solid fa-circle fa-xs"></i> Sin turno activo
                        </span>
                        <h2 class="mt-2 mb-1 fw-bold" style="font-size:1.4rem;">Registrar Asistencia</h2>
                        <p class="mb-0" style="font-size:.85rem; color:#94a3b8;">
                            Selecciona tu obra y toma una foto.
                        </p>
                    @endif
                </div>
                <div class="text-end ms-3">
                    @if($activeLog)
                        <div class="elapsed-timer" id="elapsed-timer" data-checkin-ts="{{ $activeLog->check_in_time->timestamp }}">
                            00:00:00
                        </div>
                        <div style="font-size:.7rem; color:#475569; margin-top:.15rem;">transcurrido</div>
                    @else
                        <i class="fa-solid fa-clock-rotate-left fa-2x" style="color:rgba(255,255,255,.12);"></i>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
        SECCIÓN A — CHECK-IN
        ══════════════════════════════════════════ --}}
        @if(!$activeLog)

            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa-solid fa-arrow-right-to-bracket text-success me-2"></i>
                        Nuevo Check-In
                    </h3>
                </div>
                <div class="block-content block-content-full">

                    {{-- GPS: oculto al usuario, captura silenciosa en segundo plano --}}
                    <div class="geo-status" style="position:absolute; visibility:hidden; pointer-events:none;">
                        <div class="dot"></div>
                        <span>Obteniendo ubicación GPS...</span>
                    </div>

                    <form action="{{ route('checkin.store') }}" method="POST" class="checkin-form">
                        @csrf
                        <input type="hidden" name="latitude" class="geo-lat">
                        <input type="hidden" name="longitude" class="geo-lng">
                        <input type="hidden" name="check_in_photo" id="checkin-photo-data">

                        {{-- ── PASO 1: Proyecto ── --}}
                        <div class="step-label">
                            <div class="step-num">1</div>
                            Selecciona la Obra
                        </div>

                        @if($works->isEmpty())
                            <div class="alert alert-warning mb-4">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                No tienes obras activas asignadas. Contacta a tu supervisor.
                            </div>
                        @else
                            <div class="work-select-wrapper mb-1">
                                <select id="work_id" name="work_id" class="js-select2" style="width: 100%;" data-placeholder="— Selecciona una obra —" required>
                                    <option></option>
                                    @foreach($works as $work)
                                        <option value="{{ $work->work_id }}">
                                            {{ $work->location->location_name ?? 'N/A' }} - {{ $work->work_name }}
                                            @if($work->location && $work->location->client) · {{ $work->location->client->client_name }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Info del proyecto seleccionado --}}
                            <div id="work-info-box">
                                <div class="wi-name"></div>
                                <div class="wi-client"></div>
                                <div class="wi-addr"></div>
                            </div>
                        @endif

                        @error('work_id')
                            <div class="text-danger mt-2 fs-sm">
                                <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror

                        <hr class="step-divider">

                        {{-- ── PASO 2: Foto ── --}}
                        <div class="step-label">
                            <div class="step-num">2</div>
                            Toma una Foto
                        </div>

                        <div class="camera-wrapper mb-3" id="checkin-camera-wrapper">
                            <div class="camera-placeholder" id="checkin-placeholder">
                                <i class="fa-solid fa-camera fa-3x"></i>
                                <span>Presiona "Activar Cámara"</span>
                            </div>
                            <video id="checkin-video" autoplay playsinline style="display:none;"></video>
                            <canvas id="checkin-canvas" style="display:none;"></canvas>
                            <div class="camera-overlay"></div>
                        </div>

                        {{-- Preview — oculto por defecto con style inline (garantizado) --}}
                        <div id="checkin-preview-box" class="photo-preview-box" style="display:none;">
                            <img id="checkin-preview-img" src="" alt="Foto capturada">
                            <p class="fs-sm text-success fw-semibold mt-2 mb-0">
                                <i class="fa fa-circle-check me-1"></i>Foto lista
                            </p>
                        </div>

                        <div class="camera-btn-row">
                            <button type="button" id="checkin-start" class="btn btn-alt-secondary">
                                <i class="fa-solid fa-camera me-1"></i> Activar Cámara
                            </button>
                            <button type="button" id="checkin-capture" class="btn btn-info" style="display:none;">
                                <i class="fa-solid fa-circle me-1"></i> Capturar
                            </button>
                            <button type="button" id="checkin-retake" class="btn btn-alt-danger" style="display:none;">
                                <i class="fa-solid fa-rotate-left me-1"></i> Repetir
                            </button>
                        </div>

                        @error('check_in_photo')
                            <div class="text-danger mt-2 text-center fs-sm">
                                <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror

                        {{-- ── Submit ── --}}
                        @if(!$works->isEmpty())
                            <div class="mt-4 d-grid">
                                <button type="submit" class="btn btn-lg btn-success btn-submit-full">
                                    <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Registrar Check-In
                                </button>
                            </div>
                        @endif

                    </form>
                </div>
            </div>

            {{-- ══════════════════════════════════════════
            SECCIÓN B — CHECK-OUT
            ══════════════════════════════════════════ --}}
        @else

            {{-- Tarjeta de resumen del turno --}}
            <div class="block block-rounded mb-4">
                <div class="block-content">
                    <dl class="row mb-0 align-items-center pb-4">
                        <dt class="col-5 col-md-3 text-muted fs-sm fw-semibold">Obra</dt>
                        <dd class="col-7 col-md-9 mb-2 fw-bold">{{ $activeLog->work->work_name ?? '—' }}</dd>

                        <dt class="col-5 col-md-3 text-muted fs-sm fw-semibold">Cliente</dt>
                        <dd class="col-7 col-md-9 mb-2">{{ $activeLog->work->location->client->client_name ?? '—' }}</dd>

                        <dt class="col-5 col-md-3 text-muted fs-sm fw-semibold">Entrada</dt>
                        <dd class="col-7 col-md-9 mb-2">
                            {{ $activeLog->check_in_time->format('d/m/Y') }}
                            <strong>{{ $activeLog->check_in_time->format('h:i:s A') }}</strong>
                        </dd>

                        @if($activeLog->check_in_photo)
                            <dt class="col-5 col-md-3 text-muted fs-sm fw-semibold mt-2">Foto entrada</dt>
                            <dd class="col-7 col-md-9 mt-2 mb-0">
                                <a href="{{ asset($activeLog->check_in_photo) }}" target="_blank">
                                    @php
                                        $thumbFile = 'img/check_in_out/thumbs/thumb_in_' .
                                            ltrim(str_replace('img/check_in_out/ci_in_', '', $activeLog->check_in_photo));
                                    @endphp
                                    <img src="{{ asset($thumbFile) }}" onerror="this.src='{{ asset($activeLog->check_in_photo) }}'" style="max-height:70px; border-radius:.5rem; border:1.5px solid #c4b5fd;" alt="Foto check-in">
                                </a>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Formulario Check-Out --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa-solid fa-arrow-right-from-bracket text-danger me-2"></i>
                        Registrar Check-Out
                    </h3>
                </div>
                <div class="block-content block-content-full">

                    {{-- GPS: oculto al usuario, captura silenciosa en segundo plano --}}
                    <div class="geo-status" style="position:absolute; visibility:hidden; pointer-events:none;">
                        <div class="dot"></div>
                        <span>Obteniendo ubicación GPS...</span>
                    </div>

                    <form action="{{ route('checkin.checkout') }}" method="POST" class="checkin-form">
                        @csrf
                        <input type="hidden" name="latitude" class="geo-lat">
                        <input type="hidden" name="longitude" class="geo-lng">
                        <input type="hidden" name="check_out_photo" id="checkout-photo-data">

                        <div class="step-label">
                            <div class="step-num">1</div>
                            Toma una Foto de Salida
                        </div>

                        <div class="camera-wrapper mb-3" id="checkout-camera-wrapper">
                            <div class="camera-placeholder" id="checkout-placeholder">
                                <i class="fa-solid fa-camera fa-3x"></i>
                                <span>Presiona "Activar Cámara"</span>
                            </div>
                            <video id="checkout-video" autoplay playsinline style="display:none;"></video>
                            <canvas id="checkout-canvas" style="display:none;"></canvas>
                            <div class="camera-overlay"></div>
                        </div>

                        {{-- Preview oculto con style inline --}}
                        <div id="checkout-preview-box" class="photo-preview-box" style="display:none;">
                            <img id="checkout-preview-img" src="" alt="Foto capturada">
                            <p class="fs-sm text-success fw-semibold mt-2 mb-0">
                                <i class="fa fa-circle-check me-1"></i>Foto lista
                            </p>
                        </div>

                        <div class="camera-btn-row">
                            <button type="button" id="checkout-start" class="btn btn-alt-secondary">
                                <i class="fa-solid fa-camera me-1"></i> Activar Cámara
                            </button>
                            <button type="button" id="checkout-capture" class="btn btn-info" style="display:none;">
                                <i class="fa-solid fa-circle me-1"></i> Capturar
                            </button>
                            <button type="button" id="checkout-retake" class="btn btn-alt-danger" style="display:none;">
                                <i class="fa-solid fa-rotate-left me-1"></i> Repetir
                            </button>
                        </div>

                        @error('check_out_photo')
                            <div class="text-danger mt-2 text-center fs-sm">
                                <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror

                        <div class="mt-4 d-grid">
                            <button type="submit" class="btn btn-lg btn-danger btn-submit-full">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Registrar Check-Out
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        @endif

    </div>
@endsection