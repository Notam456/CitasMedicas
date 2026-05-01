@extends('layouts.template')

@section('title', 'Dashboard | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Médicos</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMedico">
                <i class="bi bi-person-plus me-1"></i> Registrar Médico
            </button>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Especialidad</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicos as $medico)
                    <tr>
                        <td>{{ $medico->nombre }}</td>
                        <td>{{ $medico->apellido }}</td>
                        <td>{{ $medico->cedula }}</td>
                        <td>{{ $medico->telefono }}</td>
                        <td>{{ $medico->especialidad->nombre ?? 'N/A' }}</td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" data-id="{{ $medico->id }}"
                                    class=" btn-show btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" data-id="{{ $medico->id }}"
                                    class=" btn-edit btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('medicos.destroy', $medico->id) }}"
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

    <!-- Modal Registrar Médico -->
    <div class="modal fade" id="modalMedico" tabindex="-1" aria-labelledby="modalMedicoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMedicoLabel">Registrar Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('medicos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control"
                                id="nombreMedico" placeholder="Nombre" required>
                            <label for="nombreMedico">Nombres</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="apellido" value="{{ old('apellido') }}" class="form-control"
                                id="apellidoMedico" placeholder="Apellido" required>
                            <label for="apellidosMedico">Apellidos</label>
                            @error('apellidos')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="cedula" value="{{ old('cedula') }}" class="form-control"
                                id="cedulaMedico" placeholder="Cédula" required>
                            <label for="cedulaMedico">Cédula</label>
                            @error('cedula')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control"
                                id="telefonoMedico" placeholder="Teléfono" required>
                            <label for="telefonoMedico">Teléfono</label>
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="especialidad_id" class="form-label">Especialidad</label>
                            <select name="especialidad_id" id="especialidad_id" class="form-select" required>
                                <option value="">Seleccione una especialidad</option>
                                @foreach ($especialidades as $especialidad)
                                    <option value="{{ $especialidad->id }}"
                                        {{ old('especialidad_id') == $especialidad->id ? 'selected' : '' }}>
                                        {{ $especialidad->nombre }}</option>
                                @endforeach
                            </select>
                            @error('especialidad_id')
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

    <!-- Modal Editar Médico -->
    <div class="modal fade" id="modalEditarMedico" tabindex="-1" aria-labelledby="modalEditarMedicoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarMedicoLabel">Editar Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="id">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre" class="form-control" id="editarNombreMedico"
                                placeholder="Nombre" required>
                            <label for="editarNombreMedico">Nombres</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="apellido" class="form-control" id="editarApellidoMedico"
                                placeholder="Apellido" required>
                            <label for="editarApellidoMedico">Apellidos</label>
                            @error('apellido')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="cedula" class="form-control" id="editarCedulaMedico"
                                placeholder="Cédula" required>
                            <label for="editarCedulaMedico">Cédula</label>
                            @error('cedula')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="telefono" class="form-control" id="editarTelefonoMedico"
                                placeholder="Teléfono" required>
                            <label for="editarTelefonoMedico">Teléfono</label>
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="editarEspecialidadMedico" class="form-label">Especialidad</label>
                            <select name="especialidad_id" id="editarEspecialidadMedico" class="form-select" required>
                                <option value="">Seleccione una especialidad</option>
                                @foreach ($especialidades as $especialidad)
                                    <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                                @endforeach
                            </select>
                            @error('especialidad_id')
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

    <!-- Modal Mostrar Médico -->
    <div class="modal fade" id="modalShowMedico" tabindex="-1" aria-labelledby="modalShowMedicoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowMedicoLabel">Datos del Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombres</label>
                        <p class="form-control">{{ $medicoToshow->nombre ?? '' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos</label>
                        <p class="form-control">{{ $medicoToshow->apellido ?? '' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cédula</label>
                        <p class="form-control">{{ $medicoToshow->cedula ?? '' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <p class="form-control">{{ $medicoToshow->telefono ?? '' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Especialidad</label>
                        <p class="form-control">{{ $medicoToshow->especialidad->nombre ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', async function(event) {
            const btn = event.target.closest('.btn-edit');
            const btnShow = event.target.closest('.btn-show');

            if (btn) {
                const medicoId = btn.getAttribute('data-id');
                var inputNombre = document.getElementById('editarNombreMedico');
                var inputApellidos = document.getElementById('editarApellidoMedico');
                var inputCedula = document.getElementById('editarCedulaMedico');
                var inputTelefono = document.getElementById('editarTelefonoMedico');
                var inputEspecialidad = document.getElementById('editarEspecialidadMedico');

                try {
                    const modalElement = document.getElementById('modalEditarMedico');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }

                    inputNombre.disabled = true;
                    inputNombre.value = "Cargando...";
                    inputApellidos.disabled = true;
                    inputApellidos.value = "Cargando...";
                    inputCedula.disabled = true;
                    inputCedula.value = "Cargando...";
                    inputTelefono.disabled = true;
                    inputTelefono.value = "Cargando...";
                    inputEspecialidad.disabled = true;
                    inputEspecialidad.value = "Cargando...";

                    modalInstance.show();
                    const response = await fetch(`/medicos/${medicoId}/edit`, {
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
                    inputApellidos.disabled = false;
                    inputApellidos.value = data.apellido;
                    inputCedula.disabled = false;
                    inputCedula.value = data.cedula;
                    inputTelefono.disabled = false;
                    inputTelefono.value = data.telefono;
                    inputEspecialidad.disabled = false;
                    inputEspecialidad.value = data.especialidad_id;


                    const form = document.querySelector('#modalEditarMedico form');
                    form.action = `/medicos/${data.id}`;


                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del estado', 'error');
                }
            }

            if (btnShow) {
                const estadoId = btnShow.getAttribute('data-id');
                var inputNombre = document.getElementById('mostrarEstadoNombre');


                try {
                    const modalElement = document.getElementById('modalShowEstado');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }

                    inputNombre.innerHTML = "Cargando...";

                    modalInstance.show();
                    const response = await fetch(`/estados/${estadoId}/show`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Error al obtener datos');

                    const data = await response.json();



                    inputNombre.innerHTML = data.nombre;

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del estado', 'error');
                }
            }


        });
    </script>

    @if ($errors->any() && !isset($medicoToEdit) && !isset($medicoToshow))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalEl = document.getElementById('modalMedico');
                if (modalEl) {
                    var modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            });
        </script>
    @endif

    @include('layouts.footer')
@endsection
