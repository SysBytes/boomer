@extends('layouts.backoffice')

@section('css_before')
    <!-- Custom Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/localization/messages_es.js') }}"></script>

    <script>Codebase.helpersOnLoad(['jq-select2']);</script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/be_forms_validation.js') }}"></script>
@endsection

@section('content')
<div class="content">
    <h2 class="content-heading">Usuarios</h2>
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Editar Registro</h3>
        </div>
        <div class="block-content">
            <form action="{{ route('user.update', $user->id)}}" method="POST" class="js-validation-bootstrap">
                <div class="row justify-content-center pb-5 pt-2 push">
                    <div class="col-xl-8">
                        {{ method_field('PUT') }}
                        @include('backoffice.user.form')
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop