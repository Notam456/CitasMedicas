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
                @foreach ($pacientes as $paciente)
                    <tr>
                        <td>
                            <div>
                                <a class="d-inline-block text-heading text-primary-hover fw-semibold"
                                    href="{{ route('paciente.show', $paciente->id_paciente) }}">
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
                                <a href="{{ route('paciente.show', $paciente->id) }}"
                                    class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('paciente.edit', $paciente->id) }}"
                                    class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('paciente.destroy', $paciente->id) }}"
                                    class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover"
                                    data-confirm-delete="true">
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
    <div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-labelledby="modalEditarPacienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPacienteLabel">Editar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarNombrePaciente" name="nombre"
                                placeholder="Nombres del paciente" required>
                            <label for="editarNombrePaciente">Nombres</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text class="form-control" id="editarApellidoPaciente" name="apellido"
                                placeholder="Apellidos del paciente" required>
                            <label for="editarApellidoPaciente">Apellidos</label>
                            @error('apellido')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarCedulaPaciente" name="cedula"
                                placeholder="Cédula del paciente" required>
                            <label for="editarCedulaPaciente">Cédula</label>
                            @error('cedula')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="editarFechaNacimientoPaciente"
                                name="fecha_nacimiento" placeholder="Fecha de nacimiento" required>
                            <label for="editarFechaNacimientoPaciente">Fecha de Nacimiento</label>
                            @error('fecha_nacimiento')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarTelefonoPaciente" name="telefono"
                                placeholder="Teléfono del paciente" required>
                            <label for="editarTelefonoPaciente">Teléfono</label>
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarDireccionPaciente" name="direccion"
                                placeholder="Dirección del paciente" required>
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

    <div class="modal fade" id="modalShowPaciente" tabindex="-1" aria-labelledby="modalShowPacienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowPacienteLabel">Datos del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente">Nombres</label>
                        <p class="form-control" id="mostrarNombrePaciente"></p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente">Apellidos</label>
                        <p class="form-control" id="mostrarApellidoPaciente"></p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente" >Cédula</label>
                        <p class="form-control" id="mostrarCedulaPaciente"></p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente" >Fecha de Nacimiento</label>
                        <p class="form-control" id="mostrarFechaNacimientoPaciente"></p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente" >Teléfono</label>
                        <p class="form-control" id="mostrarTelefonoPaciente"></p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente">Estado</label>
                        <p class="form-control"></p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente">Municipio</label>
                        <p class="form-control"></p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente">Parroquia</label>
                        <p class="form-control"></p>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <label for="editarNombrePaciente">Dirección</label>
                        <p class="form-control" id="mostrarDireccionPaciente"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', async function(event) {
            const btn = event.target.closest('.btn-edit');
            const btnShow = event.target.closest('.btn-show');

            if (btn) {
                const pacienteId = btn.getAttribute('data-id');
                var inputNombre = document.getElementById('editarNombrePaciente');
                var inputApellido = document.getElementById('editarApellidoPaciente');
                var inputCedula = document.getElementById('editarCedulaPaciente');
                var inputFechaNacimiento = document.getElementById('editarFechaNacimientoPaciente');
                var inputTelefono = document.getElementById('editarTelefonoPaciente');
                var inputDireccion = document.getElementById('editarDireccionPaciente');

                try {
                    const modalElement = document.getElementById('modalEditarPaciente');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }

                    inputNombre.disabled = true;
                    inputNombre.value = "Cargando...";
                    inputApellido.disabled = true;
                    inputApellido.value = "Cargando...";
                    inputCedula.disabled = true;
                    inputCedula.value = "Cargando...";
                    inputFechaNacimiento.disabled = true;
                    inputFechaNacimiento.value = "Cargando...";
                    inputTelefono.disabled = true;
                    inputTelefono.value = "Cargando...";
                    inputDireccion.disabled = true;
                    inputDireccion.value = "Cargando...";

                    modalInstance.show();
                    const response = await fetch(`/paciente/${pacienteId}/edit`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Error al obtener datos');

                    const data = await response.json();


                    document.getElementById('id').value = data.id;
                    inputNombre.value = data.nombre;
                    inputNombre.disabled = false;
                    inputApellido.disabled = false;
                    inputApellido.value = data.apellido
                    inputCedula.disabled = false;
                    inputCedula.value = data.cedula
                    inputFechaNacimiento.disabled = false;
                    inputFechaNacimiento.value = data.fecha_nacimiento
                    inputTelefono.disabled = false;
                    inputTelefono.value = data.telefono
                    inputDireccion.disabled = false;
                    inputDireccion.value = data.direccion


                    const form = document.querySelector('#modalEditarPaciente form');
                    form.action = `/pacientes/${data.id}`;


                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del paciente', 'error');
                }
            }

            if (btnShow) {
                const pacienteId = btnShow.getAttribute('data-id');
                var inputNombre = document.getElementById('mostrarNombrePaciente');
                var inputApellido = document.getElementById('mostrarApellidoPaciente');
                var inputCedula = document.getElementById('mostrarCedulaPaciente');
                var inputFechaNacimiento = document.getElementById('mostrarFechaNacimientoPaciente');
                var inputTelefono = document.getElementById('mostrarTelefonoPaciente');
                var inputDireccion = document.getElementById('mostrarDireccionPaciente');



                try {
                    const modalElement = document.getElementById('modalShowPaciente');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }

                    inputNombre.innerHTML = "Cargando...";
                    inputApellido.innerHTML = "Cargando...";
                    inputCedula.innerHTML = "Cargando...";
                    inputFechaNacimiento.innerHTML = "Cargando...";
                    inputTelefono.innerHTML = "Cargando...";
                    inputDireccion.innerHTML = "Cargando...";

                    modalInstance.show();
                    const response = await fetch(`/paciente/${pacienteId}/show`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Error al obtener datos');

                    const data = await response.json();



                    inputNombre.innerHTML = data.nombre;
                    inputApellido.innerHTML = data.apellido;
                    inputCedula.innerHTML = data.cedula;
                    inputFechaNacimiento.innerHTML = data.fecha_nacimiento;
                    inputTelefono.innerHTML = data.telefono;
                    inputDireccion.innerHTML = data.direccion;

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del paciente', 'error');
                }
            }


        });
    </script>

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
