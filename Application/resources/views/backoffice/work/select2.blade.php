@extends('layouts.backoffice')

@section('css_before')
    <!-- Custom Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Custom Select2 JS -->
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>Codebase.helpersOnLoad(['jq-select2']);</script>
@endsection

@section('content')

    <!-- Page Content -->
    <div class="content">

        <!-- Select2 (.js-select2 class is initialized in Helpers.jqSelect2()) -->
        <!-- For more info and examples you can check out https://github.com/select2/select2 -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Select2</h3>
            </div>
            <div class="block-content block-content-full">
                <form action="be_forms_plugins.html" method="POST" onsubmit="return false;">
                    <h2 class="content-heading pt-0">Normal Select Box</h2>
                    <div class="row">
                        <div class="col-lg-4">
                            <p class="text-muted">
                                Default select input turns into a searchable and dynamic list
                            </p>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="mb-4">
                                <select class="js-select2 form-select" id="example-select2" name="example-select2" style="width: 100%;" data-placeholder="Choose one..">
                                    <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                    <option value="1">HTML</option>
                                    <option value="2">CSS</option>
                                    <option value="3">JavaScript</option>
                                    <option value="4">PHP</option>
                                    <option value="5">MySQL</option>
                                    <option value="6">Ruby</option>
                                    <option value="7">Angular</option>
                                    <option value="8">React</option>
                                    <option value="9">Vue.js</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Select2 -->
    </div>
    <!-- END Page Content -->
@endsection