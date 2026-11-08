@extends('layouts.backoffice')

@section('content')
<div class="content">
	<h2 class="content-heading">Editar Rol</h2>

	<div class="block">
		<div class="block-header block-header-default">
			<h3 class="block-title">Información del Registro</h3>
			<div class="block-options">
			</div>
		</div>
		<div class="block-content  pb-5 pt-2">

			<form action="{{ route('role.update', $role->id) }}" method="POST" enctype="multipart/form-data">
				<input name="_method" type="hidden" value="PATCH">
				@include('backoffice.role.form')
			</form>
		</div>
	</div>
</div>
@stop