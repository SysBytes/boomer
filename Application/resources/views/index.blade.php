@extends('layouts.master')


@section('js')
    <script src="https://web3forms.com/client/script.js" async defer></script>

    <script>
        const form = document.getElementById('b1153a61-2222-4408-9777-c296e2c98a3d');

        form.addEventListener('submit', function (e) {

            const hCaptcha = form.querySelector('textarea[name=h-captcha-response]').value;

            if (!hCaptcha) {
                e.preventDefault();
                alert("Por favor completa el campo de captcha")
                return
            }
        });
    </script>
@endsection

@section('content')

    @include('partials.home.hero')

    <!--===== ABOUT AREA STARTS =======-->
    <div id="about" class="about1-section-area sp1">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-images-area">
                        <div class="img1" data-aos="zoom-out" data-aos-duration="1000">
                            <img src="{{ asset('img/img-home-about_1a.jpg') }}" alt="">
                        </div>
                        <div class="img2" data-aos="zoom-out" data-aos-duration="1100">
                            <img src="{{ asset('img/img-home-about_2.jpg') }}" alt="">
                        </div>
                        <div class="conter-area" data-aos="zoom-out" data-aos-duration="1200">
                            <h3><span class="counter">12</span>+</h3>
                            <p>Años de experiencia</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="about-header-area heading2">
                        <h5>Acerca de nosotros</h5>
                        <h2 class="tg-element-title">Somos su aliado en el desarrollo de infraestructura</h2>
                        <p data-aos="fade-left" data-aos-duration="1000" class="text-justify">
                            En <b>Boomer Elecomm</b> integramos consultoría, diseño, construcción y mantenimiento para el sector energético y de comunicaciones.<br>
                            Nos especializamos en proyectos de alta calidad, garantizando eficiencia, cumplimiento normativo y tecnología sostenible, para industrias de:
                        </p>
                        <ul data-aos="fade-left" data-aos-duration="1100">
                            <li><img src="{{ asset('img/icons/check1.svg') }}" alt="">Energía</li>
                            <li><img src="{{ asset('img/icons/check1.svg') }}" alt="">Oil & Gas</li>
                            <li><img src="{{ asset('img/icons/check1.svg') }}" alt="">Comercial & Retail</li>
                            <li><img src="{{ asset('img/icons/check1.svg') }}" alt="">Manufactura</li>
                        </ul>

                        <div class="btn-area" data-aos="fade-left" data-aos-duration="1200">
                            <a href="{{ route('about') }}" class="header-btn1">Conócenos <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== ABOUT AREA ENDS =======-->

    @include('partials.home.services')

    <!--===== CONTACT AREA STARTS =======-->
    <div id="contact" class="contact1-section-area sp1" style="background-image: url({{ asset('img/img-contact.jpg') }}); background-position: center; background-position: center; background-repeat: no-repeat; background-size: cover;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="contact-header-area heading2">
                        <h5 data-aos="fade-left" data-aos-duration="800">Contáctanos</h5>
                        <h2 class="tg-element-title">Asesoramos, diseñamos y construimos sus proyectos</h2>
                        <p data-aos="fade-left" data-aos-duration="1000" class="text-justify">
                            Nuestro equipo está listo para ayudarte en todos los aspectos de tu proyecto. Desde la planificación inicial hasta la ejecución final, nos aseguramos de que todo se desarrolle de
                            manera eficiente y segura.</p>
                        <div class="space48"></div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-auhtor-side" data-aos="fade-left" data-aos-duration="1000">
                                    <div class="icons-text">
                                        <div class="icons">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div class="text">
                                            <p>Teléfonos</p>
                                            <a href="tel:+573186516672">+57 (318) 6516672</a><br>
                                            <a href="tel:+5491135946450">+54 9 11 35946450</a>
                                        </div>
                                    </div>
                                    <div class="space48"></div>
                                    <div class="icons-text">
                                        <div class="icons">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div class="text">
                                            <p>Correo Electrónico</p>
                                            <a href="mailto:info@boomer-elecomm.com">info@boomer-elecomm.com </a>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="space48 d-md-none"></div>
                                <div class="contact-auhtor-side" data-aos="fade-left" data-aos-duration="1200">
                                    <div class="icons-text">
                                        <div class="icons">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div class="text">
                                            <p>Colombia</p>
                                            <small class="text-white">Calle. 106 #48 - 46, Ofic. 01. Bogotá</small>
                                        </div>
                                    </div>
                                    <div class="space48"></div>
                                    <div class="icons-text">
                                        <div class="icons">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div class="text">
                                            <p>Argentina</p>
                                            <small class="text-white">Cabildo 434, piso 3, CABA. Buenos Aires</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="contact-submit-boxarea">
                        <h4>Contáctanos</h4>
                        <form action="https://api.web3forms.com/submit" method="POST">
                            <input type="hidden" name="access_key" value="b1153a61-2222-4408-9777-c296e2c98a3d">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-area">
                                        <p>Nombre</p>
                                        <input name="name" type="text" placeholder="Nombre" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-area">
                                        <p>Teléfono</p>
                                        <input name="phone" type="number" placeholder="Teléfono" required>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="input-area">
                                        <p>Correo electrónico</p>
                                        <input name="email" type="email" placeholder="Correo electrónico" required>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="input-area">
                                        <p>Detalles adicionales de su proyecto</p>
                                        <textarea name="message" placeholder="Déjanos tu mensaje" cols="30" rows="10" required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-area1">
                                        <input name="terms" type="checkbox" required>
                                        <p>Acepto la política de privacidad del sitio web.</p>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="input-area1">
                                        <div class="h-captcha" data-captcha="true" data-lang="es"></div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="input-area1">
                                        <button type="submit" class="header-btn1">Enviar <i class="fa-solid fa-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===== CONTACT AREA ENDS =======-->

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