@extends('layouts.template')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Especialidades Médicas</h3>
            <a href="{{ route('especialidades.create') }}" class="btn btn-primary">+ Nueva Especialidad</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($especialidades as $e)
                    <tr>
                        <td>{{ $e->id_especialidad }}</td>
                        <td>{{ $e->nombre }}</td>
                        <td>{{ $e->descripcion ?? 'Sin descripción' }}</td>
                        <td>
                            @if($e->estado)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('especialidades.edit', $e->id_especialidad) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('especialidades.destroy', $e->id_especialidad) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta especialidad?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection