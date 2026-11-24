@extends('layouts.backoffice')

@section('css_before')
    <style>
        .audit-thumb-btn {
            padding: 0;
            border: 2px solid #e2e8f0;
            border-radius: 0.375rem;
            overflow: hidden;
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            transition: transform 0.15s ease, border-color 0.15s ease;
        }

        .audit-thumb-btn:hover {
            transform: scale(1.06);
            border-color: #0284c7;
        }

        .audit-thumb-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .audit-evidence-img-box {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
        }

        .audit-evidence-img-box img {
            max-width: 100%;
            max-height: 280px;
            object-fit: contain;
        }
    </style>
@endsection

@section('content')
<div class="content">
    <h2 class="content-heading">
        <i class="fa-solid fa-clipboard-check text-primary me-2" aria-hidden="true"></i>Auditoría Gerencial de Inducción
    </h2>

    <!-- Indicadores gerenciales -->
    <div class="row g-3 mb-4 text-center">
        <div class="col-6 col-lg-3">
            <div class="block block-rounded mb-0 border">
                <div class="block-content py-3">
                    <div class="fs-2 fw-bold text-dark">{{ $stats['total_employees'] }}</div>
                    <div class="text-muted fs-sm">Usuarios validados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="block block-rounded mb-0 border">
                <div class="block-content py-3">
                    <div class="fs-2 fw-bold text-success">{{ $stats['completed'] }}</div>
                    <div class="text-muted fs-sm">Inducción completada</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="block block-rounded mb-0 border">
                <div class="block-content py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $stats['pending'] }}</div>
                    <div class="text-muted fs-sm">Inducción pendiente</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="block block-rounded mb-0 border">
                <div class="block-content py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $stats['total_evidences'] }}</div>
                    <div class="text-muted fs-sm">Evidencias recolectadas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros gerenciales -->
    <div class="block block-rounded mb-4">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-filter text-primary me-1" aria-hidden="true"></i>Filtros de Auditoría
            </h3>
        </div>
        <div class="block-content pb-4">
            <form action="{{ route('induction.audit.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="audit_search">Empleado (nombre o correo)</label>
                        <input
                            type="text"
                            id="audit_search"
                            name="search"
                            class="form-control"
                            placeholder="Ej. Ana Gómez o correo@..."
                            value="{{ $filters['search'] }}"
                        >
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="form-label" for="audit_status">Estado</label>
                        <select id="audit_status" name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="completed" {{ $filters['status'] === 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label" for="audit_date_from">Certificado desde</label>
                        <input type="date" id="audit_date_from" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label" for="audit_date_to">Hasta</label>
                        <input type="date" id="audit_date_to" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fa fa-search me-1" aria-hidden="true"></i> Filtrar
                        </button>
                        <a href="{{ route('induction.audit.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros" aria-label="Limpiar filtros">
                            <i class="fa fa-rotate-left" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla paginada de auditoría -->
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Registros de Cumplimiento y Evidencias</h3>
            <div class="block-options">
                <span class="badge bg-primary">{{ $employees->total() }} resultado(s)</span>
            </div>
        </div>
        <div class="block-content block-content-full">
            @if($employees->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fa fa-inbox fa-2x mb-2 d-block mx-auto" aria-hidden="true"></i>
                    No se encontraron usuarios con contenidos vistos y validados para los filtros seleccionados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Empleado</th>
                                <th scope="col">Rol</th>
                                <th scope="col" class="text-center">Estado</th>
                                <th scope="col" class="text-center">Fecha y Hora de Certificación</th>
                                <th scope="col" class="text-center">Dirección IP</th>
                                <th scope="col" class="text-center">Fotografía</th>
                                <th scope="col" class="text-center">Auditoría</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                @php
                                    $displayName = ucwords(mb_strtolower($employee->name));
                                    $visibleRoles = $isDeveloper
                                        ? $employee->roles
                                        : $employee->roles->where('slug', '!=', 'developer');
                                    $latestEvidence = $employee->latestInductionEvidence;
                                    $photoThumbUrl = ($latestEvidence && $latestEvidence->evidence_photo_path)
                                        ? route('induction.audit.evidence_file', [$latestEvidence->id, 'photo'])
                                        : null;
                                    $detailUrl = route('induction.audit.show', $employee->id);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $displayName }}</div>
                                        <div class="text-muted fs-xs">{{ $employee->email }}</div>
                                    </td>
                                    <td>
                                        @forelse($visibleRoles as $role)
                                            <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted fs-xs">Sin rol</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        @if($employee->induction_status === 'completed')
                                            <span class="badge bg-success">
                                                <i class="fa fa-circle-check me-1" aria-hidden="true"></i>Completado
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fa fa-clock me-1" aria-hidden="true"></i>Pendiente
                                            </span>
                                        @endif
                                        <div class="fs-xs text-muted mt-1">
                                            {{ $employee->completed_contents_count }} doc(s) vistos
                                        </div>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        @if($latestEvidence && $latestEvidence->accepted_at)
                                            {{ $latestEvidence->accepted_at->format('d/m/Y H:i:s') }}
                                        @else
                                            <span class="text-muted fs-xs">Sin certificar</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        @if($latestEvidence && $latestEvidence->ip_address)
                                            <code>{{ $latestEvidence->ip_address }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($photoThumbUrl)
                                            <button
                                                type="button"
                                                class="audit-thumb-btn js-open-audit-detail"
                                                data-bs-toggle="modal"
                                                data-bs-target="#auditDetailModal"
                                                data-detail-url="{{ $detailUrl }}"
                                                data-employee-name="{{ $displayName }}"
                                                title="Ampliar fotografía y ver desglose"
                                                aria-label="Ver fotografía ampliada de {{ $displayName }}"
                                            >
                                                <img src="{{ $photoThumbUrl }}" alt="Miniatura de evidencia de {{ $displayName }}" loading="lazy">
                                            </button>
                                            @if($employee->induction_evidences_count > 1)
                                                <div class="fs-xs text-muted mt-1">{{ $employee->induction_evidences_count }} registros</div>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-muted border">Sin foto</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary js-open-audit-detail"
                                            data-bs-toggle="modal"
                                            data-bs-target="#auditDetailModal"
                                            data-detail-url="{{ $detailUrl }}"
                                            data-employee-name="{{ $displayName }}"
                                            aria-label="Ver detalle de auditoría de {{ $displayName }}"
                                        >
                                            <i class="fa fa-magnifying-glass me-1" aria-hidden="true"></i> Ver detalle
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-4">
                    <div class="fs-sm text-muted">
                        Mostrando {{ $employees->firstItem() }} a {{ $employees->lastItem() }} de {{ $employees->total() }} empleado(s)
                    </div>
                    <div>
                        {{ $employees->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Bootstrap 5 de Auditoría Detallada -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" aria-labelledby="auditDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content bg-white">
            <div class="modal-header bg-body-light">
                <h5 class="modal-title fw-semibold" id="auditDetailModalLabel">
                    <i class="fa fa-shield-halved text-primary me-1" aria-hidden="true"></i>
                    <span id="auditModalTitleText">Detalle de Auditoría</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body bg-white">
                <div id="auditModalLoading" class="text-center py-5">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mb-0 fs-sm">Cargando evidencias y desglose de contenidos...</p>
                </div>

                <div id="auditModalError" class="alert alert-danger d-none" role="alert"></div>

                <div id="auditModalContent" class="d-none">
                    <!-- Resumen del empleado -->
                    <div class="row g-2 bg-body-light border rounded p-3 mb-4 fs-sm">
                        <div class="col-12 col-md-4">
                            <div class="text-muted fs-xs">Empleado</div>
                            <div class="fw-semibold" id="auditEmployeeName">-</div>
                            <div class="text-muted fs-xs" id="auditEmployeeEmail">-</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-muted fs-xs">Rol(es)</div>
                            <div id="auditEmployeeRoles" class="mt-1"></div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-muted fs-xs">Estado de Inducción</div>
                            <div id="auditEmployeeStatus" class="mt-1"></div>
                        </div>
                    </div>

                    <!-- Evidencias fotográficas y firmas -->
                    <h6 class="fw-semibold mb-3">
                        <i class="fa fa-camera text-primary me-1" aria-hidden="true"></i>Evidencias Recolectadas (Selfie, Firma e IP)
                    </h6>
                    <div id="auditEvidencesContainer" class="mb-4"></div>

                    <!-- Desglose de documentos vistos y tiempos -->
                    <h6 class="fw-semibold mb-3">
                        <i class="fa fa-list-check text-primary me-1" aria-hidden="true"></i>Desglose de Contenidos y Tiempos Registrados
                    </h6>
                    <div id="auditProgressContainer"></div>
                </div>
            </div>
            <div class="modal-footer bg-body-light">
                <span class="me-auto fs-xs text-muted">
                    <i class="fa fa-lock me-1" aria-hidden="true"></i>Registro de auditoría inmutable (solo lectura)
                </span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_after')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('auditDetailModal');
        if (!modalEl) {
            return;
        }

        const titleText = document.getElementById('auditModalTitleText');
        const loadingBox = document.getElementById('auditModalLoading');
        const errorBox = document.getElementById('auditModalError');
        const contentBox = document.getElementById('auditModalContent');

        const empNameEl = document.getElementById('auditEmployeeName');
        const empEmailEl = document.getElementById('auditEmployeeEmail');
        const empRolesEl = document.getElementById('auditEmployeeRoles');
        const empStatusEl = document.getElementById('auditEmployeeStatus');
        const evidencesContainer = document.getElementById('auditEvidencesContainer');
        const progressContainer = document.getElementById('auditProgressContainer');

        let activeRequestId = 0;

        function createEl(tag, className, text) {
            const el = document.createElement(tag);
            if (className) el.className = className;
            if (typeof text === 'string') el.textContent = text;
            return el;
        }

        function renderEvidences(evidences) {
            evidencesContainer.replaceChildren();

            if (!evidences || evidences.length === 0) {
                evidencesContainer.appendChild(
                    createEl('div', 'alert alert-warning mb-0 fs-sm', 'Este empleado aún no tiene evidencias fotográficas o firmas registradas.')
                );
                return;
            }

            evidences.forEach(function (ev) {
                const card = createEl('div', 'border rounded bg-white mb-3 overflow-hidden');

                const header = createEl('div', 'px-3 py-2 bg-body-light border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 fs-sm');
                const scopeSpan = createEl('strong', '', ev.scope_label || 'Certificación de inducción');
                const metaSpan = createEl('span', 'text-muted fs-xs', 'Fecha: ' + ev.accepted_at_label + ' · IP: ' + ev.ip_address);
                header.appendChild(scopeSpan);
                header.appendChild(metaSpan);
                card.appendChild(header);

                const body = createEl('div', 'p-3');
                const row = createEl('div', 'row g-3');

                // Columna Selfie
                const photoCol = createEl('div', 'col-12 col-md-6');
                const photoBox = createEl('div', 'border rounded overflow-hidden h-100 d-flex flex-column');
                const photoTitleBar = createEl('div', 'px-3 py-2 border-bottom bg-body-light d-flex justify-content-between align-items-center fs-xs fw-semibold');
                photoTitleBar.appendChild(createEl('span', '', 'Selfie de verificación'));
                if (ev.photo_url) {
                    const photoLink = createEl('a', 'btn btn-xs btn-outline-primary py-0 px-2', 'Abrir archivo');
                    photoLink.href = ev.photo_url;
                    photoLink.target = '_blank';
                    photoLink.rel = 'noopener noreferrer';
                    photoTitleBar.appendChild(photoLink);
                }
                photoBox.appendChild(photoTitleBar);

                const photoWrap = createEl('div', 'p-2 flex-grow-1 audit-evidence-img-box');
                if (ev.photo_url) {
                    const img = document.createElement('img');
                    img.src = ev.photo_url;
                    img.alt = 'Fotografía de evidencia';
                    img.className = 'rounded border';
                    photoWrap.appendChild(img);
                } else {
                    photoWrap.appendChild(createEl('span', 'text-muted fs-xs', 'Sin fotografía registrada.'));
                }
                photoBox.appendChild(photoWrap);
                photoCol.appendChild(photoBox);
                row.appendChild(photoCol);

                // Columna Firma
                const sigCol = createEl('div', 'col-12 col-md-6');
                const sigBox = createEl('div', 'border rounded overflow-hidden h-100 d-flex flex-column');
                const sigTitleBar = createEl('div', 'px-3 py-2 border-bottom bg-body-light d-flex justify-content-between align-items-center fs-xs fw-semibold');
                sigTitleBar.appendChild(createEl('span', '', 'Firma digital'));
                if (ev.signature_url) {
                    const sigLink = createEl('a', 'btn btn-xs btn-outline-primary py-0 px-2', 'Abrir archivo');
                    sigLink.href = ev.signature_url;
                    sigLink.target = '_blank';
                    sigLink.rel = 'noopener noreferrer';
                    sigTitleBar.appendChild(sigLink);
                }
                sigBox.appendChild(sigTitleBar);

                const sigWrap = createEl('div', 'p-2 flex-grow-1 audit-evidence-img-box bg-white');
                if (ev.signature_url) {
                    const sigImg = document.createElement('img');
                    sigImg.src = ev.signature_url;
                    sigImg.alt = 'Firma digital de evidencia';
                    sigImg.className = 'rounded border';
                    sigWrap.appendChild(sigImg);
                } else {
                    sigWrap.appendChild(createEl('span', 'text-muted fs-xs', 'Sin firma digital registrada.'));
                }
                sigBox.appendChild(sigWrap);
                sigCol.appendChild(sigBox);
                row.appendChild(sigCol);

                body.appendChild(row);
                card.appendChild(body);
                evidencesContainer.appendChild(card);
            });
        }

        function renderProgress(items) {
            progressContainer.replaceChildren();

            if (!items || items.length === 0) {
                progressContainer.appendChild(
                    createEl('p', 'text-muted fs-sm mb-0', 'Aún no existen registros de visualización en user_content_progress para este empleado.')
                );
                return;
            }

            const wrapper = createEl('div', 'table-responsive');
            const table = createEl('table', 'table table-sm table-bordered table-striped mb-0 fs-sm');

            const thead = document.createElement('thead');
            const headRow = document.createElement('tr');
            ['Contenido', 'Categoría / Subcategoría', 'Tipo', 'Tiempo', 'Estado', 'Completado'].forEach(function (label) {
                const th = createEl('th', '', label);
                th.setAttribute('scope', 'col');
                headRow.appendChild(th);
            });
            thead.appendChild(headRow);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            items.forEach(function (item) {
                const tr = document.createElement('tr');

                const titleTd = document.createElement('td');
                titleTd.appendChild(createEl('span', 'fw-semibold', item.title));
                if (item.is_mandatory) {
                    titleTd.appendChild(document.createTextNode(' '));
                    titleTd.appendChild(createEl('span', 'badge bg-primary', 'Obligatorio'));
                }
                tr.appendChild(titleTd);

                tr.appendChild(createEl('td', '', item.category + ' / ' + item.subcategory));
                tr.appendChild(createEl('td', 'text-center', item.type));
                tr.appendChild(createEl('td', 'text-center text-nowrap', item.view_time_label));

                const statusTd = createEl('td', 'text-center');
                const badge = createEl(
                    'span',
                    item.status === 'completed' ? 'badge bg-success' : 'badge bg-warning text-dark',
                    item.status === 'completed' ? 'Completado' : 'Pendiente'
                );
                statusTd.appendChild(badge);
                tr.appendChild(statusTd);

                tr.appendChild(createEl('td', 'text-center text-nowrap', item.completed_at_label));
                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            wrapper.appendChild(table);
            progressContainer.appendChild(wrapper);
        }

        modalEl.addEventListener('show.bs.modal', async function (event) {
            const trigger = event.relatedTarget;
            if (!trigger || !trigger.dataset.detailUrl) {
                return;
            }

            const requestId = ++activeRequestId;
            titleText.textContent = 'Auditoría: ' + (trigger.dataset.employeeName || '');
            loadingBox.classList.remove('d-none');
            errorBox.classList.add('d-none');
            contentBox.classList.add('d-none');
            evidencesContainer.replaceChildren();
            progressContainer.replaceChildren();

            try {
                const res = await fetch(trigger.dataset.detailUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (requestId !== activeRequestId) {
                    return;
                }

                if (!res.ok) {
                    throw new Error('No se pudo cargar el detalle de auditoría (HTTP ' + res.status + ').');
                }

                const data = await res.json();
                if (requestId !== activeRequestId) {
                    return;
                }

                empNameEl.textContent = data.employee.name;
                empEmailEl.textContent = data.employee.email;

                empRolesEl.replaceChildren();
                if (data.employee.roles && data.employee.roles.length > 0) {
                    data.employee.roles.forEach(function (roleName) {
                        empRolesEl.appendChild(createEl('span', 'badge bg-secondary me-1', roleName));
                    });
                } else {
                    empRolesEl.appendChild(createEl('span', 'text-muted fs-xs', 'Sin rol'));
                }

                empStatusEl.replaceChildren();
                const isDone = data.employee.induction_status === 'completed';
                empStatusEl.appendChild(
                    createEl('span', isDone ? 'badge bg-success' : 'badge bg-warning text-dark', isDone ? 'Completado' : 'Pendiente')
                );

                renderEvidences(data.evidences);
                renderProgress(data.progress);

                loadingBox.classList.add('d-none');
                contentBox.classList.remove('d-none');
            } catch (err) {
                if (requestId !== activeRequestId) {
                    return;
                }
                loadingBox.classList.add('d-none');
                errorBox.textContent = (err && err.message) ? err.message : 'Ocurrió un error al consultar la auditoría.';
                errorBox.classList.remove('d-none');
            }
        });
    });
</script>
@endsection
