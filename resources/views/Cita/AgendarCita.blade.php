@extends('layouts.template')
@section('title', 'Citas de ' . $especialidad->nombre)

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="container-fluid px-4 py-4">
    
    <div class="d-flex justify-content-between align-items-center border-bottom border-primary border-3 pb-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold text-dark mb-0">Citas Agendadas para {{ $especialidad->nombre }}</h1>
            <p class="text-muted mb-0">Gestión de citas y cupos de la especialidad.</p>
        </div>
        <a href="{{ route('Citas.createEspecialidad', $especialidad->id) }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> Agendar Nueva Cita
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Paciente</th>
                        <th>Cédula</th>
                        <th>Fecha de Cita</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citas as $cita)
                        <tr>
                            <td>{{ $cita->paciente->nombre }} {{ $cita->paciente->apellido }}</td>
                            <td>{{ $cita->paciente->cedula }}</td>
                            <td>{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</td>
                            <td>{{ $cita->observacion ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay citas agendadas para esta especialidad.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('layouts.footer')
@endsection