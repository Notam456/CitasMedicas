@extends('layouts.template')

@section('content')
<div class="container">
    <h1>Médicos</h1>
    
    <!-- Botón para mostrar formulario -->
    <button onclick="mostrarFormulario()" class="btn btn-primary">+ Nuevo Médico</button>
    
    <!-- Formulario para crear (oculto al inicio) -->
    <div id="formCrear" style="display:none; margin-top:20px;">
        <h3>Crear Médico</h3>
        <form action="{{ route('medicos.store') }}" method="POST">
            @csrf
            <input type="text" name="nombres" placeholder="Nombres" required>
            <input type="text" name="apellidos" placeholder="Apellidos" required>
            <input type="text" name="cedula" placeholder="Cédula" required>
            <input type="text" name="telefono" placeholder="Teléfono" required>
            <select name="id_especialidad" required>
                <option value="">Seleccione Especialidad</option>
                @foreach($especialidades as $e)
                    <option value="{{ $e->id_especialidad }}">{{ $e->nombre }}</option>
                @endforeach
            </select>
            <input type="checkbox" name="estado" value="1" checked> Activo
            <button type="submit">Guardar</button>
        </form>
    </div>
    
    <!-- Tabla de médicos -->
    <table class="table">
        <thead>
            <tr><th>ID</th><th>Nombres</th><th>Apellidos</th><th>Cédula</th><th>Teléfono</th><th>Especialidad</th><th>Estado</th><th>Acciones</th></tr>
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
                <td>{{ $m->estado ? 'Activo' : 'Inactivo' }}</td>
                <td>
                    <button onclick="mostrarEditar({{ $m }})">Editar</button>
                    <form action="{{ route('medicos.destroy', $m) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Formulario para editar (oculto al inicio) -->
    <div id="formEditar" style="display:none; margin-top:20px;">
        <h3>Editar Médico</h3>
        <form id="formEditarAction" method="POST">
            @csrf @method('PUT')
            <input type="text" name="nombres" id="edit_nombres" required>
            <input type="text" name="apellidos" id="edit_apellidos" required>
            <input type="text" name="cedula" id="edit_cedula" required>
            <input type="text" name="telefono" id="edit_telefono" required>
            <select name="id_especialidad" id="edit_id_especialidad" required>
                @foreach($especialidades as $e)
                    <option value="{{ $e->id_especialidad }}">{{ $e->nombre }}</option>
                @endforeach
            </select>
            <input type="checkbox" name="estado" value="1" id="edit_estado"> Activo
            <button type="submit">Actualizar</button>
        </form>
    </div>
</div>

<script>
    function mostrarFormulario() {
        document.getElementById('formCrear').style.display = 'block';
    }
    
    function mostrarEditar(medico) {
        document.getElementById('formEditar').style.display = 'block';
        document.getElementById('edit_nombres').value = medico.nombres;
        document.getElementById('edit_apellidos').value = medico.apellidos;
        document.getElementById('edit_cedula').value = medico.cedula;
        document.getElementById('edit_telefono').value = medico.telefono;
        document.getElementById('edit_id_especialidad').value = medico.id_especialidad;
        document.getElementById('edit_estado').checked = medico.estado;
        document.getElementById('formEditarAction').action = "{{ url('medicos') }}/" + medico.id_medico;
    }
</script>
@endsection