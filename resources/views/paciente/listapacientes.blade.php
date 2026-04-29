@extends('layouts.template')
@section('title', 'Dashboard | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Pacientes</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPaciente">
                <i class="bi bi-person-plus me-1"></i> Registrar Paciente
            </button>
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
                            <span class="badge bg-secondary">No programado aun</span>
                        </td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <a href="{{ route('paciente.edit', $paciente->id_paciente) }}" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('paciente.destroy', $paciente->id_paciente) }}" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach 
            </tbody>
        </table>
    </div>


    <!-- Modal Registrar Paciente -->
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
                            <label for="nombreUsuario">Nombre</label>
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
    </div>

    <!-- Modal Editar Paciente (similar al de registrar, pero con campos prellenados) -->
    <div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-labelledby="modalEditarPacienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPacienteLabel">Editar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ isset($pacienteToEdit) ? route('paciente.update', $pacienteToEdit->id_paciente) : '#' }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('name', $pacienteToEdit->name ?? '') }}" class="form-control" id="editarNombrePaciente" name="name" placeholder="Nombre del paciente" required>
                            <label for="editarNombrePaciente">Nombre</label>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" value="{{ old('email', $pacienteToEdit->email ?? '') }}" class="form-control" id="editarEmailPaciente" name="email" placeholder="Correo electrónico" required>
                            <label for="editarEmailPaciente">Email</label>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="editarPasswordPaciente" name="password" placeholder="Contraseña"">
                            <label for="editarPasswordPaciente">Contraseña (dejar en blanco para no cambiar)</label>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
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

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let errorMessages = '';
        
        // Recorremos todos los errores de Laravel y los acumulamos
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