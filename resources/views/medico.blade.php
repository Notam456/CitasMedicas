@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Médicos</h3>
            <a href="{{ route('medicos.create') }}" class="btn btn-primary">+ Nuevo Médico</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicos as $m)
                    <tr>
                        <td>{{ $m->id_medico }}</td>
                        <td>{{ $m->nombres }}</td>
                        <td>{{ $m->apellidos }}</td>
                        <td>{{ $m->cedula }}</td>
                        <td>{{ $m->telefono }}</td>
                        <td>{{ $m->especialidad->nombre ?? 'N/A' }}</td>
                        <td>
                            @if($m->estado)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('medicos.edit', $m->id_medico) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('medicos.destroy', $m->id_medico) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar médico?')">Eliminar</button>
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