@extends('layouts.backoffice')

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/localization/messages_es.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/forms_validation.js') }}"></script>
@endsection

@section('content')
<div class="content">
    <h2 class="content-heading">Días Feriados</h2>
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Nuevo Registro</h3>
        </div>
        <div class="block-content">
            <div class="row justify-content-center pb-5 pt-2">
                <div class="col-xl-8">
                    <form action="{{ route('holiday.store')}}" method="POST" class="js-validation-bootstrap">
                        @include('backoffice.holiday.form')
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@stop
