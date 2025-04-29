@extends('layouts.dashboard')
@section('content')
<div class="container mt-3">
    <div class="table-container">
        <h2>Editar Perfil</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nombres" class="form-label">Nombre Completo</label>
                <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror" id="nombres" value="{{ old('nombres', Auth::user()->nombres) }}" required>
                @error('nombres')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', Auth::user()->email) }}" required>
                @error('email')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password">
                @error('password')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Cuenta</button>
        </form>
    </div>
</div>
@endsection
