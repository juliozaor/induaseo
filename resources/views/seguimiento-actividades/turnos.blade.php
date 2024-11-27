{{-- 
@extends('layouts.app')

@section('content')
<div class="container">
    @foreach($turnos as $turno) --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Turno de supervisión</h5>
            <div class="card-info">
                <img src="{{ asset('assets/icons/supervision.svg') }}" alt="Supervisión" class="card-icon">
                <span>{{ $turno->hora }}</span>
                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="Fecha" class="card-icon">
                <span>{{ $turno->fecha }}</span>
            </div>
            <h5 class="card-title">Sede</h5>
            <div class="card-info">
                <img src="{{ asset('assets/icons/location.svg') }}" alt="Ubicación" class="card-icon">
                <span>{{ $turno->sede }}</span>
            </div>
            <p class="card-text">{{ $turno->descripcion }}</p>
            <a href="{{ url('/iniciar-turno/'.$turno->id) }}" class="btn btn-primary">Iniciar turno</a>
        </div>
    </div>{{-- 
    @endforeach
</div>
@endsection --}}