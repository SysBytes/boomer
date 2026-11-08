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


    <link rel="stylesheet" href="{{ asset('css/main/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/owlcarousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/slick-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/nice-select.css') }}">

    <!--=====  JS SCRIPT LINK =======-->
    <script src="{{ asset('js/main/plugins/jquery-3-6-0.min.js') }}"></script>

    @vite(['resources/sass/main/frontend.scss'])

    @yield('js')
</head>

<body class="homepage1-body tg-heading-subheading animation-style3">

    <!--===== PRELOADER STARTS =======-->
    <div class="preloader">
        <div class="loading-container">
            <div class="loading"></div>
            <div id="loading-icon"><img src="{{ asset('img/boomer-favicon.png') }}" alt=""></div>
        </div>
    </div>
    <!--===== PRELOADER ENDS =======-->

    <!--===== PROGRESS STARTS=======-->
    <div class="paginacontainer">
        <div class="progress-wrap">
            <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
            </svg>
        </div>
    </div>
    <!--===== PROGRESS ENDS=======-->

    @include('partials.header')

    @yield('content')

    @include('partials.footer')

    <!--===== JS SCRIPT LINK =======-->
    <script src="{{  asset('js/main/plugins/bootstrap.min.js') }}"></script>
    <script src="{{  asset('js/main/plugins/fontawesome.js') }}"></script>
    <script src="{{  asset('js/main/plugins/aos.js') }}"></script>
    <script src="{{  asset('js/main/plugins/counter.js') }}"></script>
    <script src="{{  asset('js/main/plugins/gsap.min.js') }}"></script>
    <script src="{{  asset('js/main/plugins/ScrollTrigger.min.js') }}"></script>
    <script src="{{  asset('js/main/plugins/Splitetext.js') }}"></script>
    <script src="{{  asset('js/main/plugins/sidebar.js') }}"></script>
    <script src="{{  asset('js/main/plugins/magnific-popup.js') }}"></script>
    <script src="{{  asset('js/main/plugins/mobilemenu.js') }}"></script>
    <script src="{{  asset('js/main/plugins/owlcarousel.min.js') }}"></script>
    <script src="{{  asset('js/main/plugins/gsap-animation.js') }}"></script>
    <script src="{{  asset('js/main/plugins/nice-select.js') }}"></script>
    <script src="{{  asset('js/main/plugins/slick-slider.js') }}"></script>
    <script src="{{  asset('js/main/plugins/waypoints.js') }}"></script>
    <script src="{{  asset('js/main/script.js') }}"></script>
    @stack('scripts')

</body>

</html>