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
	<h2 class="content-heading">Obras</h2>

	<div class="block">
		<div class="block-header block-header-default">
			<h3 class="block-title">Registros</h3>
			<div class="block-options">
				<a href="{{ route('work.create') }}" class="btn btn-sm btn-primary">
					<i class="fa fa-plus"></i> Nuevo Registro
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
						<th>Locación</th>
						<th>Obra</th>
						<th>Ciudad</th>
						<th>Cliente</th>
						<th>Supervisor</th>
						<th class="text-center">Estado Obra</th>
						<th class="text-center" style="width: 10%;">Activo</th>
						<th class="text-center" style="width: 10%;"> - </th>
					</tr>
				</thead>
				<tbody>
					@foreach ($works as $work)
						<tr>
							<td>{{ $work->location->location_name ?? 'N/A' }}</td>
							<td>{{ $work->work_name }}</td>
							<td>{{ $work->location->location_city ?? 'N/A' }}</td>
							<td>{{ $work->location && $work->location->client ? $work->location->client->client_name : 'N/A' }}</td>
							<td>{{ $work->supervisor ? strtoupper($work->supervisor->name) : '' }}</td>
							<td class="text-center">
								@if($work->work_status == 'pending')
									<span class="badge bg-warning text-dark">PENDIENTE</span>
								@elseif($work->work_status == 'in_progress')
									<span class="badge bg-info">EN PROGRESO</span>
								@elseif($work->work_status == 'completed')
									<span class="badge bg-success">FINALIZADA</span>
								@endif
							</td>
							<td class="text-center">
								@if($work->active == 1)
									<span class="badge bg-primary">ACTIVO</span>
								@else
									<span class="badge bg-danger">INACTIVO</span>
								@endif
							</td>
							<td class="text-center justify-content-center gap-1">
								<a href="{{ route('work.edit', $work->work_id) }}" type="button" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="Editar">
									<i class="fas fa-edit"></i>
								</a>
                                <form action="{{ route('work.destroy', $work->work_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro que desea eliminar este proyecto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
								<!-- <form action="{{ route('work.duplicate', $work->work_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Desea duplicar este proyecto?');">
										@csrf
										<button type="submit" class="btn btn-sm btn-info" data-toggle="tooltip" title="Duplicar">
											<i class="fas fa-copy"></i>
										</button>
									</form> -->
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@stop