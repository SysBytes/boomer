<nav id="sidebar">
    <!-- Sidebar Content -->
    <div class="sidebar-content">
        <!-- Side Header -->
        <div class="content-header justify-content-lg-center">
            <!-- Logo -->
            <div>
                <a class="logo mx-auto" href="{{ route('dashboard') }}">
                    <img src="{{ asset('img/logo/logo_boomer-elecomm.png') }}" alt="Boomer Elecomm" title="Boomer Elecomm" style="max-height: 100px;">
                </a>
            </div>
            <!-- END Logo -->

            <!-- Options -->
            <div>
                <!-- Close Sidebar, Visible only on mobile screens -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <button type="button" class="btn btn-sm btn-alt-danger d-lg-none" data-toggle="layout" data-action="sidebar_close">
                    <i class="fa fa-fw fa-times"></i>
                </button>
                <!-- END Close Sidebar -->
            </div>
            <!-- END Options -->
        </div>
        <!-- END Side Header -->

        <!-- Sidebar Scrolling -->
        <div class="js-sidebar-scroll">
            <!-- Side Main Navigation -->
            <div class="content-side content-side-full">
                <ul class="nav-main">
                    @if(!Auth::user()->hasAnyRole(['technician', 'assistant']))
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('backoffice') }}">
                                <i class="nav-main-link-icon fa fa-gear"></i>
                                <span class="nav-main-link-name">Administración</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-main-item">
                        <div class="dropdown-divider"></div>
                        <a class="nav-main-link" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="nav-main-link-icon fa fa-lock"></i>
                            <span class="nav-main-link-name">Cerrar Sesión</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- END Side Main Navigation -->
        </div>
        <!-- END Sidebar Scrolling -->
    </div>
    <!-- Sidebar Content -->
</nav>

<!-- Header -->
<header id="page-header" class="py-1">
    <!-- Header Content -->
    <div class="content-header">
        <!-- Left Section -->
        <div class="space-x-1">
            <!-- Logo -->
            <a class="logo" href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo/logo_boomer-elecomm-h.png') }}" alt="Boomer Elecomm" title="Boomer Elecomm">
            </a>
            <!-- END Logo -->
        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div>
            <!-- Header Navigation -->
            <!-- Desktop Navigation, mobile navigation can be found in #sidebar -->
            <ul class="nav-main nav-main-horizontal nav-main-hover d-none d-lg-block me-1">
                <li class="nav-main-item">
                    <a class="nav-main-link nav-main-link-submenu" data-toggle="nav-submenu" href="#">
                        <i class="nav-main-link-icon fa-solid fa-user-astronaut"></i>
                        <span class="nav-main-link-name">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="nav-main-submenu nav-main-submenu-right">
                        <!-- <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('dashboard') }}">
                                <i class="nav-main-link-icon fa-solid fa-house"></i>
                                <span class="nav-main-link-name">Inicio</span>
                            </a>
                        </li> -->

                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('dashboard.profile.edit') }}">
                                <i class="nav-main-link-icon fa-solid fa-user"></i>
                                <span class="nav-main-link-name">Mi Perfil</span>
                            </a>
                        </li>

                        @if(!Auth::user()->hasAnyRole(['technician', 'assistant']))
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('backoffice') }}">
                                    <i class="nav-main-link-icon fa-solid fa-gear"></i>
                                    <span class="nav-main-link-name">Administración</span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-main-item">

                            <a class="nav-main-link" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="nav-main-link-icon fa fa-lock"></i>
                                <span class="nav-main-link-name">Cerrar Sesión</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- END Header Navigation -->

            <button type="button" class="btn btn-sm btn-alt-secondary d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
                <span>{{ Auth::user()->name }}</span>
                <i class="fa fa-fw fa-bars opacity-50 ms-1"></i>
            </button>
        </div>
        <!-- END Right Section -->
    </div>
    <!-- END Header Content -->

    <!-- Header Loader -->
    <div id="page-header-loader" class="overlay-header bg-primary">
        <div class="content-header">
            <div class="w-100 text-center">
                <i class="far fa-sun fa-spin text-white"></i>
            </div>
        </div>
    </div>
    <!-- END Header Loader -->
</header>
<!-- END Header -->

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>