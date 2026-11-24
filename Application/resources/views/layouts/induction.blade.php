<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <title>Boomer Elecomm - Inducción Corporativa</title>

    <meta name="description" content="Inducción Corporativa Boomer Elecomm">
    <meta name="author" content="SysBytes">
    <meta name="robots" content="noindex, nofollow">

    <link rel="shortcut icon" href="{{ asset('img/boomer-favicon.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('img/boomer-favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/boomer-favicon.png') }}">

    @yield('css_before')
    @vite(['resources/sass/main.scss'])
    @yield('css_after')
</head>

<body>
    <div id="page-container" class="dashboard sidebar-light side-scroll main-content-boxed">

        @include('backoffice.dashboard.partials.header')

        <main id="main-container">
            @yield('content')
        </main>

        <footer id="page-footer">
            <div class="content py-3">
                <div class="row fs-sm">
                    <div class="col-12 py-1 text-center text-muted">
                        <strong>Boomer Elecomm</strong> &copy; {{ date('Y') }} · Programa de Inducción Corporativa
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @yield('js_before')
    <script src="{{ asset('js/codebase.app.min.js') }}"></script>
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
    @yield('js_after')
</body>

</html>
