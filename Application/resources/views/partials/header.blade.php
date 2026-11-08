<!--=====HEADER START=======-->
<header>
    <div class="header-area homepage1 header header-sticky d-none d-lg-block " id="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-elements">
                        <div class="site-logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('img/logo/logo_boomer-elecomm-h.png') }}" alt="Boomer Elecomm" title="Boomer Elecomm">
                            </a>
                        </div>
                        <div class="main-menu">
                            <ul>
                                <li><a href="{{ route('home') }}">Inicio</a></li>
                                <li><a href="{{ route('about') }}">Nosotros</a></li>
                                <li><a href="/#services">Servicios <i class="fa-solid fa-angle-down"></i></a>
                                    <ul class="dropdown-padding">
                                        <li><a href="{{ route('services.engineering') }}">Ingeniería</a></li>
                                        <li><a href="{{ route('services.construction') }}">Construcción</a></li>
                                        <li><a href="{{ route('services.maintenance') }}">Puesta en servicio y mantenimiento</a></li>
                                        <li><a href="{{ route('services.certifications') }}">Certificaciones y diagnósticos</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ route('gallery') }}">Galería</a></li>
                            </ul>
                        </div>
                        <div class="btn-area">
                            <a href="{{ route('home') }}#contact" class="header-btn1">Contáctanos <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!--=====HEADER END =======-->


<!--===== MOBILE HEADER STARTS =======-->
<div class="mobile-header mobile-header1 d-block d-lg-none">
    <div class="container-fluid">
        <div class="col-12">
            <div class="mobile-header-elements">
                <div class="mobile-logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('img/logo/logo_boomer-elecomm-h.png') }}" alt="">
                    </a>
                </div>
                <div class="mobile-nav-icon dots-menu">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mobile-sidebar mobile-sidebar1">
    <div class="logosicon-area">
        <div class="logos">
            <img src="{{ asset('img/logo/logo_boomer-elecomm.png') }}" alt="">
        </div>
        <div class="menu-close">
            <i class="fa-solid fa-xmark"></i>
        </div>
    </div>
    <div class="mobile-nav mobile-nav1">
        <ul class="mobile-nav-list nav-list1">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('about') }}">Nosotros</a></li>
            <li><a href="/#services">Servicios</a>
                <ul class="sub-menu">
                    <li><a href="{{ route('services.engineering') }}">Ingeniería</a></li>
                    <li><a href="{{ route('services.construction') }}">Construcción</a></li>
                    <li><a href="{{ route('services.maintenance') }}">Puesta en servicio y mantenimiento</a></li>
                    <li><a href="{{ route('services.certifications') }}">Certificaciones y Diagnósticos</a></li>
                </ul>
            </li>
            <li><a href="{{ route('gallery') }}">Galería</a></li>
        </ul>

        <div class="allmobilesection">
            <a href="{{ route('home') }}#contact" class="header-btn1">Contáctanos <i class="fa-solid fa-arrow-right"></i></a>
            <div class="single-footer">
                <h3>Información de Contacto</h3>
                <div class="footer1-contact-info">
                    <div class="contact-info-single">
                        <div class="contact-info-icon">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div class="contact-info-text">
                            <a href="tel:573186516672">+57 (318) 6516672</a><br>
                            <a href="tel:576017559196">+57 (601) 755 9196</a>
                        </div>
                    </div>

                    <div class="contact-info-single">
                        <div class="contact-info-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="contact-info-text">
                            <a href="mailto:info@boomer-elecomm.com">info@boomer-elecomm.com</a>
                        </div>
                    </div>

                    <div class="single-footer">
                        <h3>Nuestra Ubicación</h3>

                        <div class="contact-info-single">
                            <div class="contact-info-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="contact-info-text">
                                <p>Calle. 106 No. 48-46 Ofic. 01. <br>Bogotá, Colombia</p>
                            </div>
                        </div>

                        <div class="contact-info-single">
                            <div class="contact-info-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="contact-info-text">
                                <p>Calle. 106 No. 48-46 Ofic. 01. <br>Buenos Aires, Argentina</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--===== MOBILE HEADER STARTS =======-->