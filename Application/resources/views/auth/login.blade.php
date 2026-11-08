@extends('layouts.login')

@section('content')
    <div class="bg-image" style="background-image: url('{{ asset('img/slider-1.jpg') }}');">
        <div class="row mx-0 bg-black-50">
            <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
                <div class="p-4">
                    <p class="fs-3 fw-semibold text-white">
                        Somos soluciones integrales.
                    </p>
                    <p class="text-white-75 fw-medium">
                        Copyright &copy; <span data-toggle="year-copy"></span>
                    </p>
                </div>
            </div>
            <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-body-extra-light">
                <div class="content content-full">
                    <!-- Header -->
                    <div class="px-4 py-2 mb-4">
                        <a class="link-fx fw-bold logo" href="{{ route('home') }}">
                            <img src="{{ asset('img/logo/logo_boomer-elecomm-h.png') }}" alt="Boomer Elecomm" title="Boomer Elecomm">
                        </a>
                        <h1 class="h3 fw-bold mt-4 mb-2">Zona privada</h1>
                        <h2 class="h5 fw-medium text-muted mb-0">Por favor inicia sesión</h2>
                    </div>
                    <!-- END Header -->

                    <!-- Sign In Form -->
                    <form class="js-validation-signin px-4" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-floating mb-4">
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Escribe tu correo electrónico" autofocus>
                            <label class="form-label" for="email">Correo electrónico</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating mb-4 position-relative">
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="Escribe tu contraseña" style="padding-right: 40px;">
                            <label class="form-label" for="password">Contraseña</label>
                            <span class="position-absolute d-flex align-items-center justify-content-center" 
                                  style="top: 0; right: 0; height: 100%; width: 45px; cursor: pointer; z-index: 5;"
                                  onmouseenter="document.getElementById('password').type = 'text'" 
                                  onmouseleave="document.getElementById('password').type = 'password'">
                                <i class="fa fa-eye text-muted"></i>
                            </span>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                        </div>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-lg btn-alt-primary fw-semibold">
                                Iniciar sesión
                            </button>
                            <!-- <div class="mt-4">
                                    @if (Route::has('password.request'))
                                        <a class="fs-sm fw-medium link-fx text-muted me-2 mb-1 d-inline-block" href="{{ route('password.request') }}">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    @endif
                                </div> -->
                        </div>
                    </form>
                    <!-- END Sign In Form -->
                </div>
            </div>
        </div>
    </div>
@endsection