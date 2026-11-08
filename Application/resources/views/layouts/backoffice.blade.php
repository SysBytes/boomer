<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>Boomer Elecomm - Backoffice</title>

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
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern">


        @include('backoffice.partials.sidebar')

        @include('backoffice.partials.header')

        <!-- Main Container -->
        <main id="main-container">
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

    <!-- Fix: Evitar que la pantalla se quede oscurecida si hay un estado guardado corrupto -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var pageContainer = document.getElementById('page-container');
            if (pageContainer && pageContainer.classList.contains('page-overlay-show')) {
                // En escritorio o carga inicial no debería estar el overlay a menos que sea forzado, lo removemos por seguridad
                if (window.innerWidth > 991) {
                    pageContainer.classList.remove('page-overlay-show');
                }
            }

            // Alternativa: escuchar el click del propio overlay para cerrarlo forzosamente
            var overlay = document.getElementById('page-overlay');
            if (overlay) {
                overlay.addEventListener('click', function () {
                    pageContainer.classList.remove('page-overlay-show');
                });
            }
        });
    </script>
</body>

</html>