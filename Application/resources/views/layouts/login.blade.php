<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>Boomer Elecomm</title>

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
    @yield('css')

    @vite(['resources/sass/main.scss', 'resources/js/app.js'])

    @yield('js')
</head>

<body class="homepage1-body tg-heading-subheading animation-style3">

    <div id="page-container" class="main-content-boxed">
        <!-- Main Container -->
        <main id="main-container">
            @yield('content')
        </main>
    </div>



    <!-- Codebase Libraries -->
    <script src="{{ asset('js/codebase.app.min.js') }}"></script>

    <!-- jQuery (required for jQuery Validation plugins) -->
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>

    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/op_auth_signin.js') }}"></script>

    @stack('scripts')

    <!-- Limpiar localStorage para evitar bugs visuales (pantalla oscura) luego de expiración de sesión -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            localStorage.clear();
            sessionStorage.clear();
        });
    </script>
</body>

</html>