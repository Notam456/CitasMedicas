@extends('layouts.template')
@section('title', 'Dashboard | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Pacientes</h3>
        </div>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Cedula</th>
                    <th>Direccion</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pacientes as $paciente)
                    <tr>
                        <td>
                            <div>
                                <a class="d-inline-block text-heading text-primary-hover fw-semibold" href="{{ route('paciente.show', $paciente->id_paciente) }}">
                                    {{ $paciente->nombre }}
                                </a>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="d-inline-block text-heading text-primary-hover fw-semibold">
                                    {{ $paciente->apellido }}
                                </p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="d-inline-block text-heading text-primary-hover fw-semibold">
                                    {{ $paciente->cedula }}
                                </p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p class="d-inline-block text-heading text-primary-hover fw-semibold">
                                    {{ $paciente->direccion }}
                                </p>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <a href="{{ route('paciente.show', $paciente->id) }}" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('paciente.edit', $paciente->id) }}" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('paciente.destroy', $paciente->id) }}" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach 
            </tbody>
        </table>
    </div>


    <!-- Modal Registrar Paciente 
    <div class="modal fade" id="modalPaciente" tabindex="-1" aria-labelledby="modalPacienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPacienteLabel">Registrar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('paciente.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('name') }}" class="form-control" id="nombreUsuario" name="name" placeholder="Nombre de usuario" required>
                            <label for="nombreUsuario">Nombres</label>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" value="{{ old('email') }}" class="form-control" id="emailUsuario" name="email" placeholder="Correo electrónico" required>
                            <label for="emailUsuario">Email</label>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="passwordUsuario" name="password" placeholder="Contraseña" required>
                            <label for="passwordUsuario">Contraseña</label>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="passwordConfirmUsuario" name="password_confirmation" placeholder="Confirmar contraseña" required>
                            <label for="passwordConfirmUsuario">Confirmar Contraseña</label>
                            @error('password_confirmation')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

    <!-- Modal Editar Paciente (similar al de registrar, pero con campos prellenados) -->
    <div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-labelledby="modalEditarPacienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPacienteLabel">Editar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ isset($pacienteToEdit) ? route('paciente.update', $pacienteToEdit->id) : '#' }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('nombre', $pacienteToEdit->nombre ?? '') }}" class="form-control" id="editarNombrePaciente" name="nombre" placeholder="Nombres del paciente" required>
                            <label for="editarNombrePaciente">Nombres</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('apellido', $pacienteToEdit->apellido ?? '') }}" class="form-control" id="editarApellidoPaciente" name="apellido" placeholder="Apellidos del paciente" required>
                            <label for="editarApellidoPaciente">Apellidos</label>
                            @error('apellido')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('cedula', $pacienteToEdit->cedula ?? '') }}" class="form-control" id="editarCedulaPaciente" name="cedula" placeholder="Cédula del paciente" required>
                            <label for="editarCedulaPaciente">Cédula</label>
                            @error('cedula')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" value="{{ old('fecha_nacimiento', $pacienteToEdit->fecha_nacimiento ?? '') }}" class="form-control" id="editarFechaNacimientoPaciente" name="fecha_nacimiento" placeholder="Fecha de nacimiento" required>
                            <label for="editarFechaNacimientoPaciente">Fecha de Nacimiento</label>
                            @error('fecha_nacimiento')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('telefono', $pacienteToEdit->telefono ?? '') }}" class="form-control" id="editarTelefonoPaciente" name="telefono" placeholder="Teléfono del paciente" required>
                            <label for="editarTelefonoPaciente">Teléfono</label>
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('direccion', $pacienteToEdit->direccion ?? '') }}" class="form-control" id="editarDireccionPaciente" name="direccion" placeholder="Dirección del paciente" required>
                            <label for="editarDireccionPaciente">Dirección</label>
                            @error('direccion')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 

    <!-- Modal para mostrar datos de los pacientes -->
    <div class="modal fade" id="modalShowPaciente" tabindex="-1" aria-labelledby="modalEditarPacienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPacienteLabel">Datos del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                @isset($pacienteToshow)
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Nombres</label>
                            <p class="form-control">{{ $pacienteToshow->nombre }}</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Apellidos</label>
                            <p class="form-control">{{ $pacienteToshow->apellido }}</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Cédula</label>
                            <p class="form-control">{{ $pacienteToshow->cedula }}</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Fecha de Nacimiento</label>
                            <p class="form-control">{{ $pacienteToshow->fecha_nacimiento }}</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Teléfono</label>
                            <p class="form-control">{{ $pacienteToshow->telefono }}</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Estado</label>
                            <p class="form-control">{{ $pacienteToshow->parroquia->municipio->estado->nombre ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Municipio</label>
                            <p class="form-control">{{ $pacienteToshow->parroquia->municipio->nombre ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Parroquia</label>
                            <p class="form-control">{{ $pacienteToshow->parroquia->nombre ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <label for="editarNombrePaciente">Dirección</label>
                            <p class="form-control">{{ $pacienteToshow->direccion }}</p>
                        </div>
                    </div>
                @endisset
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
            </div>
        </div>
    </div> 

@if(isset($pacienteToEdit))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('modalEditarPaciente');
    if (modalEl) {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
});
</script>
@endif

@if(isset($pacienteToshow))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('modalShowPaciente');
    if (modalEl) {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
});
</script>
@endif

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let errorMessages = '';
        
        @foreach ($errors->all() as $error)
            errorMessages += '• {{ $error }}\n';
        @endforeach

        Swal.fire({
            icon: 'error',
            title: '¡Ups! Algo salió mal en tu accion intentalo de nuevo',
            text: errorMessages,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Entendido'
        });
    });
</script>
@endif

@include('layouts.footer')

@endsection