<!-- Header -->
<header id="page-header">
    <!-- Header Content -->
    <div class="content-header">
        <!-- Left Section -->
        <div class="space-x-1">
            <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div class="space-x-1">
            <!-- User Dropdown -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-user d-sm-none"></i>
                    <span class="d-none d-sm-inline-block fw-semibold">{{ Auth::user()->name }}</span>
                    <i class="fa fa-angle-down opacity-50 ms-1"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
                    <div class="px-2 py-3 bg-body-light rounded-top">
                        <h5 class="h6 text-center mb-0">
                            {{ Auth::user()->name }}
                        </h5>
                    </div>
                    <div class="p-2">
                        @if(Auth::user()->hasAnyRole(['supervisor', 'purchasing']))
                            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('profile.edit') }}">
                                <span>Perfil</span>
                                <i class="fa fa-fw fa-user opacity-25"></i>
                            </a>
                        @endif
                        <!-- <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:void(0)">
                            <span>Mensajes</span>
                            <i class="fa fa-fw fa-envelope-open opacity-25"></i>
                        </a>
                        <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="javascript:void(0)">
                            <span>Invoices</span>
                            <i class="fa fa-fw fa-file opacity-25"></i>
                        </a> -->
                        @if(Auth::user()->hasAnyRole(['admin', 'developer']))
                            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('user.index') }}">
                                <span>Usuarios</span>
                                <i class="fa fa-fw fa-users opacity-25"></i>
                            </a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('holiday.index') }}">
                                <span>Días Feriados</span>
                                <i class="fa fa-fw fa-calendar-alt opacity-25"></i>
                            </a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('setting.index') }}">
                                <span>Configuración</span>
                                <i class="fa fa-fw fa-cogs opacity-25"></i>
                            </a>

                            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('backups.index') }}">
                                <span>Copias de Seguridad</span>
                                <i class="fa fa-fw fa-cloud-arrow-up opacity-25"></i>
                            </a>
                        @endif

                        @if(Auth::user()->hasRole('developer'))
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('role.index') }}">
                                <span>Roles</span>
                                <i class="fa fa-fw fa-user-shield opacity-25"></i>
                            </a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('developer.index') }}">
                                <span>Desarrollo</span>
                                <i class="fa fa-fw fa-terminal opacity-25"></i>
                            </a>
                            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('developer.logs') }}">
                                <span>Logs de Errores</span>
                                <i class="fa fa-fw fa-bug opacity-25"></i>
                            </a>
                        @endif

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span>Cerrar Sesión</span>
                            <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </a>
                    </div>
                </div>
            </div>
            <!-- END User Dropdown -->

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