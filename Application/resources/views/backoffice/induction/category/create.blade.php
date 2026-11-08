@extends('layouts.backoffice')

@section('content')
<div class="content">
    <h2 class="content-heading">Inducción - Categorías</h2>

    @include('backoffice.induction._alerts')

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Nuevo Registro</h3>
        </div>
        <div class="block-content">
            <div class="row justify-content-center pb-5 pt-2">
                <div class="col-xl-8">
                    <form action="{{ route('induction.categories.store') }}" method="POST">
                        @include('backoffice.induction.category.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

