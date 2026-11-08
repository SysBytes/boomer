@extends('layouts.backoffice')

@section('css_before')
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
				margin-bottom: 16px;
				border-bottom: 2px solid #ddd;
				padding-bottom: 10px;
			}

			.block-options {
				display: none !important;
			}

			@page {
				margin: 1.5cm;
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
	<script src="{{ asset('js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons-pdfmake/pdfmake.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons-pdfmake/vfs_fonts.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons/buttons.print.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script>
	<script>
		Codebase.onLoad(() => {
			jQuery('.js-dataTable-inventory').DataTable({
				pagingType: "simple_numbers",
				layout: {
					topStart: [
						{
							buttons: [
								{
									extend: 'excelHtml5',
									text: '<i class="fa fa-file-excel me-1"></i> Excel',
									className: 'btn btn-sm btn-success',
									exportOptions: {
										columns: [0, 1, 2, 3, 4, 5]
									}
								}
							]
						},
						{
							pageLength: {
								menu: [15, 30, 60, 100]
							}
						}
					]
				},
				pageLength: 15,
				autoWidth: false,
				responsive: true,
				initComplete: function () {
					jQuery('.buttons-excel').removeClass('btn-secondary');
				}
			});
		});
	</script>
@endsection

@section('content')
<div class="content">
	<h2 class="content-heading">Inventario Actual de Materiales</h2>

	<!-- Filter Block -->
	<div class="block block-rounded no-print">
		<div class="block-header block-header-default">
			<h3 class="block-title">Filtrar Inventario</h3>
		</div>
		<div class="block-content pb-4">
			<form action="{{ route('reports.inventory') }}" method="GET">
				<div class="row align-items-end">
					<div class="col-md-6 mb-3 mb-md-0">
						<label class="form-label" for="filter">Mostrar</label>
						<select class="form-select" id="filter" name="filter" onchange="this.form.submit()">
							<option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Todos los materiales</option>
							<option value="with_stock" {{ $filter === 'with_stock' ? 'selected' : '' }}>Con stock disponible</option>
							<option value="no_stock" {{ $filter === 'no_stock' ? 'selected' : '' }}>Sin stock (agotados)</option>
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label invisible">Acción</label>
						<button type="submit" class="btn btn-primary w-100">
							<i class="fa fa-filter me-1"></i> Aplicar
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- Results Block -->
	<div class="block block-rounded">
		<div class="block-header block-header-default">
			<h3 class="block-title">
				<i class="fa-solid fa-boxes-stacked me-1 text-primary"></i>
				@if($filter === 'with_stock') Materiales Con Stock
				@elseif($filter === 'no_stock') Materiales Sin Stock
				@else Todos los Materiales
				@endif
			</h3>
			<div class="block-options">
				@php $total = $materials->flatten()->count(); @endphp
				<span class="badge bg-primary">{{ $total }} materiales</span>
				<button class="btn btn-sm btn-primary ms-2 no-print" onclick="window.print()">
					<i class="fa fa-print me-1"></i> Imprimir
				</button>
			</div>
		</div>
		<div class="block-content block-content-full">

			<!-- Print header -->
			<div class="print-header">
				<h4 style="margin:0 0 4px;">Inventario Actual de Materiales</h4>
				<p style="margin:0; font-size:13px;">
					<strong>Filtro:</strong>
					@if($filter === 'with_stock') Con stock disponible
					@elseif($filter === 'no_stock') Sin stock (agotados)
					@else Todos los materiales
					@endif
					&nbsp;|&nbsp; <strong>Total:</strong> {{ $total }} materiales
					&nbsp;|&nbsp; <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
				</p>
			</div>

			@if($materials->isEmpty())
				<p class="text-muted text-center py-5 mb-0">
					<i class="fa fa-inbox fa-2x mb-2"></i><br>
					No se encontraron materiales con el filtro seleccionado.
				</p>
			@else
				<table class="table table-bordered table-striped table-vcenter js-dataTable-inventory mb-4">
					<thead>
						<tr class="bg-body-dark">
							<th style="width: 50px;">#</th>
							<th>Categoría</th>
							<th>Material</th>
							<th>Unidad</th>
							<th class="text-center" style="width: 12%;">Stock</th>
							<th class="text-center no-print" style="width: 10%;">Estado</th>
						</tr>
					</thead>
					<tbody>
						@php $counter = 1; @endphp
						@foreach($materials as $categoryName => $items)
							@foreach($items as $mat)
								<tr>
									<td>{{ $counter++ }}</td>
									<td class="fw-semibold">
										<i class="fa fa-tag me-1 text-muted"></i> {{ $categoryName }}
									</td>
									<td>{{ $mat->material_name }}</td>
									<td>{{ $mat->material_unit ?? '—' }}</td>
									<td class="text-center fw-bold fs-5 {{ $mat->stock > 0 ? 'text-success' : 'text-danger' }}">
										{{ $mat->stock }}
									</td>
									<td class="text-center no-print">
										@if($mat->stock > 0)
											<span class="badge bg-success">Disponible</span>
										@else
											<span class="badge bg-danger">Agotado</span>
										@endif
									</td>
								</tr>
							@endforeach
						@endforeach
					</tbody>
				</table>
			@endif

		</div>
	</div>
</div>
@stop