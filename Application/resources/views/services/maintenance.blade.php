@extends('layouts.master')

@section('content')
    <!--===== HERO AREA STARTS =======-->
    <div class="hero1-section-area about-bg-area">
        <img src="{{ asset('img/img-bg-internal.jpg') }}" alt="" class="header-img1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto">
                    <div class="hero-heading-area heading1 text-center">
                        <h1>Puesta en servicio y mantenimiento</h1>
                        <a href="{{ route('home') }}" class="backline">Inicio <i class="fa-solid fa-angle-right"></i> <span>Servicios</span> <i class="fa-solid fa-angle-right"></i> <span>Puesta en servicio y mantenimiento</span></a>
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
                            <img src="{{ asset('img/img-service-big-3a.jpg') }}" alt="Construcción" title="Construcción">
                        </div>
                        <div class="space32"></div>
                        <h3>Puesta en servicio y mantenimiento</h3>
                        <p>Realizamos las pruebas y puesta en servicio de sus proyectos con un recurso humano calificado.</p>
                        <ul>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Instalaciones eléctricas en media y baja tensión (tableros, cableado, conexionado).
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
                                Subestaciones eléctricas (transformadores, cableado, conexionado).
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Generación eléctrica: grupo electrógeno.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Centros de datos.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Cableado estructurado.
                            </li>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Seguridad electrónica.
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