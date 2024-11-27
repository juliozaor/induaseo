@extends('layouts.mobile')

@section('content')
<div class="container">
    <!-- Imagen grande con texto centrado -->
    <div class="large-image-container">
        <img src="{{ asset('assets/images/fondo1.png') }}" alt="Large Image" class="large-image">
        <div class="titulo-super">¡BIENVENIDO AL SISTEMA DE SUPERVISIÓN DE ACTIVIDADES INDUASEO!</div>
    </div>

    <!-- Rectángulo con icono y texto -->
    <div class="rectangle">
        <img src="{{ asset('assets/icons/turnoasignado.svg') }}" alt="Icono" class="rectangle-icon">
        <span class="rectangle-text">Turnos asignados</span>
    </div>
    
    @foreach($turnos as $turno)
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Turno de supervisión</h5>
            <div class="card-info">
                <img src="{{ asset('assets/icons/reloj.svg') }}" alt="Supervisión" class="card-icon">
                <span>13:00 - 18:00</span>
                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="Fecha" class="card-icon">
                <span>{{$turno->fecha_inicio}} al {{$turno->fecha_fin}}</span>
            </div>
            <h5 class="card-title">Sede</h5>
            <div class="card-info">
                <img src="{{ asset('assets/icons/location.svg') }}" alt="Ubicación" class="card-icon">
                <span>{{$turno->sede->nombre}} <br> {{$turno->sede->direccion}}</span>
            </div>
            <a href="#" class="btn btn-primary btn-iniciarturno">Iniciar turno</a>
        </div>
    </div>
    @endforeach

    <span class="titulo-novedades">Novedades</span>
    <div class="novedades-slider">
        @foreach([1, 2, 3] as $novedad)
        <div class="novedad-card">
            <div class="novedad-image-container">
                <img src="{{ asset('assets/images/novedad.jpg') }}" alt="Novedad" class="novedad-image">
                <span class="novedad-span">Tipo</span>
            </div>
            <div class="novedad-info">
                <span class="novedad-date">Fecha</span>
                <p class="novedad-text">Descripción de la novedad {{ $novedad }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
