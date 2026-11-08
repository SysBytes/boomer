@extends('layouts.master')

@section('content')
    <!--===== HERO AREA STARTS =======-->
    <div class="hero1-section-area about-bg-area">
        <img src="{{ asset('img/img-bg-internal.jpg') }}" alt="" class="header-img1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto">
                    <div class="hero-heading-area heading1 text-center">
                        <h1>Construcción</h1>
                        <a href="{{ route('home') }}" class="backline">Inicio <i class="fa-solid fa-angle-right"></i> <span>Servicios</span> <i class="fa-solid fa-angle-right"></i> <span>Construcción</span></a>
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
                            <img src="{{ asset('img/img-service-big-2a.jpg') }}" alt="Construcción" title="Construcción">
                        </div>
                        <div class="space32"></div>
                        <h3>Instalaciones eléctricas</h3>
                        <ul>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Instalaciones eléctricas en media y baja tensión (equipos, cableado, conexión).
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Sistema de iluminación interior, exterior y tomacorrientes.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Sistema de protección contra descargas atmosféricas.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Subestaciones eléctricas (equipos, cableado, conexión).
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Generación eléctrica.
                            </li>
                        </ul>

                        <div class="space24"></div>

                        <h3>Instalaciones de comunicaciones</h3>
                        <ul>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Centros de datos
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Conectividad de voz y datos.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Cableado estructurado
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Seguridad electrónica:
                                <ul class="ps-4">
                                    <li class="fw-normal lh-sm">
                                        <img src="{{ asset('img/icons/check5.svg') }}" alt="">
                                        Circuito cerrado de televisión (CCTV)
                                    </li>
                                    <li class="fw-normal lh-sm">
                                        <img src="{{ asset('img/icons/check5.svg') }}" alt="">
                                        Control de acceso
                                    </li>
                                    <li class="fw-normal lh-sm">
                                        <img src="{{ asset('img/icons/check5.svg') }}" alt="">
                                        Sistemas de detección de incendios.
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="service-leftside">
                        @include('services.services-nav')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== SERVICE AREA ENDS =======-->

    @include('services.services-message')

@endsection