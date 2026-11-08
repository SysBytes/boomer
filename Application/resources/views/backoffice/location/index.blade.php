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
	<h2 class="content-heading">Locaciones</h2>

	<div class="block">
		<div class="block-header block-header-default">
			<h3 class="block-title">Registros</h3>
			<div class="block-options">
				<a href="{{ route('location.create') }}" class="btn btn-sm btn-primary">
					<i class="fa fa-plus"></i> Nueva Locación
				</a>
			</div>
		</div>
		<div class="block-content block-content-full">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

			<table class="table table-bordered table-striped table-vcenter js-dataTable-responsive">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Cliente</th>
						<th>Ciudad</th>
						<th>Dirección</th>
						<th class="text-center" style="width: 8%;">Obras</th>
						<th class="text-center" style="width: 10%;">Estatus</th>
						<th class="text-center" style="width: 10%;"> - </th>
					</tr>
				</thead>
				<tbody>
					@foreach ($locations as $location)
						<tr>
							<td>{{ $location->location_name }}</td>
							<td>{{ $location->client->client_name ?? 'N/A' }}</td>
							<td>{{ $location->location_city }}</td>
							<td>{{ $location->location_address }}</td>
							<td class="text-center">
								<span class="badge bg-secondary">{{ $location->works_count }}</span>
							</td>
							<td class="text-center">
								@if($location->active == 1)
									<span class="badge bg-primary">ACTIVO</span>
								@else
									<span class="badge bg-danger">INACTIVO</span>
								@endif
							</td>
							<td class="text-center">
								<a href="{{ route('location.edit', $location->location_id) }}" type="button" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="Editar">
									<i class="fas fa-edit"></i>
								</a>
								<form action="{{ route('location.destroy', $location->location_id) }}" method="POST" class="d-inline-block">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar esta locación?');">
										<i class="fas fa-trash"></i>
									</button>
								</form>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@stop