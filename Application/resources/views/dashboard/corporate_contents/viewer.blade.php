@extends('layouts.dashboard')

@section('content')
    <div class="content">
        <div class="secure-viewer-standalone secure-viewer-protected" id="standaloneSecureViewer">
            <div class="secure-viewer-header">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <i class="fa fa-shield-halved text-info"></i>
                    <h1 id="secureViewerTitle" class="h6 mb-0 text-truncate text-white">{{ $content->title }}</h1>
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

                    <a href="{{ route('dashboard.corporate-contents.index') }}" class="btn btn-sm btn-outline-light">
                        <i class="fa fa-arrow-left me-1"></i> Volver a la biblioteca
                    </a>
                </div>
            </div>

            <div id="secureViewerBody" class="secure-viewer-body">
                <div id="secureViewerStage" class="secure-pdf-stage"></div>
            </div>
        </div>

        <div class="secure-print-blocked">
            La impresión de este documento corporativo no está permitida.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('standaloneSecureViewer');
            if (!container || !window.BoomerSecureViewer) {
                return;
            }

            window.BoomerSecureViewer.open({
                tokenUrl: "{{ route('dashboard.corporate-contents.token', $content->id) }}",
                viewedUrl: "{{ route('dashboard.corporate-contents.viewed', $content->id) }}",
                viewedLabel: @json($viewedLabel ?? ''),
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
                    viewedBadgeEl: document.getElementById('secureViewerViewedBadge'),
                    bodyEl: document.getElementById('secureViewerBody'),
                    stageEl: document.getElementById('secureViewerStage'),
                    activeScopeEl: container,
                },
            });
        });
    </script>
@endsection

@include('dashboard.corporate_contents._secure_viewer_assets')
