@extends('layouts.master')

@section('content')
    <!--===== HERO AREA STARTS =======-->
    <div class="hero1-section-area about-bg-area">
        <img src="{{ asset('img/img-bg-internal.jpg') }}" alt="" class="header-img1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto">
                    <div class="hero-heading-area heading1 text-center">
                        <h1>Certificaciones y diagnósticos</h1>
                        <a href="{{ route('home') }}" class="backline">Inicio <i class="fa-solid fa-angle-right"></i> <span>Servicios</span> <i class="fa-solid fa-angle-right"></i> <span>Certificaciones y diagnósticos</span></a>
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
                            <img src="{{ asset('img/img-service-big-4a.jpg') }}" alt="Certificaciones y diagnósticos" title="Certificaciones y diagnósticos">
                        </div>
                        <div class="space32"></div>
                        <h3>Certificaciones y diagnósticos</h3>
                        <ul>
                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Nacional (NTC)
                            </li>

                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Internacional (NFPA, IEEE, IEC, NEMA, API)
                            </li>

                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Certificaciones de redes (cableado estructurado / fibra óptica)
                            </li>

                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                Resolución 1348
                            </li>

                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                RETIE
                            </li>

                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                RETILAP
                            </li>

                            <li class="fw-normal lh-sm">
                                <img src="{{ asset('img/icons/check4.svg') }}" alt="">
                                LEED
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