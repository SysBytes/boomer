<!-- Sidebar -->
<nav id="sidebar">
    <!-- Sidebar Content -->
    <div class="sidebar-content">
        <!-- Side Header -->
        <div class="content-header justify-content-lg-center">
            <!-- Logo -->
            <div class="main-logo">
                <span class="smini-visible fw-bold tracking-wide fs-lg">
                    <img src="{{ asset('img/boomer-favicon.png') }}" alt="Boomer Elecomm" title="Boomer Elecomm">
                </span>
                <a class="mx-auto" href="{{ route('backoffice') }}">
                    <img src="{{ asset('img/logo/logo_boomer-elecomm.png') }}" alt="Boomer Elecomm" title="Boomer Elecomm">
                </a>
            </div>
            <!-- END Logo -->

            <!-- Options -->
            <div>
                <button type="button" class="btn btn-sm btn-alt-danger d-lg-none" data-toggle="layout" data-action="sidebar_close">
                    <i class="fa fa-fw fa-times"></i>
                </button>
            </div>
            <!-- END Options -->
        </div>
        <!-- END Side Header -->

        <!-- Sidebar Scrolling -->
        <div class="js-sidebar-scroll">
            <!-- Side User -->
            <div class="content-side content-side-user px-0 py-0">
                <!-- Visible only in normal mode -->
                <div class="smini-hidden text-center mx-auto">

                    <ul class="list-inline mt-0 mb-0">
                        <li class="list-inline-item">
                            <a class="link-fx text-dual fs-sm fw-semibold text-uppercase" href="javascript:void(0)">
                                {{ Auth::user()->name }}
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a class="link-fx text-dual" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out-alt"></i>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- END Visible only in normal mode -->

            </div>
            <!-- END Side User -->

            <!-- Side Navigation -->
            <div class="content-side content-side-full pb-7">
                <ul class="nav-main">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('backoffice') ? ' active' : '' }}" href="{{ route('backoffice') }}">
                            <i class="nav-main-link-icon fa fa-house-user"></i>
                            <span class="nav-main-link-name">Inicio</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('dashboard') }}">
                            <i class="nav-main-link-icon fa-solid fa-computer"></i>
                            <span class="nav-main-link-name">Área de trabajo</span>
                        </a>
                    </li>

                    <li class="nav-main-heading">Procesos</li>

                    <li class="nav-main-item {{ request()->routeIs('requisitions.*') ? 'open' : '' }}">
                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" href="javascript:void(0)">
                            <i class="nav-main-link-icon fa-solid fa-cart-shopping"></i>
                            <span class="nav-main-link-name">Requisiciones</span>
                        </a>
                        <ul class="nav-main-submenu">
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ (request()->routeIs('requisitions.index') || request()->routeIs('requisitions.status') || request()->routeIs('requisitions.show')) && request('status') == 'pending' ? 'active text-primary' : '' }}"
                                    href="{{ route('requisitions.index', ['status' => 'pending']) }}">
                                    <span class="nav-main-link-name">Pendientes</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ (request()->routeIs('requisitions.index') || request()->routeIs('requisitions.status') || request()->routeIs('requisitions.show')) && request('status') == 'approved' ? 'active text-primary' : '' }}"
                                    href="{{ route('requisitions.index', ['status' => 'approved']) }}">
                                    <span class="nav-main-link-name">Aprobadas</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ (request()->routeIs('requisitions.index') || request()->routeIs('requisitions.status') || request()->routeIs('requisitions.show')) && request('status') == 'rejected' ? 'active text-primary' : '' }}"
                                    href="{{ route('requisitions.index', ['status' => 'rejected']) }}">
                                    <span class="nav-main-link-name">Rechazadas</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ (request()->routeIs('requisitions.index') || request()->routeIs('requisitions.status') || request()->routeIs('requisitions.show')) && request('status') == 'purchased' ? 'active text-primary' : '' }}"
                                    href="{{ route('requisitions.index', ['status' => 'purchased']) }}">
                                    <span class="nav-main-link-name">Compradas</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ (request()->routeIs('requisitions.index') || request()->routeIs('requisitions.status') || request()->routeIs('requisitions.show')) && request('status') == 'delivered' ? 'active text-primary' : '' }}"
                                    href="{{ route('requisitions.index', ['status' => 'delivered']) }}">
                                    <span class="nav-main-link-name">Entregadas</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->url() == route('timelogs.index') ? ' active' : '' }}" href="{{ route('timelogs.index') }}">
                            <i class="nav-main-link-icon fa fa-clock"></i>
                            <span class="nav-main-link-name">Horas</span>
                        </a>
                    </li>


                    <li class="nav-main-heading">Administración</li>

                    @if(Auth::user()->hasAnyRole(['admin', 'developer']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('timelogs.manual') ? ' active' : '' }}" href="{{ route('timelogs.manual') }}">
                                <i class="nav-main-link-icon fa-solid fa-door-open"></i>
                                <span class="nav-main-link-name">Check-In / Check-Out</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->hasAnyRole(['admin', 'developer', 'supervisor']))
                        <li class="nav-main-item">
                            <a class="nav-main-link{{ request()->url() == route('overtimes.index') ? ' active' : '' }}" href="{{ route('overtimes.index') }}">
                                <i class="nav-main-link-icon fa fa-business-time"></i>
                                <span class="nav-main-link-name">Horas Extras</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->hasAnyRole(['admin', 'developer', 'supervisor']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('technicians.index') ? ' active' : '' }}" href="{{ route('technicians.index') }}">
                                <i class="nav-main-link-icon fa-solid fa-toolbox"></i>
                                <span class="nav-main-link-name">Técnicos por Proyecto</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->hasAnyRole(['admin', 'developer', 'purchasing']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('import.material.*') ? ' active' : '' }}" href="{{ route('import.material') }}">
                                <i class="nav-main-link-icon fa-solid fa-file-import"></i>
                                <span class="nav-main-link-name">Importar Material</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-main-heading">Documentos</li>

                    <li class="nav-main-item">
                        <a class="nav-main-link {{ request()->routeIs('induction.contents.*') ? 'active' : '' }}" href="{{ route('induction.contents.index') }}">
                            <i class="nav-main-link-icon fa-solid fa-file-lines"></i>
                            <span class="nav-main-link-name">Contenidos</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link {{ request()->routeIs('induction.categories.*') ? 'active' : '' }}" href="{{ route('induction.categories.index') }}">
                            <i class="nav-main-link-icon fa-solid fa-table"></i>
                            <span class="nav-main-link-name">Categorías</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link {{ request()->routeIs('induction.subcategories.*') ? 'active' : '' }}" href="{{ route('induction.subcategories.index') }}">
                            <i class="nav-main-link-icon fa-solid fa-table-cells"></i>
                            <span class="nav-main-link-name">Subcategorías</span>
                        </a>
                    </li>

                    <li class="nav-main-heading">Reportes</li>

                    <li class="nav-main-item">
                        <a class="nav-main-link {{ request()->routeIs('reports.materials_by_work') ? ' active' : '' }}" href="{{ route('reports.materials_by_work') }}">
                            <i class="nav-main-link-icon fa-solid fa-file-invoice-dollar"></i>
                            <span class="nav-main-link-name">Materiales por Proyecto</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link {{ request()->routeIs('reports.inventory') ? ' active' : '' }}" href="{{ route('reports.inventory') }}">
                            <i class="nav-main-link-icon fa-solid fa-boxes-stacked"></i>
                            <span class="nav-main-link-name">Inventario Actual</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link {{ request()->routeIs('reports.worked_hours') ? ' active' : '' }}" href="{{ route('reports.worked_hours') }}">
                            <i class="nav-main-link-icon fa-solid fa-user-clock"></i>
                            <span class="nav-main-link-name">Horas Trabajadas</span>
                        </a>
                    </li>

                    @if(Auth::user()->hasAnyRole(['admin', 'developer']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('reports.work_users') ? ' active' : '' }}" href="{{ route('reports.work_users') }}">
                                <i class="nav-main-link-icon fa-solid fa-users"></i>
                                <span class="nav-main-link-name">Usuarios por Proyecto</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-main-heading">Tablas</li>

                    @if(Auth::user()->hasAnyRole(['admin', 'developer']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('client.*') ? ' active' : '' }}" href="{{ route('client.index') }}">
                                <i class="nav-main-link-icon fa-regular fa-building"></i>
                                <span class="nav-main-link-name">Clientes</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->hasAnyRole(['admin', 'developer']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('location.*') ? ' active' : '' }}" href="{{ route('location.index') }}">
                                <i class="nav-main-link-icon fa-solid fa-map-location-dot"></i>
                                <span class="nav-main-link-name">Locaciones</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->hasAnyRole(['admin', 'developer', 'supervisor']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('work.*') ? ' active' : '' }}" href="{{ route('work.index') }}">
                                <i class="nav-main-link-icon fa-solid fa-diagram-project"></i>
                                <span class="nav-main-link-name">Obras</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->hasAnyRole(['admin', 'developer', 'purchasing']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('material.*') ? ' active' : '' }}" href="{{ route('material.index') }}">
                                <i class="nav-main-link-icon fa-solid fa-shapes"></i>
                                <span class="nav-main-link-name">Materiales</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('material_category.*') ? ' active' : '' }}" href="{{ route('material_category.index') }}">
                                <i class="nav-main-link-icon fa-solid fa-layer-group"></i>
                                <span class="nav-main-link-name">Categoría Material</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->hasAnyRole(['admin', 'developer']))
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->routeIs('country.*') ? ' active' : '' }}" href="{{ route('country.index') }}">
                                <i class="nav-main-link-icon fa-solid fa-earth-americas"></i>
                                <span class="nav-main-link-name">Países</span>
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
            <!-- END Side Navigation -->
        </div>
        <!-- END Sidebar Scrolling -->
    </div>
    <!-- Sidebar Content -->
</nav>
<!-- END Sidebar -->