@extends('layouts.master')

@section('content')
    <!--===== HERO AREA STARTS =======-->
    <div class="hero1-section-area about-bg-area">
        <img src="{{ asset('img/img-bg-internal.jpg') }}" alt="" class="header-img1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto">
                    <div class="hero-heading-area heading1 text-center">
                        <h1>Ingeniería</h1>
                        <a href="{{ route('home') }}" class="backline">Inicio <i class="fa-solid fa-angle-right"></i> <span>Servicios</span> <i class="fa-solid fa-angle-right"></i> <span>Ingeniería</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== HERO AREA ENDS =======-->

    <!--===== SERVICE AREA STARTS =======-->
    <div class="service-leftside-area sp1">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="service-rightside-area heading2 rightsidebar">
                        <div class="img1">
                            <img src="{{ asset('img/img-service-big-1.jpg') }}" alt="Ingeniería" title="Ingeniería">
                        </div>
                        <div class="space32"></div>
                        <h3>Ingeniería</h3>
                        <p>Desarrollamos ingeniería conceptual, básica y de detalle acorde a sus necesidades. Contamos con altos estándares normativos y de calidad.</p>
                        <p>Nuestra experiencia específica está desarrollada en:</p>
                        <ul>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Diseños eléctricos en media y baja tensión: Distribución de energía, puesta a tierra, iluminación y tomas, protección contra descargas atmosféricas, clasificación de áreas, especificaciones técnicas de equipos, diagramas
                                de control, filosofías de operación, entre otros.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Diseño de subestaciones eléctricas. Inspecciones y diagnósticos de infraestructura existente.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Diseño de sistemas fotovoltaicos.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Ingeniería de ofertas para proyectos de infraestructura.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Diseño de comunicaciones para infraestructura industrial: sistemas de control (PLC), centro de datos, sistema de cableado estructurado, sistemas de seguridad electrónica (control de acceso, cctv, intrusión) sistema de
                                detección de incendios, entre
                                otros.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Arquitecturas de control y comunicaciones.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Elaboración de estudios especializados: estudios de conexión, generación y demanda, flujo de carga, coordinación de protecciones, corto circuito, cargabilidad de sistemas eléctricos, arranque de motores,
                                calidad de energía.
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="service-leftside">
                        @include('services.services-nav')

                        <div class="download-broucher d-none">
                            <h3>Descargar Brochure</h3>
                            <p>Conoce en detalle el servicio que tenemos a disposición para tu empresa.</p>
                            <div class="btn-area">
                                <a href="#" class="header-btn1"><img src="{{ asset('img/icons/download1.svg') }}" alt="">Pdf Download</a>
                                <a href="#" class="header-btn2"><img src="{{ asset('img/icons/download1.svg') }}" alt="">Doc Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== SERVICE AREA ENDS =======-->

    @include('services.services-message')

@endsection