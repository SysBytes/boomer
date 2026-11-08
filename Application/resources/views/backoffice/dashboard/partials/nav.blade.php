<div class="bg-body-dark">
    <div class="content">
        <div class="row justify-content-center">
            @if(!Auth::user()->hasRole('assistant'))
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded text-center {{ request()->routeIs('requisitions.request') ? 'bg-primary-lighter' : '' }}" href="{{ route('requisitions.request') }}">
                        <div class="block-content px-2">
                            <p class="mt-1 mb-3">
                                <i class="fa-solid fa-shapes {{ request()->routeIs('requisitions.request') ? 'text-white' : 'text-gray' }} fa-2x"></i>
                            </p>
                            <p class="fw-semibold fs-sm text-uppercase {{ request()->routeIs('requisitions.request') ? 'text-white' : '' }}">Solicitar Material</p>
                        </div>
                    </a>
                </div>
            @endif
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded text-center {{ request()->routeIs('checkin.index') ? 'bg-primary-lighter' : '' }}" href="{{ route('checkin.index') }}">
                    <div class="block-content px-2">
                        <p class="mt-1 mb-3">
                            <i class="fa-solid fa-clock {{ request()->routeIs('checkin.index') ? 'text-white' : 'text-gray' }} fa-2x"></i>
                        </p>
                        <p class="fw-semibold fs-sm text-uppercase {{ request()->routeIs('checkin.index') ? 'text-white' : '' }}">Check-in / <br class="d-none d-md-block">Check-out</p>
                    </div>
                </a>
            </div>
            @if(Auth::user()->hasAnyRole(['admin', 'developer', 'staff']))
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded text-center {{ request()->routeIs('staff.checkin.index') ? 'bg-primary-lighter' : '' }}" href="{{ route('staff.checkin.index') }}">
                        <div class="block-content px-2">
                            <p class="mt-1 mb-3">
                                <i class="fa-solid fa-building-user {{ request()->routeIs('staff.checkin.index') ? 'text-white' : 'text-gray' }} fa-2x"></i>
                            </p>
                            <p class="fw-semibold fs-sm text-uppercase {{ request()->routeIs('staff.checkin.index') ? 'text-white' : '' }}">Check-in / <br class="d-none d-md-block">Check-out</p>
                        </div>
                    </a>
                </div>
            @endif

            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded text-center {{ request()->routeIs('dashboard.corporate-contents.*') ? 'bg-primary-lighter' : '' }}" href="{{ route('dashboard.corporate-contents.index') }}">
                    <div class="block-content px-2">
                        <p class="mt-1 mb-3">
                            <i class="fa-solid fa-book-open {{ request()->routeIs('dashboard.corporate-contents.*') ? 'text-white' : 'text-gray' }} fa-2x"></i>
                        </p>
                        <p class="fw-semibold fs-sm text-uppercase {{ request()->routeIs('dashboard.corporate-contents.*') ? 'text-white' : '' }}">Biblioteca de<br class="d-none d-md-block"> Contenidos</p>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded text-center" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="block-content px-2">
                        <p class="mt-1 mb-3">
                            <i class="fa fa-sign-out-alt text-gray fa-2x"></i>
                        </p>
                        <p class="fw-semibold fs-sm text-uppercase">Cerrar<br class="d-none d-md-block">Sesión</p>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </a>
            </div>
        </div>
    </div>
</div>