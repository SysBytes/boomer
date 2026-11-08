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
	<h2 class="content-heading">Materiales Consumidos por Proyecto</h2>

	<!-- Filter Block -->
	<div class="block block-rounded no-print">
		<div class="block-header block-header-default">
			<h3 class="block-title">Seleccionar Proyecto</h3>
		</div>
		<div class="block-content pb-5">
			<form action="{{ route('reports.materials_by_work') }}" method="GET">
				<div class="row align-items-end">
					<div class="col-md-8 mb-3 mb-md-0">
						<label class="form-label" for="work_id">Obra</label>
						<select class="js-select2 form-select" id="work_id" name="work_id" style="width: 100%;" data-placeholder="Todos">
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
				<small class="text-muted mt-2 d-block">Solo se listan proyectos con al menos una requisición en estado <strong>Entregada</strong>.</small>
			</form>
		</div>
	</div>

	@if($selectedWork)
		<!-- Results Block -->
		<div class="block block-rounded">
			<div class="block-header block-header-default">
				<h3 class="block-title">
					<i class="fa-solid fa-diagram-project me-1 text-primary"></i>
					{{ $selectedWork->work_name }}
					@if($selectedWork->client)
						<span class="text-muted fw-normal fs-sm ms-1">— {{ $selectedWork->location->client->client_name ?? 'Sin Cliente' }}</span>
					@endif
				</h3>
				<div class="block-options">
					@if($materials->isNotEmpty())
						<span class="badge bg-primary">{{ $materials->count() }} materiales</span>
						<span class="badge bg-success ms-1">{{ number_format($materials->sum('total_quantity')) }} unidades totales</span>
						<button class="btn btn-sm btn-alt-secondary ms-2 no-print" onclick="window.print()">
							<i class="fa fa-print me-1"></i> Imprimir
						</button>
					@endif
				</div>
			</div>
			<div class="block-content block-content-full">

				<!-- Cabecera visible solo al imprimir -->
				<div class="print-header">
					<h4 style="margin:0 0 4px;">Materiales Consumidos por Proyecto</h4>
					<p style="margin:0; font-size:14px;">
						<strong>Obra:</strong> {{ $selectedWork->work_name }}
						@if($selectedWork->client)
							&nbsp;|&nbsp; <strong>Cliente:</strong> {{ $selectedWork->location->client->client_name ?? 'Sin Cliente' }}
						@endif
						&nbsp;|&nbsp; <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
						&nbsp;|&nbsp; <strong>Total materiales:</strong> {{ $materials->count() }}
						&nbsp;|&nbsp; <strong>Total unidades:</strong> {{ number_format($materials->sum('total_quantity')) }}
					</p>
				</div>

				@if($materials->isEmpty())
					<p class="text-muted text-center py-5 mb-0">
						<i class="fa fa-inbox fa-2x mb-2"></i><br>
						No se encontraron materiales consumidos para este proyecto.
					</p>
				@else
					<table class="table table-bordered table-striped table-vcenter js-dataTable-full-export">
						<thead>
							<tr>
								<th>Categoría</th>
								<th>Material</th>
								<th>Unidad</th>
								<th class="text-center" style="width: 15%;">Cantidad Total</th>
							</tr>
						</thead>
						<tbody>
							@foreach($materials as $row)
								<tr>
									<td>{{ $row->material_category_name }}</td>
									<td>{{ $row->material_name }}</td>
									<td>{{ $row->material_unit ?? '—' }}</td>
									<td class="text-center fw-bold text-primary fs-5">{{ number_format($row->total_quantity) }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				@endif

			</div>
		</div>
	@endif
</div>
@stop