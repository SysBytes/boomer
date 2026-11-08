<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>Boomer Elecomm - Área de trabajo</title>

    <meta name="description" content="Boomer Elecomm">
    <meta name="author" content="SysBytes">
    <meta name="robots" content="index, follow">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="Boomer Elecomm">
    <meta property="og:site_name" content="Boomer Elecomm">
    <meta property="og:description" content="Boomer Elecomm, Asesoramos, diseñamos y construimos sus proyectos">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://boomer-elecomm.com">
    <meta property="og:image" content="{{ asset('img/boomer-favicon.png') }}">

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('img/boomer-favicon.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('img/boomer-favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/boomer-favicon.png') }}">


    <!-- Modules -->
    @yield('css_before')
    @vite(['resources/sass/main.scss'])
    @yield('css_after')

</head>

<body>
    <div id="page-container" class="dashboard sidebar-light side-scroll main-content-boxed">

        @include('backoffice.dashboard.partials.header')

        <!-- Main Container -->
        <main id="main-container">
            <!-- Hero -->
            <div class="bg-image" style="background-image: url('{{ asset("img/img-service-big-2a.jpg") }}'); background-position: center;">
                <div class="bg-white-75">
                    <div class="content text-center">
                        <div class="pt-3 pb-3">
                            <h1 class="h2 fw-bold text-black-75 mb-2">Área de trabajo</h1>
                            <h2 class="h5 fw-medium text-muted">La fuerza del equipo reside en cada miembro</h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->


            @include('backoffice.dashboard.partials.nav')

            @yield('content')
        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        <footer id="page-footer">
            <div class="content py-3">
                <div class="row fs-sm">
                    <div class="col-sm-12 order-sm-1 py-1 text-center text-sm-center">
                        <a class="fw-semibold" href="https://boomer-elecomm.com" target="_blank">Boomer Elecomm</a> &copy; <span data-toggle="year-copy"></span>
                    </div>
                    <!-- <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
                        Desarrollado por <a class="fw-semibold" href="https://sysbytes.com.co" target="_blank">SysBytes</a>
                    </div> -->

                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- END Page Container -->


    @yield('js_before')

    <!-- Codebase Libraries -->
    <script src="{{ asset('js/codebase.app.min.js') }}"></script>

    <!-- jQuery (required for jQuery Validation plugins) -->
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>


    @yield('js_after')
</body>

</html>