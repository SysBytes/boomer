@extends('layouts.backoffice')

@section('css_before')
	<!-- Page JS Plugins CSS -->
	<link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
	<link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
	<link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
@endsection

@section('js_after')
	<!-- Page JS Plugins -->
	<script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
	<script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>

	<!-- Page JS Code -->
	<script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection

@section('content')
<div class="content">
	<h2 class="content-heading">Categorías de materiales</h2>

	<div class="block">
		<div class="block-header block-header-default">
			<h3 class="block-title">Registros</h3>
			<div class="block-options">
				<a href="{{ route('material_category.create') }}" class="btn btn-sm btn-primary">
					<i class="fa fa-plus"></i> Nuevo Registro
				</a>
			</div>
		</div>
		<div class="block-content block-content-full">

			<table class="table table-bordered table-striped table-vcenter js-dataTable-responsive">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Descripción</th>
						<th class="text-center" style="width: 15%;">Estatus</th>
						<th class="text-center" style="width: 15%;"> - </th>
					</tr>
				</thead>
				<tbody>
					@foreach ($categories as $category)
					<tr>
						<td>{{ $category->material_category_name }}</td>
						<td>{{ $category->material_category_description }}</td>
						<td class="text-center">
							@IF($category->active == 1)
							<span class="badge bg-primary">ACTIVO</span>
							@ELSE
							<span class="badge bg-danger">INACTIVO</span>
							@ENDIF
						</td>
						<td class="text-center">
							<a href="{{ route('material_category.edit', $category->material_category_id) }}" type="button" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="Editar">
								<i class="fas fa-edit"></i>
							</a>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@stop