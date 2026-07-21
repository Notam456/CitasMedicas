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
                                        id="cedulaMedico" placeholder="Cédula" required pattern="[0-9]+" maxlength="20" title="Solo números">
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
                                        id="nombreMedico" placeholder="Nombre" required
                                        pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                                    <label for="nombreMedico">Nombres</label>
                                </div>
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="apellido" value="{{ old('apellido') }}"
                                        class="form-control" id="apellidoMedico" placeholder="Apellido" required
                                        pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                                    <label for="apellidoMedico">Apellidos</label>
                                </div>
                                @error('apellido')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3 mt-3">
                                <div class="form-floating">
                                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                                        class="form-control" id="telefonoMedico" placeholder="Teléfono" required
                                        pattern="[\d\-\(\)\s\+]+" title="Solo se permiten números, guiones, paréntesis y espacios">
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

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold small text-muted text mb-2">Horario de atención (días de la semana y horas)</label>
                                <div class="row g-3">
                                    @php $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo']; @endphp
                                    @foreach($dias as $val => $nom)
                                        @php $isChecked = old("horarios.$val.checked") ? true : false; @endphp
                                        <div class="col-md-6 col-12">
                                            <div class="card border border-light-subtle shadow-xs h-100 day-card p-3" style="transition: all 0.2s; border-left: 4px solid {{ $isChecked ? '#0d6efd' : '#dee2e6' }} !important; background-color: #fafbfc;">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-check form-switch me-3 mb-0">
                                                            <input class="form-check-input check-dia-reg" type="checkbox" name="horarios[{{ $val }}][checked]" value="1" id="dia_reg_{{ $val }}" {{ $isChecked ? 'checked' : '' }}>
                                                        </div>
                                                        <label class="form-check-label fw-bold mb-0 text-dark" for="dia_reg_{{ $val }}">
                                                            <i class="bi bi-calendar3 me-1 text-primary opacity-75"></i> {{ $nom }}
                                                        </label>
                                                    </div>
                                                    
                                                </div>
                                                <div class="row g-2 time-inputs-reg mt-1" style="{{ $isChecked ? 'display: flex;' : 'display: none;' }}">
                                                    <div class="col-6">
                                                        <label class="small text-muted mb-1" style="font-size: 0.75rem;"><i class="bi bi-clock me-1 text-primary"></i> Entrada</label>
                                                        <input type="time" name="horarios[{{ $val }}][hora_entrada]" class="form-control form-control-sm" value="{{ old("horarios.$val.hora_entrada") }}" {{ $isChecked ? '' : 'disabled' }}>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="small text-muted mb-1" style="font-size: 0.75rem;"><i class="bi bi-clock-fill me-1 text-danger"></i> Salida</label>
                                                        <input type="time" name="horarios[{{ $val }}][hora_salida]" class="form-control form-control-sm" value="{{ old("horarios.$val.hora_salida") }}" {{ $isChecked ? '' : 'disabled' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted mt-3 d-block">Si no selecciona ningún día, el médico podrá atender cualquier día sin restricciones de horario.</small>
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
                                        placeholder="Cédula" required pattern="[0-9]+" maxlength="20" title="Solo números">
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
                                        placeholder="Nombre" required
                                        pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                                    <label for="editarNombreMedico">Nombres</label>
                                </div>
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" name="apellido" class="form-control" id="editarApellidoMedico"
                                        placeholder="Apellido" required
                                        pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                                    <label for="editarApellidoMedico">Apellidos</label>
                                </div>
                                @error('apellido')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3 mt-3">
                                <div class="form-floating">
                                    <input type="text" name="telefono" class="form-control" id="editarTelefonoMedico"
                                        placeholder="Teléfono" required
                                        pattern="[\d\-\(\)\s\+]+" title="Solo se permiten números, guiones, paréntesis y espacios">
                                    <label for="editarTelefonoMedico">Teléfono</label>
                                </div>
                                @error('telefono')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editarEspecialidadMedico" class="form-label mb-1">Especialidad</label>
                                <select name="especialidad_id" id="editarEspecialidadMedico" class="form-select "
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

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold small text-muted text mb-2">Horario de atención (días de la semana y horas)</label>
                                <div class="row g-3">
                                    @foreach($dias as $val => $nom)
                                        <div class="col-md-6 col-12">
                                            <div class="card border border-light-subtle shadow-xs h-100 day-card p-3" style="transition: all 0.2s; border-left: 4px solid #dee2e6 !important; background-color: #fafbfc;">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-check form-switch me-3 mb-0">
                                                            <input class="form-check-input check-dia-edit" type="checkbox" name="horarios[{{ $val }}][checked]" value="1" id="dia_edit_{{ $val }}">
                                                        </div>
                                                        <label class="form-check-label fw-bold mb-0 text-dark" for="dia_edit_{{ $val }}">
                                                            <i class="bi bi-calendar3 me-1 text-primary opacity-75"></i> {{ $nom }}
                                                        </label>
                                                    </div>
                                                   
                                                </div>
                                                <div class="row g-2 time-inputs-edit mt-1" style="display: none;">
                                                    <div class="col-6">
                                                        <label class="small text-muted mb-1" style="font-size: 0.75rem;"><i class="bi bi-clock me-1 text-primary"></i> Entrada</label>
                                                        <input type="time" name="horarios[{{ $val }}][hora_entrada]" class="form-control form-control-sm" disabled>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="small text-muted mb-1" style="font-size: 0.75rem;"><i class="bi bi-clock-fill me-1 text-danger"></i> Salida</label>
                                                        <input type="time" name="horarios[{{ $val }}][hora_salida]" class="form-control form-control-sm" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted mt-3 d-block">Si no selecciona ningún día, el médico podrá atender cualquier día sin restricciones de horario.</small>
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
                            <label class="fw-bold">Cédula</label>
                            <p class="form-control" id="mostrarCedulaMedico"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nombres</label>
                            <p class="form-control" id="mostrarNombreMedico"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Apellidos</label>
                            <p class="form-control" id="mostrarApellidoMedico"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Teléfono</label>
                            <p class="form-control" id="mostrarTelefonoMedico"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Especialidad</label>
                            <p class="form-control" id="mostrarEspecialidadMedico"></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="fw-bold">Horario de Atención</label>
                            <div id="mostrarHorarioMedico" class="border rounded p-3 bg-white" style="max-height: 250px; overflow-y: auto;"></div>
                        </div>
                    </div>
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

            // Limpiar y marcar horarios
            document.querySelectorAll('.check-dia-edit').forEach(check => {
                check.checked = false;
                const parent = check.closest('.day-card');
                parent.style.borderLeftColor = '#dee2e6';

                const timeInputsDiv = parent.querySelector('.time-inputs-edit');
                timeInputsDiv.style.display = 'none';
                const inputs = timeInputsDiv.querySelectorAll('input[type="time"]');
                inputs.forEach(input => {
                    input.disabled = true;
                    input.required = false;
                    input.value = '';
                });
            });

            if (data.horarios) {
                data.horarios.forEach(h => {
                    const check = document.getElementById(`dia_edit_${h.dia_semana}`);
                    if (check) {
                        check.checked = true;
                        const parent = check.closest('.day-card');
                        parent.style.borderLeftColor = '#0d6efd';
            

                        const timeInputsDiv = parent.querySelector('.time-inputs-edit');
                        timeInputsDiv.style.display = 'flex';
                        const inputs = timeInputsDiv.querySelectorAll('input[type="time"]');
                        inputs.forEach(input => {
                            input.disabled = false;
                            input.required = true;
                        });
                        const entInput = parent.querySelector(`input[name="horarios[${h.dia_semana}][hora_entrada]"]`);
                        const salInput = parent.querySelector(`input[name="horarios[${h.dia_semana}][hora_salida]"]`);
                        if (entInput) entInput.value = h.hora_entrada.substring(0, 5);
                        if (salInput) salInput.value = h.hora_salida.substring(0, 5);
                    }
                });
            }

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
            const response = await fetch(`/medicos/${medicoId}`, {
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

            // Mostrar horario
            const container = document.getElementById('mostrarHorarioMedico');
            container.innerHTML = '';
            if (data.horarios && data.horarios.length > 0) {
                const diasNombre = {1: 'Lunes', 2: 'Martes', 3: 'Miércoles', 4: 'Jueves', 5: 'Viernes', 6: 'Sábado', 7: 'Domingo'};
                data.horarios.sort((a, b) => a.dia_semana - b.dia_semana);
                data.horarios.forEach(h => {
                    const ent = h.hora_entrada.substring(0, 5);
                    const sal = h.hora_salida.substring(0, 5);
                    container.innerHTML += `<div class="d-flex justify-content-between border-bottom py-1">
                        <span><strong>${diasNombre[h.dia_semana]}:</strong></span>
                        <span>${ent} a ${sal}</span>
                    </div>`;
                });
            } else {
                container.innerHTML = '<span class="text-muted">Sin horario restringido (Disponible todos los días)</span>';
            }

        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos del médico', 'error');
        }
    }
});

// Manejadores de eventos de cambio de checkbox para mostrar/ocultar y habilitar/deshabilitar los campos de tiempo
document.addEventListener('change', function(event) {
    if (event.target.classList.contains('check-dia-reg')) {
        const checkbox = event.target;
        const parent = checkbox.closest('.day-card');
    
        const timeInputsDiv = parent.querySelector('.time-inputs-reg');
        const inputs = timeInputsDiv.querySelectorAll('input[type="time"]');
        if (checkbox.checked) {
            parent.style.borderLeftColor = '#0d6efd';
           
            timeInputsDiv.style.display = 'flex';
            inputs.forEach(input => {
                input.disabled = false;
                input.required = true;
            });
        } else {
            parent.style.borderLeftColor = '#dee2e6';
            timeInputsDiv.style.display = 'none';
            inputs.forEach(input => {
                input.disabled = true;
                input.required = false;
                input.value = '';
            });
        }
    }

    if (event.target.classList.contains('check-dia-edit')) {
        const checkbox = event.target;
        const parent = checkbox.closest('.day-card');
        
        const timeInputsDiv = parent.querySelector('.time-inputs-edit');
        const inputs = timeInputsDiv.querySelectorAll('input[type="time"]');
        if (checkbox.checked) {
            parent.style.borderLeftColor = '#0d6efd';
            timeInputsDiv.style.display = 'flex';
            inputs.forEach(input => {
                input.disabled = false;
                input.required = true;
            });
        } else {
            parent.style.borderLeftColor = '#dee2e6';
    
            timeInputsDiv.style.display = 'none';
            inputs.forEach(input => {
                input.disabled = true;
                input.required = false;
                input.value = '';
            });
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
