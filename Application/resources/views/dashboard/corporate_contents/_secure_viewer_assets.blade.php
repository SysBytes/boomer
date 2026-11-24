@section('css_after')
<style>
    /* Reglas obligatorias de contención de scroll para <dialog> modal */
    #secureContentDialog:modal {
        overscroll-behavior: contain;
    }

    #secureContentDialog::backdrop {
        background: rgba(15, 23, 42, 0.8);
        overflow: hidden;
        overscroll-behavior: contain;
    }

    #secureContentDialog {
        width: min(96dvw, 1140px);
        max-width: 96dvw;
        height: 92dvh;
        max-height: 92dvh;
        margin: auto;
        padding: 0;
        border: 0;
        border-radius: 0.5rem;
        box-shadow: 0 1.25rem 3rem rgba(0, 0, 0, 0.45);
        background: #1e293b;
        color: #f8fafc;
    }

    /* Mantener display condicionado a [open] para respetar el estado cerrado */
    #secureContentDialog[open] {
        display: flex;
        flex-direction: column;
    }

    .secure-viewer-standalone {
        background: #1e293b;
        color: #f8fafc;
        border-radius: 0.5rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 60dvh;
        min-height: 340px;
    }

    @media (min-width: 768px) {
        .secure-viewer-standalone {
            height: 74dvh;
            min-height: 460px;
        }
    }

    .secure-viewer-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #0f172a;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .secure-viewer-body {
        flex: 1 1 auto;
        overflow: auto;
        position: relative;
        background: #334155;
        user-select: none;
        -webkit-user-select: none;
        -webkit-touch-callout: none;
    }

    .secure-pdf-stage {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        min-height: 100%;
    }

    @media (max-width: 575.98px) {
        #secureContentDialog {
            width: 100dvw;
            max-width: 100dvw;
            height: 100dvh;
            max-height: 100dvh;
            margin: 0;
            border-radius: 0;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        .secure-viewer-header {
            padding: 0.5rem 0.75rem;
            gap: 0.5rem;
        }

        .secure-viewer-header .btn {
            min-height: 40px;
        }

        .secure-pdf-stage {
            padding: 0.5rem;
            gap: 0.75rem;
        }
    }

    .secure-pdf-page {
        position: relative;
        background: #ffffff;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.35);
        line-height: 0;
    }

    .secure-pdf-page canvas {
        display: block;
        max-width: 100%;
        height: auto;
        pointer-events: none;
    }

    .secure-video-wrapper {
        position: relative;
        width: 100%;
        max-width: 960px;
        margin: 1.5rem auto;
        background: #000000;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .secure-video-wrapper video {
        display: block;
        width: 100%;
        max-height: 72vh;
    }

    .secure-video-watermark {
        position: absolute;
        inset: 0;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .secure-video-watermark span {
        transform: rotate(-22deg);
        color: rgba(255, 255, 255, 0.24);
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-align: center;
        padding: 1rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .secure-print-blocked {
        display: none;
    }

    @media print {
        .secure-viewer-protected,
        #secureContentDialog,
        .secure-pdf-stage,
        .secure-video-wrapper {
            display: none !important;
        }

        .secure-print-blocked {
            display: block !important;
            padding: 2rem;
            text-align: center;
            font-weight: 600;
            color: #b91c1c;
        }
    }
</style>
@endsection

@section('js_after')
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"
    integrity="sha512-q+4liFwdPC/bNdhUpZx6aXDx/h77yEQtn4I1slHydcbZK34nLaR3cAeYSJshoxIOq3mjEf7xJE8YWIUHMn+oCQ=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
></script>
<script>
    (function () {
        'use strict';

        const PDFJS_WORKER_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        const PDFJS_WORKER_SRI = 'sha512-BbrZ76UNZq5BhH7LL7pn9A4TKQpQeNCHOo65/akfelcIBbcVvYWOFQKPXIrykE3qZxYjmDX573oa4Ywsc7rpTw==';

        const state = {
            renderSessionId: 0,
            workerBlobUrl: null,
            pdfDoc: null,
            watermarkText: '',
            zoomLevel: 1.0,
            lastRenderedStageWidth: 0,
            renderTasks: [],
            pageObserver: null,
            activeVideoEl: null,
            controls: null,
            isActive: false,
        };

        let pdfResizeTimer = null;
        window.addEventListener('resize', function () {
            if (!state.isActive || !state.pdfDoc || !state.controls) {
                return;
            }
            window.clearTimeout(pdfResizeTimer);
            pdfResizeTimer = window.setTimeout(function () {
                const currentWidth = state.controls.bodyEl
                    ? state.controls.bodyEl.clientWidth
                    : (state.controls.stageEl ? state.controls.stageEl.clientWidth : 0);
                if (currentWidth > 0 && Math.abs(currentWidth - state.lastRenderedStageWidth) > 24) {
                    renderPdfPages(state.renderSessionId).catch(function () {});
                }
            }, 150);
        });

        window.addEventListener('pagehide', function () {
            destroy();
            if (state.workerBlobUrl) {
                URL.revokeObjectURL(state.workerBlobUrl);
                state.workerBlobUrl = null;
            }
        });

        // Bloquear atajos de guardado e impresión únicamente mientras el visor está activo
        document.addEventListener('keydown', function (event) {
            if (!state.isActive) {
                return;
            }

            if (event.ctrlKey || event.metaKey) {
                const key = (event.key || '').toLowerCase();
                if (key === 's' || key === 'p' || key === 'u') {
                    event.preventDefault();
                    event.stopPropagation();
                }
            }
        }, true);

        async function ensurePdfWorker() {
            if (typeof window.pdfjsLib === 'undefined') {
                throw new Error('No se pudo inicializar el motor del visor PDF.');
            }

            if (state.workerBlobUrl) {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = state.workerBlobUrl;
                return;
            }

            const response = await fetch(PDFJS_WORKER_URL, {
                integrity: PDFJS_WORKER_SRI,
                mode: 'cors',
                credentials: 'omit',
                referrerPolicy: 'no-referrer',
            });

            if (!response.ok) {
                throw new Error('No se pudo cargar el componente auxiliar del visor PDF.');
            }

            const workerBuffer = await response.arrayBuffer();
            const blob = new Blob([workerBuffer], { type: 'text/javascript' });
            state.workerBlobUrl = URL.createObjectURL(blob);
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = state.workerBlobUrl;
        }

        function setStatus(stageEl, kind, message) {
            const wrapper = document.createElement('div');
            if (kind === 'loading') {
                wrapper.className = 'd-flex flex-column align-items-center justify-content-center py-5 text-light';
                const spinner = document.createElement('div');
                spinner.className = 'spinner-border text-light mb-3';
                spinner.setAttribute('role', 'status');
                const srText = document.createElement('span');
                srText.className = 'visually-hidden';
                srText.textContent = 'Cargando...';
                spinner.appendChild(srText);

                const p = document.createElement('p');
                p.className = 'mb-0 text-center';
                p.textContent = message;

                wrapper.appendChild(spinner);
                wrapper.appendChild(p);
            } else {
                wrapper.className = 'alert alert-danger m-4';
                wrapper.setAttribute('role', 'alert');
                wrapper.textContent = message;
            }
            stageEl.replaceChildren(wrapper);
        }

        function togglePdfControls(controls, visible) {
            if (!controls) {
                return;
            }
            if (controls.zoomControlsEl) {
                controls.zoomControlsEl.classList.toggle('d-none', !visible);
            }
            if (controls.pageIndicatorEl) {
                controls.pageIndicatorEl.classList.toggle('d-none', !visible);
            }
        }

        function updateZoomLabel(controls) {
            if (controls && controls.zoomLabelEl) {
                controls.zoomLabelEl.textContent = Math.round(state.zoomLevel * 100) + '%';
            }
        }

        function cancelActiveRendering() {
            if (state.pageObserver) {
                state.pageObserver.disconnect();
                state.pageObserver = null;
            }
            while (state.renderTasks.length > 0) {
                const task = state.renderTasks.pop();
                try {
                    task.cancel();
                } catch (_) {
                    // Ignorar tareas ya finalizadas
                }
            }
        }

        function drawWatermark(ctx, width, height, outputScale, text) {
            if (!text) {
                return;
            }

            ctx.save();
            ctx.setTransform(outputScale, 0, 0, outputScale, 0, 0);

            const fontSize = Math.max(13, Math.min(18, Math.round(width * 0.018)));
            ctx.fillStyle = 'rgba(71, 85, 105, 0.24)';
            ctx.font = '600 ' + fontSize + 'px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            const textWidth = ctx.measureText(text).width;
            const stepX = Math.ceil(textWidth + 110);
            const stepY = Math.max(190, Math.round(fontSize * 12));
            const diag = Math.hypot(width, height);

            ctx.translate(width / 2, height / 2);
            ctx.rotate(-Math.PI / 6);

            let rowIndex = 0;
            for (let y = -diag; y <= diag; y += stepY) {
                const offsetX = rowIndex % 2 === 0 ? 0 : stepX / 2;
                for (let x = -diag - stepX; x <= diag + stepX; x += stepX) {
                    ctx.fillText(text, x + offsetX, y);
                }
                rowIndex++;
            }
            ctx.restore();
        }

        async function renderPdfPages(sessionId) {
            const controls = state.controls;
            const pdfDoc = state.pdfDoc;
            if (!controls || !controls.stageEl || !pdfDoc) {
                return;
            }

            cancelActiveRendering();
            updateZoomLabel(controls);

            const stageEl = controls.stageEl;
            const totalPages = pdfDoc.numPages;
            const stageWidth = (controls.bodyEl ? controls.bodyEl.clientWidth : stageEl.clientWidth) || 820;
            state.lastRenderedStageWidth = stageWidth;

            const firstPage = await pdfDoc.getPage(1);
            if (sessionId !== state.renderSessionId) {
                return;
            }

            const baseViewport = firstPage.getViewport({ scale: 1 });
            const horizontalPadding = stageWidth < 576 ? 16 : 48;
            const fitScale = Math.min(1.5, Math.max(0.35, (stageWidth - horizontalPadding) / baseViewport.width));
            const effectiveScale = fitScale * state.zoomLevel;
            const outputScale = Math.min(window.devicePixelRatio || 1, 2);

            const fragment = document.createDocumentFragment();
            const pageEntries = [];

            for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                const wrapper = document.createElement('div');
                wrapper.className = 'secure-pdf-page';
                wrapper.dataset.pageNumber = String(pageNum);

                const canvas = document.createElement('canvas');
                wrapper.appendChild(canvas);
                fragment.appendChild(wrapper);
                pageEntries.push({ pageNum, wrapper, canvas });
            }

            stageEl.replaceChildren(fragment);

            if (controls.pageIndicatorEl) {
                controls.pageIndicatorEl.textContent = 'Página 1 de ' + totalPages;
                state.pageObserver = new IntersectionObserver(function (entries) {
                    let bestEntry = null;
                    for (const entry of entries) {
                        if (entry.isIntersecting && (!bestEntry || entry.intersectionRatio > bestEntry.intersectionRatio)) {
                            bestEntry = entry;
                        }
                    }
                    if (bestEntry && controls.pageIndicatorEl) {
                        controls.pageIndicatorEl.textContent = 'Página ' + bestEntry.target.dataset.pageNumber + ' de ' + totalPages;
                    }
                }, {
                    root: controls.bodyEl || null,
                    threshold: [0.25, 0.5, 0.75],
                });

                for (const item of pageEntries) {
                    state.pageObserver.observe(item.wrapper);
                }
            }

            for (const item of pageEntries) {
                if (sessionId !== state.renderSessionId) {
                    return;
                }

                const page = item.pageNum === 1 ? firstPage : await pdfDoc.getPage(item.pageNum);
                if (sessionId !== state.renderSessionId) {
                    return;
                }

                const viewport = page.getViewport({ scale: effectiveScale });
                const canvas = item.canvas;
                const ctx = canvas.getContext('2d');

                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = Math.floor(viewport.width) + 'px';
                canvas.style.height = Math.floor(viewport.height) + 'px';

                const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;
                const renderTask = page.render({
                    canvasContext: ctx,
                    transform: transform,
                    viewport: viewport,
                });

                state.renderTasks.push(renderTask);

                try {
                    await renderTask.promise;
                } catch (err) {
                    if (err && err.name === 'RenderingCancelledException') {
                        return;
                    }
                    throw err;
                }

                if (sessionId !== state.renderSessionId) {
                    return;
                }

                drawWatermark(ctx, viewport.width, viewport.height, outputScale, state.watermarkText);
            }
        }

        function renderVideo(streamUrl, watermarkText, stageEl) {
            const wrapper = document.createElement('div');
            wrapper.className = 'secure-video-wrapper';

            const video = document.createElement('video');
            video.controls = true;
            video.setAttribute('controlsList', 'nodownload noplaybackrate');
            video.disablePictureInPicture = true;
            video.playsInline = true;
            video.preload = 'metadata';
            video.src = streamUrl;
            video.addEventListener('contextmenu', function (e) {
                e.preventDefault();
            });

            wrapper.appendChild(video);

            if (watermarkText) {
                const overlay = document.createElement('div');
                overlay.className = 'secure-video-watermark';
                const badge = document.createElement('span');
                badge.textContent = watermarkText;
                overlay.appendChild(badge);
                wrapper.appendChild(overlay);
            }

            stageEl.replaceChildren(wrapper);
            state.activeVideoEl = video;
        }

        function attachScopeGuards(scopeEl) {
            if (!scopeEl || scopeEl.dataset.secureGuardsAttached === '1') {
                return;
            }
            scopeEl.dataset.secureGuardsAttached = '1';

            const prevent = function (e) {
                e.preventDefault();
            };
            scopeEl.addEventListener('contextmenu', prevent);
            scopeEl.addEventListener('dragstart', prevent);
            scopeEl.addEventListener('copy', prevent);
        }

        function attachZoomHandlers(controls) {
            if (!controls || !controls.zoomControlsEl || controls.zoomControlsEl.dataset.bound === '1') {
                return;
            }
            controls.zoomControlsEl.dataset.bound = '1';

            const applyZoom = function (nextZoom) {
                if (!state.pdfDoc) {
                    return;
                }
                const clamped = Math.min(2.0, Math.max(0.5, Math.round(nextZoom * 100) / 100));
                if (clamped === state.zoomLevel) {
                    return;
                }
                state.zoomLevel = clamped;
                renderPdfPages(state.renderSessionId).catch(function () {
                    if (state.controls && state.controls.stageEl) {
                        setStatus(state.controls.stageEl, 'error', 'No se pudo actualizar el zoom del documento.');
                    }
                });
            };

            if (controls.zoomOutBtn) {
                controls.zoomOutBtn.addEventListener('click', function () {
                    applyZoom(state.zoomLevel - 0.25);
                });
            }
            if (controls.zoomInBtn) {
                controls.zoomInBtn.addEventListener('click', function () {
                    applyZoom(state.zoomLevel + 0.25);
                });
            }
            if (controls.zoomResetBtn) {
                controls.zoomResetBtn.addEventListener('click', function () {
                    applyZoom(1.0);
                });
            }
        }

        function destroy() {
            state.renderSessionId++;
            state.isActive = false;
            cancelActiveRendering();

            if (state.activeVideoEl) {
                try {
                    state.activeVideoEl.pause();
                    state.activeVideoEl.removeAttribute('src');
                    state.activeVideoEl.load();
                } catch (_) {
                    // Ignorar errores al liberar video
                }
                state.activeVideoEl = null;
            }

            if (state.controls && state.controls.stageEl) {
                const canvases = state.controls.stageEl.querySelectorAll('canvas');
                canvases.forEach(function (canvas) {
                    canvas.width = 0;
                    canvas.height = 0;
                });
                state.controls.stageEl.replaceChildren();
            }

            if (state.pdfDoc) {
                try {
                    state.pdfDoc.destroy();
                } catch (_) {
                    // Ignorar
                }
                state.pdfDoc = null;
            }

            state.watermarkText = '';
            state.zoomLevel = 1.0;
        }

        function setViewedBadge(controls, text, kind) {
            const badge = controls && controls.viewedBadgeEl;
            if (!badge) {
                return;
            }
            if (!text) {
                badge.textContent = '';
                badge.classList.add('d-none');
                return;
            }
            badge.textContent = text;
            badge.classList.remove('d-none', 'bg-success', 'bg-warning', 'text-dark');
            if (kind === 'preview') {
                badge.classList.add('bg-warning', 'text-dark');
            } else {
                badge.classList.add('bg-success');
            }
        }

        // Notifica al servidor que el contenido fue abierto y muestra la fecha/hora de visualización
        function registerView(options, controls) {
            if (!options.viewedUrl) {
                return;
            }

            const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
            fetch(options.viewedUrl, {
                method: 'POST',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfTokenEl ? csrfTokenEl.getAttribute('content') : '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (!data) {
                    return;
                }
                if (data.viewed) {
                    setViewedBadge(controls, 'Visto el ' + data.completed_at_label, 'viewed');
                    if (typeof options.onViewed === 'function') {
                        options.onViewed(data);
                    }
                } else if (data.preview) {
                    setViewedBadge(controls, 'Vista previa (no se registra)', 'preview');
                }
            })
            .catch(function () {
                // El registro de visualización no debe interrumpir la lectura del contenido
            });
        }

        async function open(options) {
            destroy();
            const sessionId = state.renderSessionId;
            state.isActive = true;
            state.controls = options.controls || {};
            state.zoomLevel = 1.0;

            const controls = state.controls;
            attachScopeGuards(controls.activeScopeEl);
            attachZoomHandlers(controls);

            if (controls.titleEl) {
                controls.titleEl.textContent = options.title || 'Contenido';
            }

            setViewedBadge(controls, options.viewedLabel ? 'Visto el ' + options.viewedLabel : '', 'viewed');

            togglePdfControls(controls, false);
            setStatus(controls.stageEl, 'loading', 'Cargando documento de forma segura...');

            try {
                const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenEl ? csrfTokenEl.getAttribute('content') : '';

                const tokenRes = await fetch(options.tokenUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (sessionId !== state.renderSessionId) {
                    return;
                }

                if (!tokenRes.ok) {
                    throw new Error(tokenRes.status === 403
                        ? 'No tienes permisos para visualizar este contenido.'
                        : 'No se pudo autorizar el acceso al contenido.');
                }

                const tokenData = await tokenRes.json();
                if (sessionId !== state.renderSessionId) {
                    return;
                }

                state.watermarkText = tokenData.watermark || '';
                registerView(options, controls);

                if (tokenData.type === 'video') {
                    renderVideo(tokenData.stream_url, state.watermarkText, controls.stageEl);
                    return;
                }

                await ensurePdfWorker();
                if (sessionId !== state.renderSessionId) {
                    return;
                }

                const fileRes = await fetch(tokenData.stream_url, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'X-Content-Viewer': '1',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (sessionId !== state.renderSessionId) {
                    return;
                }

                if (!fileRes.ok) {
                    throw new Error('El enlace temporal expiró o no pudo cargarse el archivo.');
                }

                const pdfBytes = new Uint8Array(await fileRes.arrayBuffer());
                if (sessionId !== state.renderSessionId) {
                    return;
                }

                const loadingTask = window.pdfjsLib.getDocument({
                    data: pdfBytes,
                    disableAutoFetch: true,
                    disableStream: true,
                });

                state.pdfDoc = await loadingTask.promise;
                if (sessionId !== state.renderSessionId) {
                    destroy();
                    return;
                }

                togglePdfControls(controls, true);
                await renderPdfPages(sessionId);
            } catch (err) {
                if (sessionId !== state.renderSessionId) {
                    return;
                }
                togglePdfControls(controls, false);
                setStatus(
                    controls.stageEl,
                    'error',
                    (err && err.message) ? err.message : 'Ocurrió un error al cargar el documento.'
                );
            }
        }

        window.BoomerSecureViewer = {
            open: open,
            destroy: destroy,
        };
    })();
</script>
@endsection
