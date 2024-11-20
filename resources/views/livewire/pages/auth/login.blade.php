<!-- resources/views/livewire/pages/auth/login.blade.php -->
@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container">
    <div class="login-card">
        <!-- Logo en la parte superior -->
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo-img">

        <!-- Texto de encabezado -->
        <p class="login-header">Ingresa tus datos para iniciar sesión</p>

        <!-- Formulario de inicio de sesión -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Campo de Usuario -->
            <label for="usuario" class="label">Usuario</label>
            <input id="usuario" type="text" class="form-control @error('usuario') is-invalid @enderror" name="usuario" required autofocus>
            @error('usuario')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror

            <!-- Campo de Contraseña -->
            <label for="password" class="label mt-3">Contraseña</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
            @error('password')
                 <span class="invalid-feedback">{{ $message }}</span>
            @enderror

            <!-- CAPTCHA -->
          {{--   <div class="form-group mt-3">
                {!! NoCaptcha::display(['class' => $errors->has('g-recaptcha-response') ? 'is-invalid' : '']) !!}
                @error('g-recaptcha-response')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div> --}}

            <!-- Botón de Iniciar Sesión -->
            <button type="submit" class="btn btn-login">Iniciar Sesión</button>
        </form>

        <!-- Línea divisoria -->
        <div class="divider"></div>

        <!-- Enlace de recuperación de contraseña -->
        <p class="forgot-password-text">¿Olvidó su contraseña?</p>
        <a href="#" data-toggle="modal" data-target="#forgotPasswordModal" class="forgot-password-link">Recupérala aquí</a>
    </div>
</div>

<!-- Modal de recuperación de contraseña -->
@include('livewire.pages.auth.partials.forgot_password_modal')
@endsection

<!-- Script de NoCaptcha -->
{!! NoCaptcha::renderJs() !!}
