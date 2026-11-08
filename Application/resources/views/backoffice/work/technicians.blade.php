@extends('layouts.backoffice')

@section('css_before')
    <!-- Custom Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Custom Select2 JS -->
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>Codebase.helpersOnLoad(['jq-select2']);</script>

    <script>
        jQuery(function () {
            // Work selection change event
            jQuery('#work_id').on('change', function () {
                let workId = jQuery(this).val();
                let techniciansList = jQuery('#technicians_list');
                let assistantsList = jQuery('#assistants_list');
                let externalsList = jQuery('#externals_list');

                if (!workId) {
                    techniciansList.html('<tr><td colspan="2" class="text-center">Seleccione una obra para ver los técnicos asignados</td></tr>');
                    assistantsList.html('<tr><td colspan="2" class="text-center">Seleccione una obra para ver los auxiliares asignados</td></tr>');
                    externalsList.html('<tr><td colspan="2" class="text-center">Seleccione una obra para ver los externos asignados</td></tr>');
                    jQuery('.assign-section').hide();
                    return;
                }

                // Show loading
                techniciansList.html('<tr><td colspan="2" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
                assistantsList.html('<tr><td colspan="2" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
                externalsList.html('<tr><td colspan="2" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
                jQuery('.assign-section').show();

                // Fetch assigned users
                loadUsers(workId);
            });

            // Add technician form submit
            jQuery('#form_add_technician').on('submit', function (e) {
                e.preventDefault();
                submitAssignment(jQuery('#work_id').val(), jQuery('#user_id').val(), '#user_id');
            });

            // Add assistant form submit
            jQuery('#form_add_assistant').on('submit', function (e) {
                e.preventDefault();
                submitAssignment(jQuery('#work_id').val(), jQuery('#assistant_id').val(), '#assistant_id');
            });

            // Add external form submit
            jQuery('#form_add_external').on('submit', function (e) {
                e.preventDefault();
                submitAssignment(jQuery('#work_id').val(), jQuery('#external_id').val(), '#external_id');
            });
        });

        function submitAssignment(workId, userId, selectSelector) {
            if (!workId || !userId) {
                alert('Debe seleccionar una obra y un usuario.');
                return;
            }

            jQuery.ajax({
                url: '/backoffice/technicians/' + workId + '/add',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId
                },
                success: function (response) {
                    if (response.success) {
                        loadUsers(workId);
                        // Reset select definition
                        jQuery(selectSelector).val('').trigger('change');
                    } else {
                        alert(response.message || 'Error al asignar el usuario.');
                    }
                },
                error: function (xhr) {
                    alert('Ocurrió un error al procesar la solicitud.');
                }
            });
        }

        // Function to reload the assigned users tables
        function loadUsers(workId) {
            let techniciansList = jQuery('#technicians_list');
            let assistantsList = jQuery('#assistants_list');
            let externalsList = jQuery('#externals_list');

            jQuery.ajax({
                url: '/backoffice/technicians/' + workId + '/get',
                type: 'GET',
                success: function (response) {
                    // Load Technicians
                    if (response.assigned && response.assigned.length > 0) {
                        let html = '';
                        response.assigned.forEach(function (tech) {
                            html += '<tr>';
                            html += '<td>' + (tech.name || '') + '</td>';
                            html += '<td class="text-center">';
                            html += '<button type="button" class="btn btn-sm btn-danger" onclick="removeUser(' + workId + ', ' + tech.id + ')" title="Remover">';
                            html += '<i class="fa fa-times"></i>';
                            html += '</button>';
                            html += '</td>';
                            html += '</tr>';
                        });
                        techniciansList.html(html);
                    } else {
                        techniciansList.html('<tr><td colspan="2" class="text-center">No hay técnicos asignados a esta obra</td></tr>');
                    }

                    // Load Assistants
                    if (response.assigned_assistants && response.assigned_assistants.length > 0) {
                        let html = '';
                        response.assigned_assistants.forEach(function (ast) {
                            html += '<tr>';
                            html += '<td>' + (ast.name || '') + '</td>';
                            html += '<td class="text-center">';
                            html += '<button type="button" class="btn btn-sm btn-danger" onclick="removeUser(' + workId + ', ' + ast.id + ')" title="Remover">';
                            html += '<i class="fa fa-times"></i>';
                            html += '</button>';
                            html += '</td>';
                            html += '</tr>';
                        });
                        assistantsList.html(html);
                    } else {
                        assistantsList.html('<tr><td colspan="2" class="text-center">No hay auxiliares asignados a esta obra</td></tr>');
                    }

                    // Load Externals
                    if (response.assigned_externals && response.assigned_externals.length > 0) {
                        let html = '';
                        response.assigned_externals.forEach(function (ext) {
                            html += '<tr>';
                            html += '<td>' + (ext.name || '') + '</td>';
                            html += '<td class="text-center">';
                            html += '<button type="button" class="btn btn-sm btn-danger" onclick="removeUser(' + workId + ', ' + ext.id + ')" title="Remover">';
                            html += '<i class="fa fa-times"></i>';
                            html += '</button>';
                            html += '</td>';
                            html += '</tr>';
                        });
                        externalsList.html(html);
                    } else {
                        externalsList.html('<tr><td colspan="2" class="text-center">No hay externos asignados a esta obra</td></tr>');
                    }
                },
                error: function () {
                    techniciansList.html('<tr><td colspan="2" class="text-center text-danger">Error al cargar la lista</td></tr>');
                    assistantsList.html('<tr><td colspan="2" class="text-center text-danger">Error al cargar la lista</td></tr>');
                    externalsList.html('<tr><td colspan="2" class="text-center text-danger">Error al cargar la lista</td></tr>');
                }
            });
        }

        // Function to remove a user from work
        function removeUser(workId, userId) {
            if (confirm('¿Está seguro de remover a este usuario de la obra?')) {
                jQuery.ajax({
                    url: '/backoffice/technicians/' + workId + '/remove',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: userId
                    },
                    success: function (response) {
                        if (response.success) {
                            loadUsers(workId);
                        } else {
                            alert('Error al remover el usuario.');
                        }
                    },
                    error: function () {
                        alert('Ocurrió un error al procesar la solicitud.');
                    }
                });
            }
        }
    </script>
@endsection

@section('content')
    <div class="content">
        <h2 class="content-heading">Usuarios por Obra</h2>

        <div class="row">
            <!-- Project Selection -->
            <div class="col-md-5">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Seleccionar Obra</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="form-group row">
                            <label class="form-label" for="work_id">Obra</label>
                            <div class="col-12">
                                <select class="js-select2 form-select" id="work_id" name="work_id" style="width: 100%;" data-placeholder="Seleccione un proyecto...">
                                    <option></option>
                                    @foreach($works as $work)
                                        <option value="{{ $work->work_id }}">{{ $work->location->location_name ?? 'N/A' }} - {{ $work->work_name }} {{ $work->location && $work->location->client ? ' - ' . $work->location->client->client_name : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technician Assignment Form -->
                <div class="block block-rounded assign-section" style="display: none;">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Asignar Técnico</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <form id="form_add_technician" method="POST">
                            <div class="form-group row">
                                <label class="col-12" for="user_id">Técnico</label>
                                <div class="col-12">
                                    <select class="js-select2 form-control" id="user_id" name="user_id" style="width: 100%;" data-placeholder="Seleccione un técnico..">
                                        <option></option>
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 mt-3 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-plus mr-1"></i> Asignar Técnico
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Assistant Assignment Form -->
                <div class="block block-rounded assign-section" style="display: none;">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Asignar Auxiliar</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <form id="form_add_assistant" method="POST">
                            <div class="form-group row">
                                <label class="col-12" for="assistant_id">Auxiliar</label>
                                <div class="col-12">
                                    <select class="js-select2 form-control" id="assistant_id" name="assistant_id" style="width: 100%;" data-placeholder="Seleccione un auxiliar..">
                                        <option></option>
                                        @foreach($assistants as $ast)
                                            <option value="{{ $ast->id }}">{{ $ast->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 mt-3 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-plus mr-1"></i> Asignar Auxiliar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- External Assignment Form -->
                <div class="block block-rounded assign-section" style="display: none;">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Asignar Externo</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <form id="form_add_external" method="POST">
                            <div class="form-group row">
                                <label class="col-12" for="external_id">Externo</label>
                                <div class="col-12">
                                    <select class="js-select2 form-control" id="external_id" name="external_id" style="width: 100%;" data-placeholder="Seleccione un externo..">
                                        <option></option>
                                        @foreach($externals as $ext)
                                            <option value="{{ $ext->id }}">{{ $ext->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 mt-3 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-plus mr-1"></i> Asignar Externo
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Assigned Users Lists -->
            <div class="col-md-7">
                <!-- Assigned Technicians -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Técnicos Asignados</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-center" style="width: 25%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="technicians_list">
                                <tr>
                                    <td colspan="2" class="text-center">Seleccione una obra para ver los técnicos asignados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Assigned Assistants -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Auxiliares Asignados</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-center" style="width: 25%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="assistants_list">
                                <tr>
                                    <td colspan="2" class="text-center">Seleccione una obra para ver los auxiliares asignados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Assigned Externals -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Externos Asignados</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-center" style="width: 25%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="externals_list">
                                <tr>
                                    <td colspan="2" class="text-center">Seleccione una obra para ver los externos asignados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection