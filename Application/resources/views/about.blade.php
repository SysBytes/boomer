@extends('layouts.master')

@section('content')
    <!--===== HERO AREA STARTS =======-->
    <div class="hero1-section-area about-bg-area">
        <img src="{{ asset('img/img-bg-internal.jpg') }}" alt="" class="header-img1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto">
                    <div class="hero-heading-area heading1 text-center">
                        <h1>Acerca de nosotros</h1>
                        <a href="{{ route('home') }}" class="backline">Inicio <i class="fa-solid fa-angle-right"></i> <span>Nosotros</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== HERO AREA ENDS =======-->

    <!--===== ABOUT AREA STARTS =======-->
    <div class="about3-section-area aboutpage-inner sp1">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="about3-header-area heading2">
                        <h5>¿Quienes somos?</h5>
                        <h2 class="tg-element-title">En Boomer Elecomm</h2>
                        <p>Tenemos como objetivo principal, el desarrollo e implementación constante de nuevas soluciones para adaptarse a las cambiantes necesidades del mercado.
                            El cumplimiento de este objetivo constituye nuestra razón de ser.</p>
                        <div class="misson-text">
                            <p>Nuestra misión</p>
                            <ul>
                                <li class="fw-normal lh-base"><img src="{{ asset('img/icons/check4.svg') }}" alt="">Satisfacer las necesidades del mercado en el sector eléctrico y de comunicaciones.</li>
                                <li class="fw-normal lh-base"><img src="{{ asset('img/icons/check4.svg') }}" alt="">Abarcar el diseño, la construcción y la puesta en marcha de las instalaciones desarrolladas.</li>
                                <li class="fw-normal lh-base"><img src="{{ asset('img/icons/check4.svg') }}" alt="">Implementar soluciones cumpliendo altos estándares de calidad.</li>
                            </ul>
                        </div>
                        <div class="btn-area">
                            <a href="{{ route('services.engineering') }}" class="header-btn1">Conoce nuestros servicios <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-6">
                    <div class="about-images-area">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="img1">
                                    <div class="space70 d-md-block d-none"></div>
                                    <div class="space30 d-md-none d-block"></div>
                                    <img src="{{ asset('img/img-about-1.jpg') }}" alt="Boomer Elecomm" title="Boomer Elecomm">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="img1">
                                    <div class="space30 d-md-none d-block"></div>
                                    <img src="{{ asset('img/img-about-2a.jpg') }}" alt="Boomer Elecomm" title="Boomer Elecomm">
                                    <div class="about-footer-bottom">
                                        <div class="img">
                                            <img src="{{ asset('img/icons/star1.svg') }}" alt="">
                                        </div>
                                        <div class="content">
                                            <span>Asesoramos, diseñamos y construimos sus proyectos</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== ABOUT AREA ENDS =======-->

    <!--===== ALLIES AREA STARTS =======-->
    <div class="slider-section-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 m-auto">
                    <div class="header-slider text-center">
                        <h3>Nuestros aliados en tecnología</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="slider-images owl-carousel">
                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-1.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-2.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-3.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-4.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-5.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-6.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-7.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-8.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-9.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-10.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-11.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-12.png') }}" alt="">
                        </div>

                        <div class="img1">
                            <img src="{{ asset('img/allies/img-ally-13.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== ALLIES AREA ENDS =======-->

@endsection