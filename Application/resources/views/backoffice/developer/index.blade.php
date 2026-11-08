@extends('layouts.backoffice')

@section('css_after')
<style>
    .btn-dev-action {
        display: block;
        width: 100%;
        color: #dc2626;
        background-color: #ffffff;
        border: 1px solid #ef4444;
        border-radius: 0.375rem;
        padding: 0.65rem 1rem;
        font-weight: 600;
        text-align: center;
        transition: all 0.15s ease-in-out;
        margin-bottom: 0.65rem;
        line-height: 1.4;
    }
    .btn-dev-action:hover:not(:disabled) {
        color: #ffffff;
        background-color: #dc2626;
        border-color: #dc2626;
    }
    .btn-dev-action:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .dev-console {
        width: 100%;
        min-height: 360px;
        background-color: #181f2c;
        color: #cbd5e1;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.875rem;
        line-height: 1.5;
        padding: 1rem 1.25rem;
        border-radius: 0.375rem;
        border: 1px solid #2d3748;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-word;
        resize: vertical;
    }
</style>
@endsection

@section('content')
    <div class="content">
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <i class="fa fa-terminal text-danger me-2"></i>Herramientas de Desarrollo
                </h3>
            </div>
            <div class="block-content pb-4">
                <p class="text-muted mb-4">
                    Acciones administrativas exclusivas para desarrolladores. Úsalas con precaución.
                </p>

                <div class="row g-4">
                    <!-- Botones de Acción -->
                    <div class="col-md-4">
                        <button type="button" class="btn-dev-action" onclick="runDevCommand('optimize:clear', 'Limpiar Caché General (optimize:clear)')">
                            <i class="fa fa-paintbrush me-1"></i> Limpiar Caché General<br>(optimize:clear)
                        </button>

                        <button type="button" class="btn-dev-action" onclick="runDevCommand('route:cache', 'Caché Rutas (route:cache)')">
                            <i class="fa fa-route me-1"></i> Caché Rutas (route:cache)
                        </button>

                        <button type="button" class="btn-dev-action" onclick="runDevCommand('view:cache', 'Caché Vistas (view:cache)')">
                            <i class="fa fa-eye me-1"></i> Caché Vistas (view:cache)
                        </button>

                        <button type="button" class="btn-dev-action" onclick="runDevCommand('config:cache', 'Caché Configuración (config:cache)')">
                            <i class="fa fa-gears me-1"></i> Caché Configuración (config:cache)
                        </button>

                        <button type="button" class="btn-dev-action" onclick="runDevCommand('storage:link', 'Link Storage (storage:link)')">
                            <i class="fa fa-folder me-1"></i> Link Storage (storage:link)
                        </button>

                        <button type="button" class="btn-dev-action" onclick="runDevCommand('migrate', 'Migrar Base de Datos (migrate)', true)">
                            <i class="fa fa-database me-1"></i> Migrar Base de Datos (migrate)
                        </button>

                        <button type="button" class="btn-dev-action" onclick="runDevCommand('migrate:status', 'Estado de Migraciones (migrate:status)')">
                            <i class="fa fa-list-check me-1"></i> Estado de Migraciones (migrate:status)
                        </button>

                        <button type="button" class="btn-dev-action mb-0" onclick="runDevCommand('check-missing-checkouts', 'Recordatorios Check-Out (check-missing-checkouts)')">
                            <i class="fa fa-bell me-1"></i> Notificaciones Check-Out
                        </button>
                    </div>

                    <!-- Salida de la consola -->
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold text-dark mb-0">Salida de la consola:</label>
                            <button type="button" class="btn btn-sm btn-alt-secondary" onclick="clearConsole()">
                                <i class="fa fa-eraser me-1"></i> Limpiar salida
                            </button>
                        </div>
                        <div id="dev-console-output" class="dev-console">Esperando ejecución de comando...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')
<script>
    function clearConsole() {
        const consoleEl = document.getElementById('dev-console-output');
        consoleEl.textContent = 'Esperando ejecución de comando...';
    }

    function setButtonsDisabled(disabled) {
        document.querySelectorAll('.btn-dev-action').forEach(btn => {
            btn.disabled = disabled;
        });
    }

    function runDevCommand(action, label, requireConfirm = false) {
        if (requireConfirm) {
            if (!confirm('¿Estás seguro de que deseas ejecutar "' + label + '" en este entorno?')) {
                return;
            }
        }

        const consoleEl = document.getElementById('dev-console-output');
        setButtonsDisabled(true);

        const now = new Date().toLocaleTimeString();
        consoleEl.textContent = `[${now}] Ejecutando: ${label}...\nPor favor espera...`;

        fetch("{{ route('developer.execute') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ action: action })
        })
        .then(async response => {
            const data = await response.json();
            return { status: response.status, data: data };
        })
        .then(({ status, data }) => {
            const statusText = data.success ? '✓ COMPLETADO' : '✗ ERROR';
            const header = `$ ${data.command || action}  [${data.timestamp || now}] — ${statusText}\n`
                         + `${'-'.repeat(65)}\n`;
            consoleEl.textContent = header + (data.output || 'Sin salida.');
        })
        .catch(error => {
            consoleEl.textContent = `✗ Error de red o servidor al ejecutar el comando:\n${error.message}`;
        })
        .finally(() => {
            setButtonsDisabled(false);
        });
    }
</script>
@endsection

