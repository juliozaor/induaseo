<!-- resources/views/livewire/pages/auth/reset_password.blade.php -->
@extends('layouts.app')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="login-card">
    <!-- Logo en la parte superior -->
    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo-img">

    <!-- Mensaje de éxito -->
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- Línea divisoria -->
    <div class="divider"></div>

    <!-- Mensajes de error de validación -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Título y texto de instrucciones -->
    <p class="login-header">Actualice su contraseña</p>
    <p class="subtitulo aling-initial">Ha ingresado con una contraseña temporal. Por seguridad debe actualizar su contraseña.</p>

    <p class="subtitulo">La contraseña nueva debe contener como mínimo:</p>
    <ul class="requirements-list">
        <li><img src="{{ asset('assets/images/check.png') }}" alt="check">8 caracteres</li>
        <li><img src="{{ asset('assets/images/check.png') }}" alt="check">1 letra mayúscula</li>
        <li><img src="{{ asset('assets/images/check.png') }}" alt="check">1 letra minúscula</li>
        <li><img src="{{ asset('assets/images/check.png') }}" alt="check">1 número</li>
        <li><img src="{{ asset('assets/images/check.png') }}" alt="check">1 carácter especial</li>
        <li></li>
    </ul>
    

    <!-- Formulario de restablecimiento de contraseña -->
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- Token de restablecimiento de contraseña -->
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Nueva Contraseña -->
        <div class="form-group">
            <label for="password" class="label">Nueva Contraseña</label>
            <input id="password" type="password" class="form-control" name="password" required>
            @error('password')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
        </div>

        <!-- Confirmar Nueva Contraseña -->
        <div class="form-group mt-3">
            <label for="password_confirmation" class="label">Confirmar Nueva Contraseña</label>
            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
            @error('password_confirmation')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
        </div>

        <!-- Botón de restablecimiento de contraseña -->
        <button type="submit" class="btn btn-primary mt-3">Restablecer Contraseña</button>
    </form>
</div>
@endsection
