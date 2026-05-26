@extends('layouts.template')

@section('title', 'Lista de Médicos | SAGECIM')

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

        <table class="table table-hover" id="tablaMedicos">
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
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Registrar Médico (sin cambios estructurales, solo se mantiene) -->
    <div class="modal fade" id="modalMedico" tabindex="-1" aria-labelledby="modalMedicoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMedicoLabel">Registrar Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('medicos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="cedula" value="{{ old('cedula') }}" class="form-control"
                                        id="cedulaMedico" placeholder="Cédula" required>
                                    <label for="cedulaMedico">Cédula</label>
                                </div>
                                @error('cedula')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3 d-none d-md-block"></div>

                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control"
                                        id="nombreMedico" placeholder="Nombre" required>
                                    <label for="nombreMedico">Nombres</label>
                                </div>
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="apellido" value="{{ old('apellido') }}"
                                        class="form-control" id="apellidoMedico" placeholder="Apellido" required>
                                    <label for="apellidoMedico">Apellidos</label>
                                </div>
                                @error('apellido')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                                        class="form-control" id="telefonoMedico" placeholder="Teléfono" required>
                                    <label for="telefonoMedico">Teléfono</label>
                                </div>
                                @error('telefono')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="especialidad_id" class="form-label mb-1">Especialidad</label>
                                <select name="especialidad_id" id="especialidad_id" class="form-select"
                                    style="padding: 0.58rem 0.75rem;" required>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Médico (sin cambios estructurales) -->
    <div class="modal fade" id="modalEditarMedico" tabindex="-1" aria-labelledby="modalEditarMedicoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="cedula" class="form-control" id="editarCedulaMedico"
                                        placeholder="Cédula" required>
                                    <label for="editarCedulaMedico">Cédula</label>
                                </div>
                                @error('cedula')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3 d-none d-md-block"></div>

                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="nombre" class="form-control" id="editarNombreMedico"
                                        placeholder="Nombre" required>
                                    <label for="editarNombreMedico">Nombres</label>
                                </div>
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="apellido" class="form-control" id="editarApellidoMedico"
                                        placeholder="Apellido" required>
                                    <label for="editarApellidoMedico">Apellidos</label>
                                </div>
                                @error('apellido')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="telefono" class="form-control" id="editarTelefonoMedico"
                                        placeholder="Teléfono" required>
                                    <label for="editarTelefonoMedico">Teléfono</label>
                                </div>
                                @error('telefono')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editarEspecialidadMedico" class="form-label mb-1">Especialidad</label>
                                <select name="especialidad_id" id="editarEspecialidadMedico" class="form-select"
                                    style="padding: 0.58rem 0.75rem;" required>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mostrar Médico (sin cambios estructurales) -->
    <div class="modal fade" id="modalShowMedico" tabindex="-1" aria-labelledby="modalShowMedicoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowMedicoLabel">Datos del Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cédula</label>
                            <p class="form-control-plaintext" id="mostrarCedulaMedico"></p>
                        </div>
                        <div class="col-md-6 mb-3 d-none d-md-block"></div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombres</label>
                            <p class="form-control-plaintext" id="mostrarNombreMedico"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Apellidos</label>
                            <p class="form-control-plaintext" id="mostrarApellidoMedico"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <p class="form-control-plaintext" id="mostrarTelefonoMedico"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Especialidad</label>
                            <p class="form-control-plaintext" id="mostrarEspecialidadMedico"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
@endsection

@push('scripts')
<link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('#tablaMedicos').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("medicos.index") }}',
        columns: [
            { data: 0, name: 'nombre' },
            { data: 1, name: 'apellido' },
            { data: 2, name: 'cedula' },
            { data: 3, name: 'telefono' },
            { data: 4, name: 'especialidad' },
            { data: 5, name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[0, 'asc']]
    });
});

// Eventos para editar/mostrar (igual que antes, pero ahora usando delegación de eventos en #tablaMedicos)
document.addEventListener('click', async function(event) {
    const btnEdit = event.target.closest('.btn-edit');
    const btnShow = event.target.closest('.btn-show');

    if (btnEdit) {
        const medicoId = btnEdit.getAttribute('data-id');
        const inputNombre = document.getElementById('editarNombreMedico');
        const inputApellidos = document.getElementById('editarApellidoMedico');
        const inputCedula = document.getElementById('editarCedulaMedico');
        const inputTelefono = document.getElementById('editarTelefonoMedico');
        const inputEspecialidad = document.getElementById('editarEspecialidadMedico');

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
            Swal.fire('Error', 'No se pudieron cargar los datos del médico', 'error');
        }
    }

    if (btnShow) {
        const medicoId = btnShow.getAttribute('data-id');
        const inputNombre = document.getElementById('mostrarNombreMedico');
        const inputApellidos = document.getElementById('mostrarApellidoMedico');
        const inputCedula = document.getElementById('mostrarCedulaMedico');
        const inputTelefono = document.getElementById('mostrarTelefonoMedico');
        const inputEspecialidad = document.getElementById('mostrarEspecialidadMedico');

        try {
            const modalElement = document.getElementById('modalShowMedico');
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }

            inputNombre.innerHTML = "Cargando...";
            inputApellidos.innerHTML = "Cargando...";
            inputCedula.innerHTML = "Cargando...";
            inputTelefono.innerHTML = "Cargando...";
            inputEspecialidad.innerHTML = "Cargando...";

            modalInstance.show();
            const response = await fetch(`/medicos/${medicoId}/show`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Error al obtener datos');

            const data = await response.json();

            inputNombre.innerHTML = data.nombre;
            inputApellidos.innerHTML = data.apellido;
            inputCedula.innerHTML = data.cedula;
            inputTelefono.innerHTML = data.telefono;
            inputEspecialidad.innerHTML = data.especialidad.nombre;

        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos del médico', 'error');
        }
    }
});

// Mostrar modal de registro si hay errores de validación
@if ($errors->any() && !isset($medicoToEdit) && !isset($medicoToshow))
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalMedico');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
@endif
</script>
@endpush
