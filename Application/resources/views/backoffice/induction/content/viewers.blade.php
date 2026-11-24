@extends('layouts.backoffice')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <style>
        #evidenceDetailDialog:modal {
            overscroll-behavior: contain;
        }

        #evidenceDetailDialog::backdrop {
            background: rgba(15, 23, 42, 0.72);
            overscroll-behavior: contain;
        }

        #evidenceDetailDialog {
            width: min(94dvw, 820px);
            max-width: 94dvw;
            max-height: 90dvh;
            margin: auto;
            padding: 0;
            border: 0;
            border-radius: 0.5rem;
            background: #ffffff;
            color: #1e293b;
            box-shadow: 0 1.25rem 3rem rgba(15, 23, 42, 0.35);
            overflow: hidden;
        }

        #evidenceDetailDialog[open] {
            display: flex;
            flex-direction: column;
        }

        .evidence-modal-body {
            flex: 1 1 auto;
            overflow: auto;
        }

        .evidence-preview-box {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
        }

        .evidence-preview-box img {
            max-width: 100%;
            max-height: 260px;
            object-fit: contain;
        }

        @media (max-width: 575.98px) {
            #evidenceDetailDialog {
                width: 100dvw;
                max-width: 100dvw;
                height: 100dvh;
                max-height: 100dvh;
                border-radius: 0;
                padding-bottom: env(safe-area-inset-bottom, 0px);
            }
        }

        @media (pointer: coarse) {
            #evidenceDetailDialog button {
                min-block-size: 44px;
            }
        }
    </style>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
    <script>
        document.addEventListener('submit', function (event) {
            const form = event.target.closest('form.js-confirm-reset-view');
            if (!form) {
                return;
            }
            const message = form.dataset.confirm || '¿Confirmas eliminar esta visualización?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const dialog = document.getElementById('evidenceDetailDialog');
            if (!dialog) {
                return;
            }

            const userEl = document.getElementById('evidenceModalUser');
            const emailEl = document.getElementById('evidenceModalEmail');
            const acceptedAtEl = document.getElementById('evidenceModalAcceptedAt');
            const viewedAtEl = document.getElementById('evidenceModalViewedAt');
            const ipEl = document.getElementById('evidenceModalIp');
            const scopeEl = document.getElementById('evidenceModalScope');

            const photoImg = document.getElementById('evidenceModalPhoto');
            const photoEmpty = document.getElementById('evidenceModalPhotoEmpty');
            const signatureImg = document.getElementById('evidenceModalSignature');
            const signatureEmpty = document.getElementById('evidenceModalSignatureEmpty');

            const closeBtn = document.getElementById('evidenceModalCloseBtn');
            const closeFooterBtn = document.getElementById('evidenceModalCloseFooterBtn');

            function closeDialog() {
                if (typeof dialog.close === 'function') {
                    dialog.close();
                } else {
                    dialog.removeAttribute('open');
                }
            }

            function resetImages() {
                if (photoImg) {
                    photoImg.removeAttribute('src');
                    photoImg.classList.add('d-none');
                }
                if (photoEmpty) {
                    photoEmpty.classList.remove('d-none');
                }
                if (signatureImg) {
                    signatureImg.removeAttribute('src');
                    signatureImg.classList.add('d-none');
                }
                if (signatureEmpty) {
                    signatureEmpty.classList.remove('d-none');
                }
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeDialog);
            }
            if (closeFooterBtn) {
                closeFooterBtn.addEventListener('click', closeDialog);
            }
            dialog.addEventListener('close', resetImages);

            document.addEventListener('click', function (event) {
                const trigger = event.target.closest('.js-open-evidence-modal');
                if (!trigger) {
                    return;
                }

                if (userEl) userEl.textContent = trigger.dataset.userName || '-';
                if (emailEl) emailEl.textContent = trigger.dataset.userEmail || '-';
                if (acceptedAtEl) acceptedAtEl.textContent = trigger.dataset.acceptedAt || '-';
                if (viewedAtEl) viewedAtEl.textContent = trigger.dataset.viewedAt || '-';
                if (ipEl) ipEl.textContent = trigger.dataset.ipAddress || 'N/D';
                if (scopeEl) scopeEl.textContent = trigger.dataset.evidenceScope || '-';

                resetImages();

                const photoUrl = trigger.dataset.photoUrl || '';
                if (photoUrl && photoImg) {
                    photoImg.src = photoUrl;
                    photoImg.classList.remove('d-none');
                    if (photoEmpty) photoEmpty.classList.add('d-none');
                }

                const signatureUrl = trigger.dataset.signatureUrl || '';
                if (signatureUrl && signatureImg) {
                    signatureImg.src = signatureUrl;
                    signatureImg.classList.remove('d-none');
                    if (signatureEmpty) signatureEmpty.classList.add('d-none');
                }

                if (typeof dialog.showModal === 'function') {
                    dialog.showModal();
                } else {
                    dialog.setAttribute('open', 'open');
                }
            });
        });
    </script>
@endsection

@section('content')
@php
    $isDeveloper = Auth::user()->hasRole('developer');
    $total = $recipients->count();
    $percent = $total > 0 ? round(($viewed->count() / $total) * 100) : 0;
@endphp
<div class="content">
    <h2 class="content-heading">Inducción - Visualización de Contenido</h2>

    @include('backoffice.induction._alerts')

    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">{{ $content->title }}</h3>
            <div class="block-options">
                <a href="{{ route('induction.contents.index') }}" class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-angle-left"></i> Atrás
                </a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="text-muted fs-sm">Categoría / Subcategoría</div>
                    <div class="fw-semibold">{{ $content->subcategory->category->name ?? 'N/A' }} / {{ $content->subcategory->name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted fs-sm">Destinatario</div>
                    <div>
                        @if($content->target_user_id && $content->targetUser)
                            <span class="badge bg-info"><i class="fa fa-user me-1"></i>{{ ucwords(mb_strtolower($content->targetUser->name)) }}</span>
                        @else
                            @php
                                $visibleRoles = $isDeveloper ? $content->roles : $content->roles->where('slug', '!=', 'developer');
                            @endphp
                            @forelse($visibleRoles as $role)
                                <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted">Todos los roles</span>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3 text-center mb-3">
                <div class="col-sm-4">
                    <div class="border rounded p-3">
                        <div class="fs-3 fw-bold text-success">{{ $viewed->count() }}</div>
                        <div class="text-muted fs-sm">Visualizados</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="border rounded p-3">
                        <div class="fs-3 fw-bold text-warning">{{ $pending->count() }}</div>
                        <div class="text-muted fs-sm">Pendientes</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="border rounded p-3">
                        <div class="fs-3 fw-bold">{{ $total }}</div>
                        <div class="text-muted fs-sm">Usuarios habilitados</div>
                    </div>
                </div>
            </div>

            <div class="progress mb-4" style="height: 10px;" role="progressbar" aria-label="Avance de visualización" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
            </div>

            <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-viewed" data-bs-toggle="tab" data-bs-target="#pane-viewed" type="button" role="tab" aria-controls="pane-viewed" aria-selected="true">
                        <i class="fa fa-circle-check text-success me-1"></i> Visualizados
                        <span class="badge bg-success ms-1">{{ $viewed->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-pending" data-bs-toggle="tab" data-bs-target="#pane-pending" type="button" role="tab" aria-controls="pane-pending" aria-selected="false">
                        <i class="fa fa-clock text-warning me-1"></i> Pendientes
                        <span class="badge bg-warning text-dark ms-1">{{ $pending->count() }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-3">
                <div class="tab-pane active" id="pane-viewed" role="tabpanel" aria-labelledby="tab-viewed">
                    @if($viewed->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">Ningún usuario ha visualizado este contenido todavía.</p>
                    @else
                        <table class="table table-bordered table-striped table-vcenter js-dataTable-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Email</th>
                                    <th scope="col" class="text-center">Fecha y hora de visualización</th>
                                    <th scope="col" class="text-center" style="width: 260px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewed as $row)
                                    @php
                                        $displayName = ucwords(mb_strtolower($row->user->name));
                                        $confirmMessage = ($content->is_mandatory && $content->is_active)
                                            ? "¿Eliminar la visualización de {$displayName}? El usuario volverá a estado pendiente y deberá completar nuevamente este contenido obligatorio y certificar su inducción."
                                            : "¿Eliminar la visualización de {$displayName}? El contenido volverá a figurar como pendiente para este usuario.";

                                        $evidence = $row->evidence ?? null;
                                        $hasPhoto = $evidence && !empty($evidence->evidence_photo_path);
                                        $hasSignature = $evidence && !empty($evidence->signature_path);
                                        $photoUrl = $hasPhoto ? route('induction.evidences.file', [$evidence->id, 'photo']) : '';
                                        $signatureUrl = $hasSignature ? route('induction.evidences.file', [$evidence->id, 'signature']) : '';
                                        $acceptedAtLabel = ($evidence && $evidence->accepted_at)
                                            ? $evidence->accepted_at->format('d/m/Y H:i:s')
                                            : $row->viewed_at->format('d/m/Y H:i:s');
                                        $evidenceScope = ($evidence && $evidence->content_id)
                                            ? 'Confirmación específica de este contenido'
                                            : 'Certificación general de inducción obligatoria';
                                    @endphp
                                    <tr>
                                        <td>{{ $displayName }}</td>
                                        <td>{{ $row->user->email }}</td>
                                        <td class="text-center" data-order="{{ $row->viewed_at->timestamp }}">{{ $row->viewed_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            @if($evidence)
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary me-1 mb-1 mb-sm-0 js-open-evidence-modal"
                                                    data-user-name="{{ $displayName }}"
                                                    data-user-email="{{ $row->user->email }}"
                                                    data-viewed-at="{{ $row->viewed_at->format('d/m/Y H:i:s') }}"
                                                    data-accepted-at="{{ $acceptedAtLabel }}"
                                                    data-ip-address="{{ $evidence->ip_address ?? 'N/D' }}"
                                                    data-photo-url="{{ $photoUrl }}"
                                                    data-signature-url="{{ $signatureUrl }}"
                                                    data-evidence-scope="{{ $evidenceScope }}"
                                                    aria-label="Ver confirmación de lectura de {{ $displayName }}"
                                                >
                                                    <i class="fa fa-id-card me-1" aria-hidden="true"></i> Ver confirmación
                                                </button>
                                            @else
                                                <span class="badge bg-secondary me-1">Sin evidencia</span>
                                            @endif

                                            <form action="{{ route('induction.contents.viewers.reset', [$content->id, $row->user->id]) }}"
                                                  method="POST"
                                                  class="d-inline js-confirm-reset-view"
                                                  data-confirm="{{ $confirmMessage }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Eliminar visualización y volver a exigir lectura"
                                                        aria-label="Eliminar visualización de {{ $displayName }}">
                                                    <i class="fa fa-trash-can me-1" aria-hidden="true"></i> Restablecer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="tab-pane" id="pane-pending" role="tabpanel" aria-labelledby="tab-pending">
                    @if($pending->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">No hay usuarios pendientes: todos los habilitados ya visualizaron este contenido.</p>
                    @else
                        <table class="table table-bordered table-striped table-vcenter js-dataTable-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Roles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pending as $user)
                                    <tr>
                                        <td>{{ ucwords(mb_strtolower($user->name)) }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles->when(!$isDeveloper, fn ($roles) => $roles->where('slug', '!=', 'developer')) as $role)
                                                <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<dialog id="evidenceDetailDialog" aria-labelledby="evidenceModalTitle" closedby="closerequest">
    <div class="d-flex align-items-center justify-content-between gap-2 px-3 py-3 border-bottom bg-body-light">
        <h2 id="evidenceModalTitle" class="h6 fw-semibold mb-0">
            <i class="fa fa-shield-halved text-primary me-1" aria-hidden="true"></i>Detalle de Confirmación de Lectura
        </h2>
        <button type="button" class="btn-close" id="evidenceModalCloseBtn" aria-label="Cerrar detalle"></button>
    </div>

    <div class="evidence-modal-body p-3 p-md-4">
        <div class="row g-2 bg-body-light border rounded p-3 mb-3 fs-sm">
            <div class="col-12 col-sm-6">
                <div class="text-muted fs-xs">Usuario</div>
                <div class="fw-semibold" id="evidenceModalUser">-</div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="text-muted fs-xs">Correo electrónico</div>
                <div class="fw-semibold" id="evidenceModalEmail">-</div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="text-muted fs-xs">Fecha y hora de confirmación</div>
                <div class="fw-semibold text-success" id="evidenceModalAcceptedAt">-</div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="text-muted fs-xs">Fecha y hora de visualización</div>
                <div class="fw-semibold" id="evidenceModalViewedAt">-</div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="text-muted fs-xs">Dirección IP</div>
                <div class="fw-semibold" id="evidenceModalIp">-</div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="text-muted fs-xs">Origen de confirmación</div>
                <div class="fw-semibold" id="evidenceModalScope">-</div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="border rounded overflow-hidden h-100 d-flex flex-column">
                    <div class="px-3 py-2 border-bottom bg-body-light fw-semibold fs-sm">
                        <i class="fa fa-camera text-primary me-1" aria-hidden="true"></i>Selfie de verificación
                    </div>
                    <div class="p-2 flex-grow-1 evidence-preview-box">
                        <img id="evidenceModalPhoto" class="d-none rounded border" alt="Selfie de confirmación de lectura">
                        <div id="evidenceModalPhotoEmpty" class="text-muted fs-sm text-center py-4">
                            No hay selfie registrada.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="border rounded overflow-hidden h-100 d-flex flex-column">
                    <div class="px-3 py-2 border-bottom bg-body-light fw-semibold fs-sm">
                        <i class="fa fa-signature text-primary me-1" aria-hidden="true"></i>Firma digital
                    </div>
                    <div class="p-2 flex-grow-1 evidence-preview-box bg-white">
                        <img id="evidenceModalSignature" class="d-none rounded border" alt="Firma digital de confirmación de lectura">
                        <div id="evidenceModalSignatureEmpty" class="text-muted fs-sm text-center py-4">
                            No hay firma digital registrada.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-3 py-2 border-top bg-body-light d-flex justify-content-end">
        <button type="button" class="btn btn-secondary w-100 w-sm-auto" id="evidenceModalCloseFooterBtn">Cerrar</button>
    </div>
</dialog>
@stop
