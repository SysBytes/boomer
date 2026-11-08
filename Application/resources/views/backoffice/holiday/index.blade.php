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
	<h2 class="content-heading">Días Feriados</h2>

	<div class="block">
		<div class="block-header block-header-default">
			<h3 class="block-title">Registros</h3>
			<div class="block-options">
				<a href="{{ route('holiday.create') }}" class="btn btn-sm btn-primary">
					<i class="fa fa-plus"></i> Nuevo Registro
				</a>
			</div>
		</div>
		<div class="block-content block-content-full">

			<table class="table table-bordered table-striped table-vcenter js-dataTable-responsive" data-order='[[ 2, "asc" ]]'>
				<thead>
					<tr>
						<th>Nombre del Feriado</th>
						<th>País</th>
						<th>Fecha</th>
						<th class="text-center" style="width: 15%;">Anual</th>
						<th class="text-center" style="width: 15%;">Estatus</th>
						<th class="text-center" style="width: 10%;"> - </th>
					</tr>
				</thead>
				<tbody>
					@foreach ($holidays as $holiday)
					<tr>
						<td>{{ $holiday->holiday_name }}</td>
						<td>{{ $countries->firstWhere('id', $holiday->country_id)->name ?? 'N/A' }}</td>
						<td data-order="{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('Ymd') }}">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d/m/Y') }}</td>
						<td class="text-center">
							@IF($holiday->is_annual == 1)
							<span class="badge bg-info">SÍ</span>
							@ELSE
							<span class="badge bg-secondary">NO</span>
							@ENDIF
						</td>
						<td class="text-center">
							@IF($holiday->active == 1)
							<span class="badge bg-primary">ACTIVO</span>
							@ELSE
							<span class="badge bg-danger">INACTIVO</span>
							@ENDIF
						</td>
						<td class="text-center">
							<a href="{{ route('holiday.edit', $holiday->holiday_id) }}" type="button" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="Editar">
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
