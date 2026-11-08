@extends('layouts.master')

@section('content')
    <!--===== HERO AREA STARTS =======-->
    <div class="hero1-section-area about-bg-area">
        <img src="{{ asset('img/img-bg-internal.jpg') }}" alt="" class="header-img1">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto">
                    <div class="hero-heading-area heading1 text-center">
                        <h1>Nuestros trabajos</h1>
                        <a href="{{ route('home') }}" class="backline">Inicio <i class="fa-solid fa-angle-right"></i> <span>Nuestros trabajos</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== HERO AREA ENDS =======-->

    <!--===== CASE STUDY AREA STARTS =======-->
    <div class="casestudy-inner-section-area sp1">
        <div class="container">
            <div class="row gallery-container">
                @for ($i = 1; $i <= 20; $i++)
                    <div class="col-lg-3 col-md-6">
                        <div class="case-author-boxarea">
                            <div class="imges">
                                <a href="{{ asset('img/gallery/' . $i . '.jpg') }}" class="image-popup">
                                    <img src="{{ asset('img/gallery/' . $i . '.jpg') }}" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
    <!--===== CASE STUDY AREA ENDS =======-->

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.gallery-container').magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
                    }
                });
            });
        </script>
    @endpush

@endsection