@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Listado de Pacientes</h3>
            <a href="{{ route('pacientes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Paciente
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Ubicación (E/M/P)</th> {{-- Nueva Columna --}}
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pacientes as $paciente)
                    <tr>
                        <td>{{ $paciente->cedula }}</td>
                        <td>{{ $paciente->nombre }}</td>
                        {{-- Aquí mostramos los nombres de las relaciones --}}
                        <td>
                            <small>
                                <strong>{{ $paciente->estado->nombre ?? 'N/A' }}</strong><br>
                                {{ $paciente->municipio->nombre ?? 'N/A' }}, 
                                {{ $paciente->parroquia->nombre ?? 'N/A' }}
                            </small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('pacientes.destroy', $paciente->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro de eliminar?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay pacientes registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection