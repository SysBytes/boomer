@extends('layouts.backoffice')

@section('css_after')
<style>
    .backup-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .backup-actions .btn {
        margin-left: 0.5rem;
    }
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1.25rem;
        transition: all 0.2s ease;
    }
    .nav-tabs .nav-link:hover {
        color: #3b82f6;
        border-bottom-color: #93c5fd;
    }
    .nav-tabs .nav-link.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        background: transparent;
    }
    .nav-tabs .nav-link i {
        margin-right: 0.4rem;
    }
    .backup-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }
    .backup-table td {
        vertical-align: middle;
    }
    .backup-table .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    .badge-size {
        background: #e2e8f0;
        color: #475569;
        font-weight: 600;
        padding: 0.3em 0.65em;
        border-radius: 0.375rem;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }
    .file-tree {
        max-height: 450px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        background: #fff;
    }
    .file-tree-list {
        list-style-type: none;
        padding-left: 1.5rem;
        margin: 0;
    }
    .file-tree > .file-tree-list {
        padding-left: 0;
    }
    .tree-folder, .tree-file {
        margin: 0.15rem 0;
    }
    .tree-item-content {
        display: flex;
        align-items: center;
        padding: 0.4rem 0.5rem;
        border-radius: 0.25rem;
        transition: background-color 0.15s;
    }
    .tree-item-content:hover {
        background-color: #f1f5f9;
    }
    .tree-folder > .tree-item-content {
        cursor: pointer;
        font-weight: 600;
        color: #334155;
    }
    .tree-file .file-name {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        color: #475569;
    }
    .tree-file .file-size {
        font-size: 0.75rem;
        color: #94a3b8;
        white-space: nowrap;
    }
    .hidden-by-search {
        display: none !important;
    }
    .select-controls {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .select-controls .form-check {
        margin: 0;
    }
    .selected-count {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
    }
    .search-files {
        max-width: 300px;
    }
    .loading-spinner {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
    }
    .loading-spinner .spinner-border {
        width: 2rem;
        height: 2rem;
    }
    @media (max-width: 768px) {
        .backup-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .backup-actions {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .backup-actions .btn {
            margin-left: 0;
            flex: 1;
            min-width: 140px;
        }
    }
    .backup-progress-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.7);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .backup-progress-overlay .progress-box {
        max-width: 500px;
        width: 90%;
        border-top: 4px solid #3b82f6;
        border-radius: 0.5rem;
    }
</style>
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <!-- Header -->
        <div class="backup-header mb-4">
            <div>
                <h2 class="content-heading mb-0 pb-0">
                    <i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>Copias de Seguridad
                </h2>
                <p class="text-muted fs-sm mt-1 mb-0">Gestión de respaldos de archivos y base de datos del sistema</p>
            </div>
            <div class="backup-actions">
                <a href="{{ route('backups.settings') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-cog me-1"></i> Configuración
                </a>
                <form action="{{ route('backups.create_files') }}" method="POST" class="d-inline"
                      onsubmit="return handleBackupSubmit(this, '¿Crear un respaldo de todos los archivos del sistema? Este proceso puede tardar varios minutos.')">
                    @csrf
                    <button type="submit" class="btn btn-alt-info">
                        <i class="fa fa-file-zipper me-1"></i> Respaldar Archivos
                    </button>
                </form>
                <form action="{{ route('backups.create_database') }}" method="POST" class="d-inline"
                      onsubmit="return handleBackupSubmit(this, '¿Crear un respaldo de la base de datos?')">
                    @csrf
                    <button type="submit" class="btn btn-alt-success">
                        <i class="fa fa-database me-1"></i> Respaldar BD
                    </button>
                </form>
            </div>
        </div>

        <!-- Alerts -->
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

        <!-- Tabs -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-files" data-bs-toggle="tab" data-bs-target="#pane-files" type="button" role="tab" aria-controls="pane-files" aria-selected="true">
                            <i class="fa fa-folder-open"></i> Archivos
                            <span class="badge bg-primary ms-1">{{ count($fileBackups) }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-database" data-bs-toggle="tab" data-bs-target="#pane-database" type="button" role="tab" aria-controls="pane-database" aria-selected="false">
                            <i class="fa fa-database"></i> Base de Datos
                            <span class="badge bg-success ms-1">{{ count($dbBackups) }}</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="block-content tab-content">
                <!-- Tab: Archivos -->
                <div class="tab-pane active" id="pane-files" role="tabpanel" aria-labelledby="tab-files">
                    @if(count($fileBackups) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-vcenter backup-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45%;">Nombre del Archivo</th>
                                        <th style="width: 20%;">Fecha del Respaldo</th>
                                        <th style="width: 15%;">Tamaño</th>
                                        <th class="text-end" style="width: 20%;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fileBackups as $backup)
                                        <tr>
                                            <td>
                                                <i class="fa fa-file-zipper text-info me-2"></i>
                                                <strong>{{ $backup['filename'] }}</strong>
                                            </td>
                                            <td>
                                                <i class="fa fa-calendar-alt text-muted me-1"></i>
                                                {{ $backup['date'] }}
                                            </td>
                                            <td>
                                                <span class="badge badge-size">{{ $backup['size'] }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('backups.download', ['type' => 'files', 'filename' => $backup['filename']]) }}" class="btn btn-sm btn-alt-success" title="Descargar">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-alt-info"
                                                            title="Ver contenido"
                                                            onclick="viewContents('{{ $backup['filename'] }}')">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <form action="{{ route('backups.delete', ['type' => 'files', 'filename' => $backup['filename']]) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('¿Está seguro de eliminar este respaldo?\n\n{{ $backup['filename'] }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-alt-danger" title="Eliminar">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa fa-folder-open"></i>
                            <h5>No hay respaldos de archivos</h5>
                            <p>Haga clic en <strong>"Respaldar Archivos"</strong> para crear el primer respaldo.</p>
                        </div>
                    @endif
                </div>

                <!-- Tab: Base de Datos -->
                <div class="tab-pane" id="pane-database" role="tabpanel" aria-labelledby="tab-database">
                    @if(count($dbBackups) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-vcenter backup-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45%;">Nombre del Archivo</th>
                                        <th style="width: 20%;">Fecha del Respaldo</th>
                                        <th style="width: 15%;">Tamaño</th>
                                        <th class="text-end" style="width: 20%;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dbBackups as $backup)
                                        <tr>
                                            <td>
                                                <i class="fa fa-database text-success me-2"></i>
                                                <strong>{{ $backup['filename'] }}</strong>
                                            </td>
                                            <td>
                                                <i class="fa fa-calendar-alt text-muted me-1"></i>
                                                {{ $backup['date'] }}
                                            </td>
                                            <td>
                                                <span class="badge badge-size">{{ $backup['size'] }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('backups.download', ['type' => 'database', 'filename' => $backup['filename']]) }}" class="btn btn-sm btn-alt-success" title="Descargar">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <form action="{{ route('backups.restore_database', ['filename' => $backup['filename']]) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('⚠️ ATENCIÓN: Restaurar esta copia reemplazará TODA la base de datos actual.\n\n¿Está seguro de restaurar?\n\n{{ $backup['filename'] }}')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-alt-warning" title="Restaurar">
                                                            <i class="fa fa-rotate-left"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('backups.delete', ['type' => 'database', 'filename' => $backup['filename']]) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('¿Está seguro de eliminar este respaldo?\n\n{{ $backup['filename'] }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-alt-danger" title="Eliminar">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa fa-database"></i>
                            <h5>No hay respaldos de base de datos</h5>
                            <p>Haga clic en <strong>"Respaldar BD"</strong> para crear el primer respaldo.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->

    <!-- Progress Overlay -->
    <div id="backup-progress-overlay" class="backup-progress-overlay" style="display: none;">
        <div class="progress-box text-center p-4 bg-white shadow-lg">
            <h4 class="mb-3 text-primary"><i class="fa fa-spinner fa-spin me-2"></i>Realizando Respaldo</h4>
            <p class="text-muted mb-4 text-center">Por favor, no cierre ni recargue esta ventana. Este proceso puede tardar varios minutos dependiendo del tamaño de la información.</p>
            <div class="progress active" style="height: 20px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <!-- Modal: Ver Contenido del ZIP -->
    <div class="modal fade" id="modalContents" tabindex="-1" aria-labelledby="modalContentsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalContentsLabel">
                        <i class="fa fa-folder-tree text-info me-2"></i>Contenido del Respaldo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Loading -->
                    <div id="contents-loading" class="loading-spinner">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando contenido del respaldo...</p>
                        </div>
                    </div>

                    <!-- Error -->
                    <div id="contents-error" class="p-4 text-center" style="display: none;">
                        <i class="fa fa-exclamation-triangle text-danger fs-1 mb-3 d-block"></i>
                        <p class="text-danger" id="contents-error-msg"></p>
                    </div>

                    <!-- File List -->
                    <div id="contents-list" style="display: none;">
                        <div class="select-controls">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllFiles" onchange="toggleSelectAll()">
                                <label class="form-check-label fw-bold" for="selectAllFiles">Seleccionar todos</label>
                            </div>
                            <span class="selected-count" id="selectedCount">0 archivos seleccionados</span>
                            <input type="text" class="form-control form-control-sm search-files ms-auto" id="searchFiles" placeholder="Buscar archivo..." oninput="filterFiles()">
                        </div>
                        <div class="file-tree" id="fileTreeContainer">
                            <!-- Files will be populated via JS -->
                        </div>
                        <div class="p-3 bg-light border-top">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted fs-sm" id="totalFilesInfo">Total: 0 archivos</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <form id="restoreForm" action="{{ route('backups.restore_files') }}" method="POST">
                        @csrf
                        <input type="hidden" name="filename" id="restoreFilename">
                        <div id="restoreSelectedInputs"></div>
                        <button type="button" class="btn btn-warning" id="btnRestoreSelected" onclick="restoreSelected()" disabled>
                            <i class="fa fa-rotate-left me-1"></i> Restaurar Seleccionados
                        </button>
                        <button type="button" class="btn btn-danger" id="btnRestoreAll" onclick="restoreAll()">
                            <i class="fa fa-arrows-rotate me-1"></i> Restaurar Todo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')
<script>
    let currentFiles = [];
    let currentFilename = '';

    function handleBackupSubmit(form, message) {
        if (!confirm(message)) {
            return false;
        }

        // Deshabilitar todos los botones para prevenir doble clic
        document.querySelectorAll('.backup-actions .btn, .backup-table .btn').forEach(btn => {
            btn.style.pointerEvents = 'none';
            btn.classList.add('disabled');
        });
        // Mover el overlay al body para evitar problemas de z-index o transform en contenedores padre
        let overlay = document.getElementById('backup-progress-overlay');
        document.body.appendChild(overlay);
        
        // Mostrar overlay de progreso
        overlay.style.display = 'flex';
        
        return true;
    }

    function viewContents(filename) {
        currentFilename = filename;
        document.getElementById('restoreFilename').value = filename;

        // Reset modal state
        document.getElementById('contents-loading').style.display = 'flex';
        document.getElementById('contents-list').style.display = 'none';
        document.getElementById('contents-error').style.display = 'none';
        document.getElementById('selectAllFiles').checked = false;
        document.getElementById('searchFiles').value = '';
        document.getElementById('btnRestoreSelected').disabled = true;

        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('modalContents'));
        modal.show();

        // Fetch contents
        fetch("{{ url('backoffice/backups/files') }}/" + encodeURIComponent(filename) + "/contents", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('contents-loading').style.display = 'none';

            if (data.success) {
                currentFiles = data.files;
                renderFileTree(currentFiles);
                document.getElementById('contents-list').style.display = 'block';
                document.getElementById('totalFilesInfo').textContent = 'Total: ' + data.total + ' archivos';
            } else {
                document.getElementById('contents-error').style.display = 'block';
                document.getElementById('contents-error-msg').textContent = data.message;
            }
        })
        .catch(error => {
            document.getElementById('contents-loading').style.display = 'none';
            document.getElementById('contents-error').style.display = 'block';
            document.getElementById('contents-error-msg').textContent = 'Error de conexión: ' + error.message;
        });
    }

    function buildTree(files) {
        let root = {};
        files.forEach(file => {
            let parts = file.name.split('/');
            let current = root;
            for (let i = 0; i < parts.length; i++) {
                let part = parts[i];
                if (i === parts.length - 1) {
                    current[part] = { _type: 'file', data: file };
                } else {
                    if (!current[part]) {
                        current[part] = { _type: 'folder', children: {} };
                    }
                    current = current[part].children;
                }
            }
        });
        return root;
    }

    function renderTreeNodes(node, path = '') {
        let html = '<ul class="file-tree-list">';
        let keys = Object.keys(node).filter(k => k !== '_type' && k !== 'data' && k !== 'children');
        keys.sort((a, b) => {
            let isDirA = node[a]._type === 'folder';
            let isDirB = node[b]._type === 'folder';
            if (isDirA && !isDirB) return -1;
            if (!isDirA && isDirB) return 1;
            return a.localeCompare(b);
        });

        keys.forEach(key => {
            let item = node[key];
            let fullPath = path ? path + '/' + key : key;
            
            if (item._type === 'folder') {
                html += `
                    <li class="tree-folder closed">
                        <div class="tree-item-content">
                            <input type="checkbox" class="form-check-input folder-checkbox me-2" onchange="toggleFolderCheckbox(this)">
                            <div class="d-flex align-items-center cursor-pointer flex-grow-1" onclick="toggleFolder(this)">
                                <i class="fa fa-folder text-warning folder-icon me-2"></i>
                                <span class="folder-name">${escapeHtml(key)}</span>
                            </div>
                        </div>
                        <div class="tree-children" style="display: none;">
                            ${renderTreeNodes(item.children, fullPath)}
                        </div>
                    </li>
                `;
            } else {
                let file = item.data;
                let icon = getFileIcon(file.name);
                html += `
                    <li class="tree-file" data-name="${escapeHtml(file.name.toLowerCase())}">
                        <div class="tree-item-content">
                            <label class="d-flex align-items-center w-100 m-0 cursor-pointer">
                                <input type="checkbox" class="form-check-input file-checkbox me-2" value="${escapeHtml(file.name)}" onchange="updateSelectedCount(); updateParentFolderCheckboxes(this)">
                                <i class="${icon} text-muted me-2"></i>
                                <span class="file-name text-truncate" title="${escapeHtml(file.name)}">${escapeHtml(key)}</span>
                                <span class="file-size ms-auto">${file.size}</span>
                            </label>
                        </div>
                    </li>
                `;
            }
        });
        html += '</ul>';
        return html;
    }

    function renderFileTree(files) {
        var container = document.getElementById('fileTreeContainer');
        var tree = buildTree(files);
        container.innerHTML = renderTreeNodes(tree);
        updateSelectedCount();
    }

    function toggleFolder(element) {
        let li = element.closest('li');
        let children = li.querySelector('.tree-children');
        let icon = element.querySelector('.folder-icon');
        
        if (li.classList.contains('closed')) {
            li.classList.remove('closed');
            li.classList.add('open');
            children.style.display = 'block';
            icon.classList.remove('fa-folder');
            icon.classList.add('fa-folder-open');
        } else {
            li.classList.remove('open');
            li.classList.add('closed');
            children.style.display = 'none';
            icon.classList.remove('fa-folder-open');
            icon.classList.add('fa-folder');
        }
    }

    function toggleFolderCheckbox(checkbox) {
        let li = checkbox.closest('li.tree-folder');
        let childrenCheckboxes = li.querySelectorAll('.tree-children .file-checkbox:not(:disabled)');
        let folderCheckboxes = li.querySelectorAll('.tree-children .folder-checkbox:not(:disabled)');
        
        childrenCheckboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        
        folderCheckboxes.forEach(cb => {
            cb.checked = checkbox.checked;
            cb.indeterminate = false;
        });
        
        updateSelectedCount();
        updateParentFolderCheckboxes(checkbox);
    }

    function updateParentFolderCheckboxes(element) {
        let parentFolder = element.closest('.tree-children');
        while (parentFolder) {
            let folderLi = parentFolder.closest('.tree-folder');
            if (!folderLi) break;
            
            let folderCb = folderLi.querySelector('.tree-item-content > .folder-checkbox');
            if (folderCb) {
                let allFiles = folderLi.querySelectorAll('.tree-children .file-checkbox');
                let allChecked = folderLi.querySelectorAll('.tree-children .file-checkbox:checked');
                folderCb.checked = (allFiles.length > 0 && allFiles.length === allChecked.length);
                folderCb.indeterminate = (allChecked.length > 0 && allChecked.length < allFiles.length);
            }
            parentFolder = folderLi.closest('.tree-children');
        }
    }

    function getFileIcon(filename) {
        var ext = filename.split('.').pop().toLowerCase();
        var icons = {
            'php': 'fa-brands fa-php',
            'js': 'fa-brands fa-js',
            'css': 'fa fa-paintbrush',
            'html': 'fa-brands fa-html5',
            'json': 'fa fa-code',
            'md': 'fa fa-file-lines',
            'txt': 'fa fa-file-lines',
            'jpg': 'fa fa-image',
            'jpeg': 'fa fa-image',
            'png': 'fa fa-image',
            'gif': 'fa fa-image',
            'svg': 'fa fa-image',
            'webp': 'fa fa-image',
            'mp4': 'fa fa-video',
            'pdf': 'fa fa-file-pdf',
            'zip': 'fa fa-file-zipper',
            'env': 'fa fa-cog',
            'lock': 'fa fa-lock',
            'xml': 'fa fa-code',
            'sql': 'fa fa-database',
            'scss': 'fa-brands fa-sass',
        };
        return icons[ext] || 'fa fa-file';
    }

    function escapeHtml(text) {
        var map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function toggleSelectAll() {
        var checked = document.getElementById('selectAllFiles').checked;
        var visibleCheckboxes = document.querySelectorAll('.tree-file:not(.hidden-by-search) .file-checkbox');
        visibleCheckboxes.forEach(function(cb) {
            cb.checked = checked;
        });
        var visibleFolders = document.querySelectorAll('.tree-folder:not(.hidden-by-search) > .tree-item-content > .folder-checkbox');
        visibleFolders.forEach(function(cb) {
            cb.checked = checked;
            cb.indeterminate = false;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        var checked = document.querySelectorAll('.file-checkbox:checked');
        var count = checked.length;
        document.getElementById('selectedCount').textContent = count + ' archivo' + (count !== 1 ? 's' : '') + ' seleccionado' + (count !== 1 ? 's' : '');
        document.getElementById('btnRestoreSelected').disabled = count === 0;
    }

    function filterFiles() {
        var query = document.getElementById('searchFiles').value.toLowerCase();
        
        document.querySelectorAll('.tree-file').forEach(el => el.classList.remove('hidden-by-search'));
        document.querySelectorAll('.tree-folder').forEach(el => el.classList.remove('hidden-by-search'));

        if (!query) {
            // Restaurar estado por defecto: carpetas cerradas
            document.querySelectorAll('.tree-folder.open').forEach(li => {
                li.classList.remove('open');
                li.classList.add('closed');
                li.querySelector('.tree-children').style.display = 'none';
                let icon = li.querySelector('.folder-icon');
                icon.classList.remove('fa-folder-open');
                icon.classList.add('fa-folder');
            });
            return;
        }

        document.querySelectorAll('.tree-file').forEach(el => {
            var name = el.getAttribute('data-name');
            if (!name.includes(query)) {
                el.classList.add('hidden-by-search');
            } else {
                // Asegurar que las carpetas padre estén visibles y abiertas
                var parent = el.closest('.tree-folder');
                while (parent) {
                    parent.classList.remove('closed');
                    parent.classList.add('open');
                    parent.querySelector('.tree-children').style.display = 'block';
                    var icon = parent.querySelector('.folder-icon');
                    icon.classList.remove('fa-folder');
                    icon.classList.add('fa-folder-open');
                    parent = parent.parentElement.closest('.tree-folder');
                }
            }
        });

        // Ocultar carpetas que no tienen archivos visibles
        document.querySelectorAll('.tree-folder').forEach(el => {
            var hasVisible = false;
            el.querySelectorAll('.tree-file').forEach(fileEl => {
                if (!fileEl.classList.contains('hidden-by-search')) hasVisible = true;
            });
            if (!hasVisible) {
                el.classList.add('hidden-by-search');
            }
        });
    }

    function restoreSelected() {
        var checked = document.querySelectorAll('.file-checkbox:checked');
        if (checked.length === 0) {
            alert('Seleccione al menos un archivo para restaurar.');
            return;
        }

        if (!confirm('¿Restaurar ' + checked.length + ' archivo(s) seleccionado(s)?\n\nLos archivos existentes serán sobrescritos.')) {
            return;
        }

        var container = document.getElementById('restoreSelectedInputs');
        container.innerHTML = '';

        checked.forEach(function(cb) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_files[]';
            input.value = cb.value;
            container.appendChild(input);
        });

        document.getElementById('restoreForm').submit();
    }

    function restoreAll() {
        if (!confirm('⚠️ ATENCIÓN: Esto restaurará TODOS los archivos del respaldo.\n\nLos archivos existentes serán sobrescritos.\n\n¿Está seguro de continuar?')) {
            return;
        }

        var container = document.getElementById('restoreSelectedInputs');
        container.innerHTML = '<input type="hidden" name="restore_all" value="1">';

        document.getElementById('restoreForm').submit();
    }
</script>
@endsection
