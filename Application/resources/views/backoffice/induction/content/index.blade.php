@extends('layouts.backoffice')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection

@section('content')
<div class="content">
    <h2 class="content-heading">Inducción - Contenidos</h2>

    @include('backoffice.induction._alerts')

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Registros</h3>
            <div class="block-options">
                <a href="{{ route('induction.contents.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Nuevo Registro
                </a>
            </div>
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-responsive">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoría / Subcategoría</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Origen</th>
                        <th>Destinatario</th>
                        <th class="text-center">Tiempo (seg)</th>
                        <th class="text-center">Obligatorio</th>
                        <th class="text-center">Estatus</th>
                        <th class="text-center" style="width: 14%;"> - </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contents as $content)
                        <tr>
                            <td>{{ $content->title }}</td>
                            <td>{{ $content->subcategory->category->name ?? 'N/A' }} / {{ $content->subcategory->name ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($content->type === 'pdf')
                                    <span class="badge bg-danger"><i class="fa fa-file-pdf"></i> PDF</span>
                                @else
                                    <span class="badge bg-info"><i class="fa fa-video"></i> VIDEO</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($content->is_external)
                                    <a href="{{ $content->url }}" target="_blank" rel="noopener" class="badge bg-warning text-dark"><i class="fa fa-link"></i> ENLACE</a>
                                @else
                                    <a href="{{ route('dashboard.corporate-contents.view', $content->id) }}" target="_blank" class="badge bg-secondary"><i class="fa fa-file"></i> ARCHIVO</a>
                                @endif
                            </td>
                            <td>
                                @if($content->target_user_id && $content->targetUser)
                                    <span class="badge bg-info">
                                        <i class="fa fa-user me-1"></i>{{ ucwords(mb_strtolower($content->targetUser->name)) }}
                                    </span>
                                @else
                                    @php
                                        $visibleRoles = Auth::user()->hasRole('developer')
                                            ? $content->roles
                                            : $content->roles->where('slug', '!=', 'developer');
                                    @endphp
                                    @forelse($visibleRoles as $role)
                                        <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">Todos los roles</span>
                                    @endforelse
                                @endif
                            </td>
                            <td class="text-center">{{ $content->required_time_seconds }}</td>
                            <td class="text-center">
                                @if($content->is_mandatory)
                                    <span class="badge bg-primary">SÍ</span>
                                @else
                                    <span class="badge bg-secondary">NO</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($content->is_active)
                                    <span class="badge bg-primary">ACTIVO</span>
                                @else
                                    <span class="badge bg-danger">INACTIVO</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('induction.contents.edit', $content->id) }}" class="btn btn-sm btn-secondary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('induction.contents.destroy', $content->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este contenido? Si ya tiene progreso de usuarios, será inactivado en su lugar.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
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