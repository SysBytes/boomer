@extends('layouts.backoffice')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>Codebase.helpersOnLoad(['jq-select2']);</script>
@endsection

@section('content')
<div class="content">
    <h2 class="content-heading">Inducción - Contenidos</h2>

    @include('backoffice.induction._alerts')

    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Nuevo Registro</h3>
        </div>
        <div class="block-content">
            <div class="row justify-content-center pb-5 pt-2">
                <div class="col-xl-8">
                    <form action="{{ route('induction.contents.store') }}" method="POST" enctype="multipart/form-data">
                        @include('backoffice.induction.content.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
