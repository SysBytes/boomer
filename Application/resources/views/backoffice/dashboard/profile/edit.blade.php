@extends('layouts.dashboard')

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/localization/messages_es.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/be_forms_validation.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('profile-form').addEventListener('submit', function(e) {
                // Efecto de carga
                const submitBtn = document.getElementById('btn-submit');
                const btnIcon = document.getElementById('btn-icon');
                const btnText = document.getElementById('btn-text');
                
                if (jQuery(this).valid()) {
                    submitBtn.disabled = true;
                    btnIcon.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>';
                    btnText.textContent = 'Guardando...';
                }
            });
        });
    </script>
@endsection

@section('content')
<div class="content">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('dashboard') }}" class="btn btn-alt-secondary">
                <i class="fa fa-arrow-left me-1"></i> Volver al Área de Trabajo
            </a>
        </div>
    </div>

    <!-- Título y cabecera -->
    <div class="block block-rounded block-themed">
        <div class="block-header bg-primary-dark">
            <h3 class="block-title">Mi Perfil</h3>
        </div>
        <div class="block-content block-content-full">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('dashboard.profile.update') }}" method="POST" id="profile-form" class="js-validation-bootstrap">
                @csrf
                {{ method_field('PUT') }}
                
                <h2 class="content-heading pt-0">Información de Usuario</h2>
                
                <div class="row form-group mb-4">
                    <label class="col-md-2 col-form-label" for="name">Nombre <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name ?? old('name') }}" required>
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row form-group mb-4">
                    <label class="col-md-2 col-form-label" for="email">Email</label>
                    <div class="col-md-8">
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled>
                        <div class="form-text text-muted">El correo electrónico no puede ser modificado.</div>
                    </div>
                </div>

                <div class="row form-group mb-4">
                    <label class="col-md-2 col-form-label" for="password">Contraseña</label>
                    <div class="col-md-8">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Déjalo en blanco si no deseas cambiarla">
                        @error('password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row form-group mb-5">
                    <label class="col-md-2 col-form-label" for="password_confirmation">Confirmar Contraseña</label>
                    <div class="col-md-8">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirma tu nueva contraseña">
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-12 text-center text-md-end">
                        <button type="submit" id="btn-submit" class="btn btn-lg btn-success">
                            <span id="btn-icon"><i class="fa fa-save me-1"></i></span> <span id="btn-text">Guardar Cambios</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
