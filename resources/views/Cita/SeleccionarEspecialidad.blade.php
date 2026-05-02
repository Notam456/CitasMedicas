@extends('layouts.template')
@section('title','Dashboard | SAGECIM')
    
@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<style>
    /*CSS necesario para el efecto de agrandar */
    .card-especialidad {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-especialidad:hover {
        transform: scale(1.05); 
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important; 
        border-color: #0d6efd !important;
    }
</style>

<div class="container-fluid px-4 py-4">
    
    <div class="border-bottom border-primary border-3 pb-3 mb-5 mt-2">
        <h1 class="display-5 fw-bold text-dark text-uppercase mb-1">Nuestras Especialidades</h1>
        <p class="text-muted fs-5 mb-0">Seleccione la especialidad en la que desea agendar su cita médica.</p>
    </div>

    <div class="row g-4">
        
        @foreach($especialidades as $especialidad)
            <div class="col-12 col-md-6 col-lg-10">                
                <a href="{{ route ('Citas.agendar.especialidad', $especialidad->id) }}" class="text-decoration-none text-body h-100 d-block">
                    <div class="card h-100 shadow-sm border-light card-especialidad">
                        <div class="card-body d-flex flex-column">
                            
                            <h3 class="card-title h4 text-primary border-bottom pb-2 mb-3">
                                {{ $especialidad->nombre }}
                            </h3>
                            <div class="mt-auto">
                                <strong class="d-block mb-2 text-dark">Médicos disponibles:</strong>
                                @forelse($especialidad->medicos as $medico)
                                    <div class="d-flex align-items-center text-secondary mb-1">
                                        <!-- Viñeta simulada con Bootstrap -->
                                        <span class="text-primary fs-5 lh-1 me-2">&bull;</span> 
                                        <span>Dr/Dra. {{ $medico->nombre }} {{ $medico->apellido }}</span>
                                    </div>
                                @empty
                                    <div class="text-muted small fst-italic">No hay médicos registrados.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

@endsection
