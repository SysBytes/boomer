@extends('layouts.backoffice')

@section('content')
<div class="content">
    <h2 class="content-heading">Inducción - Subcategorías</h2>

    @include('backoffice.induction._alerts')

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Editar Registro</h3>
        </div>
        <div class="block-content">
            <div class="row justify-content-center pb-5 pt-2">
                <div class="col-xl-8">
                    <form action="{{ route('induction.subcategories.update', $subcategory->id) }}" method="POST">
                        {{ method_field('PUT') }}
                        @include('backoffice.induction.subcategory.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

