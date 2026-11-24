@extends('layouts.dashboard')

@section('content')
    <div class="content">
        <h2 class="content-heading">
            <i class="fa-solid fa-book-open text-primary me-2"></i>Biblioteca de Contenidos
        </h2>
        <p class="text-muted mb-4">Material corporativo disponible para ti. Puedes consultarlo cuando lo necesites.</p>

        @include('backoffice.induction._alerts')

        @if($categories->isEmpty())
            <div class="block block-rounded">
                <div class="block-content block-content-full text-center py-5 text-muted">
                    <i class="fa fa-folder-open fa-3x mb-3 d-block mx-auto"></i>
                    <h5 class="mb-1">No hay contenidos disponibles</h5>
                    <p class="mb-0">Por el momento no tienes material corporativo asignado.</p>
                </div>
            </div>
        @else
            <div class="accordion" id="libraryAccordion">
                @foreach($categories as $category)
                    @php $collapseId = 'category-' . $category->id; @endphp
                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="heading-{{ $collapseId }}">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                aria-controls="{{ $collapseId }}">
                                <i class="fa-solid fa-layer-group text-primary me-2"></i>
                                {{ $category->name }}
                                <span class="badge bg-primary ms-2">{{ $category->subcategories->sum(fn($s) => $s->contents->count()) }}</span>
                            </button>
                        </h2>
                        <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-{{ $collapseId }}" data-bs-parent="#libraryAccordion">
                            <div class="accordion-body">
                                @if($category->description)
                                    <p class="text-muted fs-sm">{{ $category->description }}</p>
                                @endif

                                @foreach($category->subcategories as $subcategory)
                                    <h5 class="fw-semibold mt-3 mb-3">
                                        <i class="fa-regular fa-folder-open text-warning me-1"></i>{{ $subcategory->name }}
                                    </h5>
                                    @if($subcategory->description)
                                        <p class="text-muted fs-sm mt-n2">{{ $subcategory->description }}</p>
                                    @endif

                                    <div class="row g-3">
                                        @foreach($subcategory->contents as $content)
                                            @php
                                                $viewedAt = $viewedMap[$content->id] ?? null;
                                                $isValidated = $viewedAt !== null;
                                                $viewedLabel = $isValidated ? \Illuminate\Support\Carbon::parse($viewedAt)->format('d/m/Y H:i') : '';
                                            @endphp
                                            <div class="col-12 col-md-6 col-xl-4">
                                                <div class="card h-100 border">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                                            @if($content->is_external)
                                                                <span class="badge bg-warning text-dark"><i class="fa fa-link me-1" aria-hidden="true"></i>Enlace</span>
                                                            @elseif($content->type === 'pdf')
                                                                <span class="badge bg-danger"><i class="fa fa-file-pdf me-1" aria-hidden="true"></i>PDF</span>
                                                            @else
                                                                <span class="badge bg-info"><i class="fa fa-video me-1" aria-hidden="true"></i>Video</span>
                                                            @endif

                                                            @if($content->is_mandatory)
                                                                <span class="badge bg-primary">Obligatorio</span>
                                                            @endif
                                                        </div>
                                                        <h6 class="card-title fw-semibold mb-2" style="text-wrap: balance;">{{ $content->title }}</h6>

                                                        <div class="fs-sm mb-3 js-view-status" data-content-id="{{ $content->id }}">
                                                            @if($isValidated)
                                                                <span class="text-success"><i class="fa fa-circle-check me-1" aria-hidden="true"></i>Visto el {{ $viewedLabel }}</span>
                                                            @else
                                                                <div class="text-muted"><i class="fa-regular fa-circle me-1" aria-hidden="true"></i>Pendiente por ver</div>
                                                                <div class="fs-xs text-primary mt-1">
                                                                    <i class="fa fa-camera me-1" aria-hidden="true"></i>Requiere selfie y firma la primera vez
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="mt-auto">
                                                            @if(!$isValidated)
                                                                <a
                                                                    href="{{ route('dashboard.corporate-contents.verify', $content->id) }}"
                                                                    class="btn btn-primary w-100 py-2"
                                                                >
                                                                    <i class="fa {{ $content->is_external ? 'fa-up-right-from-square' : ($content->type === 'pdf' ? 'fa-file-pdf' : 'fa-circle-play') }} me-1" aria-hidden="true"></i>
                                                                    @if($content->is_external)
                                                                        Ver y validar enlace
                                                                    @elseif($content->type === 'pdf')
                                                                        Ver y validar documento
                                                                    @else
                                                                        Ver y validar video
                                                                    @endif
                                                                </a>
                                                            @elseif($content->is_external)
                                                                <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary w-100 py-2 js-open-external-content"
                                                                    data-viewed-url="{{ route('dashboard.corporate-contents.viewed', $content->id) }}"
                                                                    data-content-id="{{ $content->id }}">
                                                                    <i class="fa fa-up-right-from-square me-1" aria-hidden="true"></i> Abrir enlace
                                                                </a>
                                                            @else
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-outline-primary w-100 py-2 js-open-secure-content"
                                                                    data-token-url="{{ route('dashboard.corporate-contents.token', $content->id) }}"
                                                                    data-viewed-url="{{ route('dashboard.corporate-contents.viewed', $content->id) }}"
                                                                    data-content-id="{{ $content->id }}"
                                                                    data-viewed-label="{{ $viewedLabel }}"
                                                                    data-title="{{ $content->title }}"
                                                                    data-type="{{ $content->type }}"
                                                                >
                                                                    <i class="fa {{ $content->type === 'pdf' ? 'fa-file-pdf' : 'fa-circle-play' }} me-1" aria-hidden="true"></i>
                                                                    {{ $content->type === 'pdf' ? 'Abrir documento' : 'Ver video' }}
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <dialog id="secureContentDialog" aria-labelledby="secureViewerTitle" closedby="closerequest">
        <div class="secure-viewer-header secure-viewer-protected">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <i class="fa fa-shield-halved text-info"></i>
                <h1 id="secureViewerTitle" class="h6 mb-0 text-truncate text-white">Documento</h1>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span id="secureViewerViewedBadge" class="badge bg-success d-none"></span>
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

                <button type="button" class="btn btn-sm btn-outline-light" id="secureViewerCloseBtn" aria-label="Cerrar visor" autofocus>
                    <i class="fa fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>

        <div id="secureViewerBody" class="secure-viewer-body secure-viewer-protected">
            <div id="secureViewerStage" class="secure-pdf-stage"></div>
        </div>

        <div class="secure-print-blocked">
            La impresión de este documento corporativo no está permitida.
        </div>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dialog = document.getElementById('secureContentDialog');
            const closeBtn = document.getElementById('secureViewerCloseBtn');
            if (!dialog) {
                return;
            }

            const controls = {
                titleEl: document.getElementById('secureViewerTitle'),
                zoomControlsEl: document.getElementById('secureViewerZoomControls'),
                zoomOutBtn: document.getElementById('secureZoomOut'),
                zoomInBtn: document.getElementById('secureZoomIn'),
                zoomResetBtn: document.getElementById('secureZoomReset'),
                zoomLabelEl: document.getElementById('secureZoomLabel'),
                pageIndicatorEl: document.getElementById('secureViewerPageIndicator'),
                viewedBadgeEl: document.getElementById('secureViewerViewedBadge'),
                bodyEl: document.getElementById('secureViewerBody'),
                stageEl: document.getElementById('secureViewerStage'),
                activeScopeEl: dialog,
            };

            // Actualiza el estado "Visto el ..." en la tarjeta correspondiente (sin recargar)
            function updateCardStatus(contentId, label) {
                document.querySelectorAll('.js-view-status[data-content-id="' + contentId + '"]').forEach(function (el) {
                    const span = document.createElement('span');
                    span.className = 'text-success';
                    const icon = document.createElement('i');
                    icon.className = 'fa fa-circle-check me-1';
                    span.appendChild(icon);
                    span.appendChild(document.createTextNode('Visto el ' + label));
                    el.replaceChildren(span);
                });
                document.querySelectorAll('.js-open-secure-content[data-content-id="' + contentId + '"]').forEach(function (btn) {
                    btn.dataset.viewedLabel = label;
                });
            }

            // Enlaces externos: registrar la apertura sin impedir que el enlace se abra en la pestaña nueva
            document.querySelectorAll('.js-open-external-content').forEach(function (link) {
                link.addEventListener('click', function () {
                    const csrfEl = document.querySelector('meta[name="csrf-token"]');
                    fetch(link.dataset.viewedUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        keepalive: true,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfEl ? csrfEl.getAttribute('content') : '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(function (res) { return res.ok ? res.json() : null; })
                    .then(function (data) {
                        if (data && data.viewed) {
                            updateCardStatus(link.dataset.contentId, data.completed_at_label);
                        }
                    })
                    .catch(function () { /* el enlace se abre igualmente */ });
                });
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    dialog.close();
                });
            }

            dialog.addEventListener('close', function () {
                dialog.returnValue = '';
                if (window.BoomerSecureViewer) {
                    window.BoomerSecureViewer.destroy();
                }
            });

            document.querySelectorAll('.js-open-secure-content').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (typeof dialog.showModal === 'function') {
                        dialog.showModal();
                    } else {
                        dialog.setAttribute('open', 'open');
                    }

                    if (window.BoomerSecureViewer) {
                        window.BoomerSecureViewer.open({
                            tokenUrl: button.dataset.tokenUrl,
                            viewedUrl: button.dataset.viewedUrl,
                            viewedLabel: button.dataset.viewedLabel || '',
                            onViewed: function (data) {
                                updateCardStatus(button.dataset.contentId, data.completed_at_label);
                            },
                            title: button.dataset.title,
                            type: button.dataset.type,
                            controls: controls,
                        });
                    }
                });
            });
        });
    </script>
@endsection

@include('dashboard.corporate_contents._secure_viewer_assets')
