@extends('layouts.template')
@section('title', 'Lista de Pacientes | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Pacientes</h3>
        </div>

        <table class="table table-hover" id="tablaPacientes">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Cédula</th>
                    <th>RIF</th>
                    <th>Dirección</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Editar Paciente (sin cambios estructurales) -->
    <div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-labelledby="modalEditarPacienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPacienteLabel">Editar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="" method="POST" id="formEditarPaciente">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="editarCedulaPaciente" name="cedula"
                                        placeholder="Cédula del paciente" required>
                                    <label for="editarCedulaPaciente">Cédula</label>
                                </div>
                                @error('cedula')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="editarRifPaciente" name="rif"
                                        placeholder="RIF del paciente">
                                    <label for="editarRifPaciente">RIF</label>
                                </div>
                                @error('rif')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="editarNombrePaciente" name="nombre"
                                        placeholder="Nombres del paciente" required>
                                    <label for="editarNombrePaciente">Nombres</label>
                                </div>
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="editarApellidoPaciente" name="apellido"
                                        placeholder="Apellidos del paciente" required>
                                    <label for="editarApellidoPaciente">Apellidos</label>
                                </div>
                                @error('apellido')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="editarFechaNacimientoPaciente"
                                        name="fecha_nacimiento" placeholder="Fecha de nacimiento" required>
                                    <label for="editarFechaNacimientoPaciente">Fecha de Nacimiento</label>
                                </div>
                                @error('fecha_nacimiento')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="editarTelefonoPaciente" name="telefono"
                                        placeholder="Teléfono del paciente" required>
                                    <label for="editarTelefonoPaciente">Teléfono</label>
                                </div>
                                @error('telefono')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12">
                                <h6 class="text-secondary border-bottom pb-2 mt-2">Ubicación del Paciente</h6>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted small fw-bold">Estado</label>
                                <select id="select-estado-edit" class="form-select">
                                    <option value="">Seleccione Estado</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted small fw-bold">Municipio</label>
                                <select id="select-municipio-edit" class="form-select">
                                    <option value="">Seleccione Municipio</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted small fw-bold">Parroquia</label>
                                <select name="parroquia_id" id="select-parroquia-edit"
                                    class="form-select @error('parroquia_id') is-invalid @enderror" required>
                                    <option value="">Seleccione Parroquia</option>
                                </select>
                                @error('parroquia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="editarDireccionPaciente"
                                        name="direccion" placeholder="Dirección del paciente" required>
                                    <label for="editarDireccionPaciente">Dirección exacta</label>
                                </div>
                                @error('direccion')
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

    <div class="modal fade" id="modalShowPaciente" tabindex="-1" aria-labelledby="modalShowPacienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowPacienteLabel">Datos del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Cédula</label>
                            <p class="form-control" id="mostrarCedulaPaciente"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">RIF</label>
                            <p class="form-control" id="mostrarRifPaciente"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nombres</label>
                            <p class="form-control" id="mostrarNombrePaciente"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Apellidos</label>
                            <p class="form-control" id="mostrarApellidoPaciente"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Fecha de Nacimiento</label>
                            <p class="form-control" id="mostrarFechaNacimientoPaciente"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Teléfono</label>
                            <p class="form-control" id="mostrarTelefonoPaciente"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Estado</label>
                            <p class="form-control" id="mostrarEstadoPaciente"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Municipio</label>
                            <p class="form-control" id="mostrarMunicipioPaciente"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Parroquia</label>
                            <p class="form-control" id="mostrarParroquiaPaciente"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Dirección</label>
                            <p class="form-control" id="mostrarDireccionPaciente"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    $('#tablaPacientes').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("paciente.index") }}',
            columns: [
                { data: 0, name: 'nombre' },
                { data: 1, name: 'apellido' },
                { data: 2, name: 'cedula' },
                { data: 3, name: 'rif' },
                { data: 4, name: 'direccion' },
                { data: 5, name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
        language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[0, 'asc']]
    });
});
</script>

<!-- Mantener todo el JavaScript original para modales, edición, visualización y búsqueda de cédula -->
<script>
document.addEventListener('click', async function(event) {
    const btn = event.target.closest('.btn-edit');
    const btnShow = event.target.closest('.btn-show');

    if (btn) {
        const pacienteId = btn.getAttribute('data-id');
        var inputRif = document.getElementById('editarRifPaciente');
        var inputNombre = document.getElementById('editarNombrePaciente');
        var inputApellido = document.getElementById('editarApellidoPaciente');
        var inputCedula = document.getElementById('editarCedulaPaciente');
        var inputFechaNacimiento = document.getElementById('editarFechaNacimientoPaciente');
        var inputTelefono = document.getElementById('editarTelefonoPaciente');
        var inputDireccion = document.getElementById('editarDireccionPaciente');
       
        var selectEstado = document.getElementById('select-estado-edit');
        var selectMunicipio = document.getElementById('select-municipio-edit');
        var selectParroquia = document.getElementById('select-parroquia-edit');

        try {
            const modalElement = document.getElementById('modalEditarPaciente');
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }

            inputRif.disabled = true;
            inputRif.value = "Cargando...";
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

            selectEstado.disabled = true;
            selectEstado.innerHTML = '<option value="">Cargando Estados...</option>';
            selectMunicipio.disabled = true;
            selectMunicipio.innerHTML = '<option value="">Cargando...</option>';
            selectParroquia.disabled = true;
            selectParroquia.innerHTML = '<option value="">Cargando...</option>';

            modalInstance.show();

            try {
                const resEstados = await fetch('/api/estados');
                if (resEstados.ok) {
                    const estados = await resEstados.json();
                    selectEstado.innerHTML = '<option value="">Seleccione Estado</option>';
                    estados.forEach(e => {
                        selectEstado.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
                    });
                }
            } catch (errEst) {
                console.error("Error al obtener el catálogo de estados:", errEst);
            }

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
            inputRif.disabled = false;
            inputRif.value = data.rif || '';
            inputNombre.value = data.nombre;
            inputNombre.disabled = false;
            inputApellido.disabled = false;
            inputApellido.value = data.apellido;
            inputCedula.disabled = false;
            inputCedula.value = data.cedula;
            inputFechaNacimiento.disabled = false;
            inputFechaNacimiento.value = data.fecha_nacimiento;
            inputTelefono.disabled = false;
            inputTelefono.value = data.telefono;
            inputDireccion.disabled = false;
            inputDireccion.value = data.direccion;

            selectEstado.disabled = false;

            if (data.parroquia && data.parroquia.municipio) {
                const parroquiaId = data.parroquia.id;
                const municipioId = data.parroquia.municipio.id;
                const estadoId = data.parroquia.municipio.estado_id || data.parroquia.municipio.estado?.id;

                selectEstado.value = estadoId;

                const resMunicipios = await fetch(`/api/municipios/${estadoId}`);
                if (resMunicipios.ok) {
                    const municipios = await resMunicipios.json();
                    selectMunicipio.innerHTML = '<option value="">Seleccione Municipio</option>';
                    municipios.forEach(m => {
                        let selected = m.id == municipioId ? 'selected' : '';
                        selectMunicipio.innerHTML += `<option value="${m.id}" ${selected}>${m.nombre}</option>`;
                    });
                    selectMunicipio.disabled = false;
                }

                const resParroquias = await fetch(`/api/parroquias/${municipioId}`);
                if (resParroquias.ok) {
                    const parroquias = await resParroquias.json();
                    selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';
                    parroquias.forEach(p => {
                        let selected = p.id == parroquiaId ? 'selected' : '';
                        selectParroquia.innerHTML += `<option value="${p.id}" ${selected}>${p.nombre}</option>`;
                    });
                    selectParroquia.disabled = false;
                }
            } else {
                selectMunicipio.innerHTML = '<option value="">Seleccione Municipio</option>';
                selectMunicipio.disabled = false;
                selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';
                selectParroquia.disabled = false;
            }

            const form = document.querySelector('#modalEditarPaciente form');
            form.action = `/paciente/${data.id}`;

        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos del paciente', 'error');
        }
    }

    if (btnShow) {
        const pacienteId = btnShow.getAttribute('data-id');
        var inputRif = document.getElementById('mostrarRifPaciente');
        var inputNombre = document.getElementById('mostrarNombrePaciente');
        var inputApellido = document.getElementById('mostrarApellidoPaciente');
        var inputCedula = document.getElementById('mostrarCedulaPaciente');
        var inputFechaNacimiento = document.getElementById('mostrarFechaNacimientoPaciente');
        var inputTelefono = document.getElementById('mostrarTelefonoPaciente');
        var inputDireccion = document.getElementById('mostrarDireccionPaciente');
        var inputEstado = document.getElementById('mostrarEstadoPaciente');
        var inputMunicipio = document.getElementById('mostrarMunicipioPaciente');
        var inputParroquia = document.getElementById('mostrarParroquiaPaciente');

        try {
            const modalElement = document.getElementById('modalShowPaciente');
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }

            inputRif.innerHTML = "Cargando...";
            inputNombre.innerHTML = "Cargando...";
            inputApellido.innerHTML = "Cargando...";
            inputCedula.innerHTML = "Cargando...";
            inputFechaNacimiento.innerHTML = "Cargando...";
            inputTelefono.innerHTML = "Cargando...";
            inputDireccion.innerHTML = "Cargando...";
            inputEstado.innerHTML = "Cargando...";
            inputMunicipio.innerHTML = "Cargando...";
            inputParroquia.innerHTML = "Cargando...";

            modalInstance.show();
            const response = await fetch(`/paciente/${pacienteId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Error al obtener datos');

            const data = await response.json();

            inputRif.innerHTML = data.rif || '';
            inputNombre.innerHTML = data.nombre;
            inputApellido.innerHTML = data.apellido;
            inputCedula.innerHTML = data.cedula;
            inputFechaNacimiento.innerHTML = data.fecha_nacimiento;
            inputTelefono.innerHTML = data.telefono;
            inputDireccion.innerHTML = data.direccion;

            inputParroquia.innerHTML = data.parroquia ? data.parroquia.nombre : 'N/A';
            inputMunicipio.innerHTML = data.parroquia?.municipio ? data.parroquia.municipio.nombre : 'N/A';
            inputEstado.innerHTML = data.parroquia?.municipio?.estado ? data.parroquia.municipio.estado.nombre : 'N/A';

        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos del paciente', 'error');
        }
    }
});

document.getElementById('select-estado-edit').addEventListener('change', function() {
    let estadoId = this.value;
    let selectMunicipio = document.getElementById('select-municipio-edit');
    let selectParroquia = document.getElementById('select-parroquia-edit');

    selectMunicipio.innerHTML = '<option value="">Cargando...</option>';
    selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';

    if (estadoId) {
        fetch(`/api/municipios/${estadoId}`)
            .then(res => res.json())
            .then(data => {
                selectMunicipio.innerHTML = '<option value="">Seleccione Municipio</option>';
                data.forEach(m => {
                    selectMunicipio.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
                });
            })
            .catch(err => console.error(err));
    }
});

document.getElementById('select-municipio-edit').addEventListener('change', function() {
    let municipioId = this.value;
    let selectParroquia = document.getElementById('select-parroquia-edit');

    selectParroquia.innerHTML = '<option value="">Cargando...</option>';

    if (municipioId) {
        fetch(`/api/parroquias/${municipioId}`)
            .then(res => res.json())
            .then(data => {
                selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';
                data.forEach(p => {
                    selectParroquia.innerHTML += `<option value="${p.id}">${p.nombre}</option>`;
                });
            })
            .catch(err => console.error(err));
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
            title: '¡Ups! Algo salió mal en tu acción, inténtalo de nuevo',
            text: errorMessages,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Entendido'
        });
    });
</script>
@endif
@endpush