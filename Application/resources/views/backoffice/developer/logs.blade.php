@extends('layouts.backoffice')

@section('css_after')
<style>
    .log-summary {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.82rem;
        color: #1e293b;
        word-break: break-word;
    }
    .log-location {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.75rem;
        color: #dc2626;
        background: #fef2f2;
        padding: 0.15rem 0.45rem;
        border-radius: 0.25rem;
        display: inline-block;
        margin-top: 0.25rem;
    }
    .trace-viewer {
        background-color: #181f2c;
        color: #e2e8f0;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.8rem;
        line-height: 1.55;
        padding: 1rem 1.25rem;
        border-radius: 0.375rem;
        max-height: 65vh;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-word;
    }
</style>
@endsection

@section('content')
    <div class="content">
        <!-- Encabezado -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="content-heading mb-0 pb-0">
                    <i class="fa fa-bug text-danger me-2"></i>Errores 500 y Logs del Sistema
                </h2>
                <p class="text-muted fs-sm mt-1 mb-0">
                    Monitoreo de excepciones críticas y errores internos registrados en <code>storage/logs/{{ $selectedFile }}</code>
                    <span class="badge bg-secondary ms-1">{{ $fileSizeFormatted }}</span>
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('developer.logs', ['file' => $selectedFile, 'level' => $levelFilter]) }}" class="btn btn-alt-secondary">
                    <i class="fa fa-sync-alt me-1"></i> Actualizar
                </a>

                @if(count($availableFiles) > 0)
                    <a href="{{ route('developer.logs.download', ['file' => $selectedFile]) }}" class="btn btn-alt-info">
                        <i class="fa fa-download me-1"></i> Descargar Log
                    </a>

                    <form action="{{ route('developer.logs.clear') }}" method="POST" class="d-inline"
                          onsubmit="return confirm('¿Estás seguro de vaciar por completo el archivo {{ $selectedFile }}?')">
                        @csrf
                        <input type="hidden" name="file" value="{{ $selectedFile }}">
                        <button type="submit" class="btn btn-alt-danger">
                            <i class="fa fa-trash-alt me-1"></i> Vaciar Log
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <p class="mb-0"><i class="fa fa-check-circle me-1"></i> {{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <p class="mb-0"><i class="fa fa-exclamation-circle me-1"></i> {{ session('error') }}</p>
            </div>
        @endif

        <!-- Filtros -->
        <div class="block block-rounded mb-4">
            <div class="block-content py-3">
                <form method="GET" action="{{ route('developer.logs') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold mb-1" for="file">Archivo de Log</label>
                        <select name="file" id="file" class="form-select form-select-sm" onchange="this.form.submit()">
                            @forelse($availableFiles as $file)
                                <option value="{{ $file }}" {{ $selectedFile === $file ? 'selected' : '' }}>
                                    {{ $file }}
                                </option>
                            @empty
                                <option value="laravel.log">laravel.log</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1" for="level">Nivel de Severidad</label>
                        <select name="level" id="level" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="errors" {{ $levelFilter === 'errors' ? 'selected' : '' }}>
                                Solo Errores 500 / Críticos (ERROR, CRITICAL, EMERGENCY)
                            </option>
                            <option value="all" {{ $levelFilter === 'all' ? 'selected' : '' }}>
                                Todos los niveles (ERROR, WARNING, INFO, DEBUG)
                            </option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-semibold mb-1" for="searchLogInput">Filtrar en pantalla</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" id="searchLogInput" class="form-control" placeholder="Buscar por excepción, controlador, tabla o fecha..." oninput="filterLogRows()">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Registros -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Registros encontrados
                    <span class="badge bg-danger ms-1" id="visibleLogCount">{{ count($entries) }}</span>
                </h3>
            </div>
            <div class="block-content p-0">
                @if(count($entries) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter mb-0" id="logsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 110px;">Nivel</th>
                                    <th style="width: 175px;">Fecha / Hora</th>
                                    <th>Descripción del Error / Excepción</th>
                                    <th class="text-end" style="width: 130px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $index => $entry)
                                    @php
                                        $badgeClass = match($entry['level']) {
                                            'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY' => 'bg-danger',
                                            'WARNING' => 'bg-warning text-dark',
                                            'INFO', 'NOTICE' => 'bg-info',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <tr class="log-row">
                                        <td>
                                            <span class="badge {{ $badgeClass }}">{{ $entry['level'] }}</span>
                                            <div class="text-muted fs-xs mt-1">{{ $entry['env'] }}</div>
                                        </td>
                                        <td class="fs-sm text-nowrap">
                                            <i class="fa fa-clock text-muted me-1"></i>{{ $entry['timestamp'] }}
                                        </td>
                                        <td>
                                            <div class="log-summary">{{ \Illuminate\Support\Str::limit($entry['summary'], 260) }}</div>
                                            @if($entry['location'])
                                                <div class="log-location">
                                                    <i class="fa fa-file-code me-1"></i>{{ $entry['location'] }}
                                                </div>
                                            @endif
                                            <textarea class="d-none raw-trace-data" id="trace-data-{{ $index }}">{{ $entry['full_trace'] }}</textarea>
                                        </td>
                                        <td class="text-end">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="showTraceModal({{ $index }}, '{{ addslashes($entry['timestamp']) }}', '{{ addslashes($entry['level']) }}')">
                                                <i class="fa fa-terminal me-1"></i> Stack Trace
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-check-circle text-success fa-3x mb-3"></i>
                        <h5 class="mb-1">No se encontraron errores registrados</h5>
                        <p class="fs-sm mb-0">El archivo <code>{{ $selectedFile }}</code> no contiene registros para el filtro seleccionado.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Stack Trace -->
    <div class="modal fade" id="modalStackTrace" tabindex="-1" aria-labelledby="modalStackTraceLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalStackTraceLabel">
                        <i class="fa fa-bug text-danger me-2"></i>Detalle de Excepción / Error 500
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-body-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge bg-danger me-2" id="modalTraceLevel">ERROR</span>
                            <span class="text-muted fs-sm fw-semibold" id="modalTraceTimestamp"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-alt-secondary" onclick="copyStackTrace()">
                            <i class="fa fa-copy me-1"></i> Copiar traza
                        </button>
                    </div>
                    <div id="modalTraceContent" class="trace-viewer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')
<script>
    function showTraceModal(index, timestamp, level) {
        const traceText = document.getElementById('trace-data-' + index).value;
        document.getElementById('modalTraceTimestamp').textContent = timestamp;
        document.getElementById('modalTraceLevel').textContent = level;
        document.getElementById('modalTraceContent').textContent = traceText;

        const modal = new bootstrap.Modal(document.getElementById('modalStackTrace'));
        modal.show();
    }

    function copyStackTrace() {
        const content = document.getElementById('modalTraceContent').textContent;
        navigator.clipboard.writeText(content).then(() => {
            alert('Traza del error copiada al portapapeles.');
        });
    }

    function filterLogRows() {
        const query = document.getElementById('searchLogInput').value.toLowerCase();
        const rows = document.querySelectorAll('#logsTable tbody .log-row');
        let visible = 0;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        const badge = document.getElementById('visibleLogCount');
        if (badge) {
            badge.textContent = visible;
        }
    }
</script>
@endsection

