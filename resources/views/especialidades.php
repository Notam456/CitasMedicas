@extends('layouts.template')

@section('content')
<div class="container">
    <h1>Especialidades Médicas</h1>
    
    <!-- Botón para mostrar formulario -->
    <button onclick="mostrarFormulario()" class="btn btn-primary">+ Nueva Especialidad</button>
    
    <!-- Formulario para crear (oculto al inicio) -->
    <div id="formCrear" style="display:none; margin-top:20px;">
        <h3>Crear Especialidad</h3>
        <form action="{{ route('especialidades.store') }}" method="POST">
            @csrf
            <input type="text" name="nombre" placeholder="Nombre" required>
            <textarea name="descripcion" placeholder="Descripción"></textarea>
            <input type="checkbox" name="estado" value="1" checked> Activo
            <button type="submit">Guardar</button>
        </form>
    </div>
    
    <!-- Tabla de especialidades -->
    <table class="table">
        <thead>
            <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @foreach($especialidades as $e)
            <tr>
                <td>{{ $e->id_especialidad }}</td>
                <td>{{ $e->nombre }}</td>
                <td>{{ $e->descripcion }}</td>
                <td>{{ $e->estado ? 'Activo' : 'Inactivo' }}</td>
                <td>
                    <button onclick="mostrarEditar({{ $e }})">Editar</button>
                    <form action="{{ route('especialidades.destroy', $e) }}" method="POST" style="display:inline;">
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
        <h3>Editar Especialidad</h3>
        <form id="formEditarAction" method="POST">
            @csrf @method('PUT')
            <input type="text" name="nombre" id="edit_nombre" required>
            <textarea name="descripcion" id="edit_descripcion"></textarea>
            <input type="checkbox" name="estado" value="1" id="edit_estado"> Activo
            <button type="submit">Actualizar</button>
        </form>
    </div>
</div>

<script>
    function mostrarFormulario() {
        document.getElementById('formCrear').style.display = 'block';
    }
    
    function mostrarEditar(especialidad) {
        document.getElementById('formEditar').style.display = 'block';
        document.getElementById('edit_nombre').value = especialidad.nombre;
        document.getElementById('edit_descripcion').value = especialidad.descripcion;
        document.getElementById('edit_estado').checked = especialidad.estado;
        document.getElementById('formEditarAction').action = "{{ url('especialidades') }}/" + especialidad.id_especialidad;
    }
</script>
@endsection