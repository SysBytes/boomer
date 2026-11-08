@extends('layouts.backoffice')

@section('css_before')
	<!-- Page JS Plugins CSS -->
	<link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
	<link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
	<link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
	<style>
		.print-header {
			display: none;
		}

		@media print {

			#sidebar,
			#page-header,
			.content-heading,
			.block-filter,
			.no-print,
			.dataTables_length,
			.dataTables_filter,
			.dataTables_paginate,
			.dataTables_info,
			.dt-paging,
			.dt-info,
			.dt-container .dt-layout-start,
			.dt-container .dt-layout-end {
				display: none !important;
			}

			.print-header {
				display: block !important;
				margin-bottom: 20px;
				border-bottom: 2px solid #ddd;
				padding-bottom: 12px;
			}

			.block-options .btn {
				display: none !important;
			}

			@page {
				margin: 1.5cm;
			}
		}
	</style>
@endsection

@section('js_after')
	<!-- Page JS Plugins -->
	<script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons-pdfmake/pdfmake.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons-pdfmake/vfs_fonts.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons/buttons.print.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script>

	<!-- Page JS Code -->
	<script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection

@section('content')
<div class="content">
	<h2 class="content-heading">Usuarios Asignados por Proyecto</h2>

	<!-- Filter Block -->
	<div class="block block-rounded no-print">
		<div class="block-header block-header-default">
			<h3 class="block-title">Seleccionar Proyecto</h3>
		</div>
		<div class="block-content pb-5">
			<form action="{{ route('reports.work_users') }}" method="GET">
				<div class="row align-items-end">
					<div class="col-md-8 mb-3 mb-md-0">
						<label class="form-label" for="work_id">Obra</label>
						<select class="js-select2 form-select" id="work_id" name="work_id" style="width: 100%;" data-placeholder="Todos" onchange="this.form.submit()">
							<option></option>
							@foreach($works as $work)
								<option value="{{ $work->work_id }}" {{ request('work_id') == $work->work_id ? 'selected' : '' }}>
									{{ $work->location->location_name ?? 'N/A' }} - {{ $work->work_name }}
									@if($work->client)
										({{ $work->location->client->client_name ?? 'Sin Cliente' }})
									@endif
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4 mb-3 mb-md-0">
						<label class="form-label invisible">Acción</label>
						<button type="submit" class="btn btn-primary w-100">
							<i class="fa fa-search me-1"></i> Consultar
						</button>
					</div>
				</div>
				<small class="text-muted mt-2 d-block">Solo se listan proyectos que tienen usuarios técnicos, auxiliares o externos asignados.</small>
			</form>
		</div>
	</div>

	@if(empty($reportData))
		<p class="text-muted text-center py-5 mb-0">
			<i class="fa fa-inbox fa-2x mb-2"></i><br>
			No se encontraron usuarios asignados para la consulta actual.
		</p>
	@else
		@foreach($reportData as $data)
			<!-- Results Block -->
			<div class="block block-rounded">
				<div class="block-header block-header-default">
					<h3 class="block-title">
						<i class="fa-solid fa-folder-open me-1 text-primary"></i>
						{{ $data->work->location->location_name ?? 'N/A' }} - {{ $data->work->work_name }}
						@if($data->work->location && $data->work->location->client)
							<span class="text-muted fw-normal fs-sm ms-1">— {{ $data->work->location->client->client_name }}</span>
						@endif
					</h3>
					<div class="block-options">
						<span class="badge bg-primary">{{ $data->assignments->count() }} asignaciones</span>
						<button class="btn btn-sm btn-primary ms-2 no-print" onclick="window.print()">
							<i class="fa fa-print me-1"></i> Imprimir
						</button>
					</div>
				</div>
				<div class="block-content block-content-full">

					<!-- Cabecera visible solo al imprimir -->
					<div class="print-header">
						<h4 style="margin:0 0 4px;">Usuarios Asignados a Proyecto</h4>
						<p style="margin:0; font-size:14px;">
							<strong>Obra:</strong> {{ $data->work->location->location_name ?? 'N/A' }} - {{ $data->work->work_name }}
							@if($data->work->location && $data->work->location->client)
								&nbsp;|&nbsp; <strong>Cliente:</strong> {{ $data->work->location->client->client_name }}
							@endif
							&nbsp;|&nbsp; <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
							&nbsp;|&nbsp; <strong>Total usuarios:</strong> {{ $data->assignments->count() }}
						</p>
					</div>

					<table class="table table-bordered table-striped table-vcenter">
						<thead>
							<tr class="bg-body-dark">
								<th style="width: 40%;">Usuario</th>
								<th style="width: 10%; text-align: center;">Rol</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data->assignments as $assignment)
								<tr>
									<td>
										{{ $assignment->user_name }}<br>
										<span class="text-muted fs-sm">{{ $assignment->user_email }}</span>
									</td>
									<td class="text-center" data-sort="{{ $assignment->role_order }}">
										@if($assignment->user_role === 'Técnico')
											<span class="badge bg-primary">Técnico</span>
										@elseif($assignment->user_role === 'Auxiliar')
											<span class="badge bg-info">Auxiliar</span>
										@elseif($assignment->user_role === 'Externo')
											<span class="badge bg-warning">Externo</span>
										@else
											<span class="badge bg-secondary">{{ $assignment->user_role }}</span>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>

				</div>
			</div>
		@endforeach
	@endif
</div>
@stop