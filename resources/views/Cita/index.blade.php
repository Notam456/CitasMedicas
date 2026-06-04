@extends('layouts.template')
@section('title', 'Citas Agendadas | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Citas Agendadas</h3>
            <div class="d-flex align-items-center gap-2">
                <label for="filtro-fecha" class="mb-0">Filtrar por fecha:</label>
                <input type="date" id="filtro-fecha" class="form-control form-control-sm" style="width:150px">
            </div>
        </div>
        <table class="table table-hover" id="tablaCitas" >
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Cédula</th>
                    <th>Médico</th>
                    <th>Especialidad</th>
                    <th>Fecha Cita</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    @if ($errors->any())
        <script>
            const errorMessages = @json(implode("\n", $errors->all()));

    Swal.fire({ 
        icon: 'error', 
        title: 'Error', 
        text: errorMessages 
    });
        </script>
    @endif

    <!-- Modal Mostrar Cita -->
    <div class="modal fade" id="modalShowCita" tabindex="-1" aria-labelledby="modalShowCitaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowCitaLabel">Detalles de la Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Paciente</label>
                            <p class="form-control-plaintext" id="mostrarPacienteCita"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cédula</label>
                            <p class="form-control-plaintext" id="mostrarCedulaCita"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Médico</label>
                            <p class="form-control-plaintext" id="mostrarMedicoCita"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Especialidad</label>
                            <p class="form-control-plaintext" id="mostrarEspecialidadCita"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha de la Cita</label>
                            <p class="form-control-plaintext" id="mostrarFechaCita"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Paciente</label>
                            <p class="form-control-plaintext" id="mostrarTipoCita"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado</label>
                            <p class="form-control-plaintext" id="mostrarEstadoCita"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha de Registro</label>
                            <p class="form-control-plaintext" id="mostrarFechaRegistroCita"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Reagendada</label>
                            <p class="form-control-plaintext" id="mostrarReagendadaCita"></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Observación</label>
                            <p class="form-control-plaintext" id="mostrarObservacionCita"></p>
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

    const table = $('#tablaCitas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('Citas.index') }}",
            type: 'GET',
            data: function(d) {
                d.fecha_filtro = $('#filtro-fecha').val();
            }
        },
        columns: [
            { data: '0', name: 'paciente' },
            { data: '1', name: 'cedula' },
            { data: '2', name: 'medico' },
            { data: '3', name: 'especialidad' },
            { data: '4', name: 'fecha_cita' },
            { data: '5', name: 'tipo_paciente', orderable: false, searchable: false },
            { data: '6', name: 'estado' },
            { data: '7', name: 'acciones', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: "{{ asset('vendor/datatables/es-ES.json') }}"
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[4, 'desc']]
    });

    $('#filtro-fecha').on('change', function() {
        table.ajax.reload();
    });
});

document.addEventListener('click', async function(event) {
    const btnShow = event.target.closest('.btn-show');

    if (btnShow) {
        const citaId = btnShow.getAttribute('data-id');
        const spanPaciente = document.getElementById('mostrarPacienteCita');
        const spanCedula = document.getElementById('mostrarCedulaCita');
        const spanMedico = document.getElementById('mostrarMedicoCita');
        const spanEspecialidad = document.getElementById('mostrarEspecialidadCita');
        const spanFecha = document.getElementById('mostrarFechaCita');
        const spanTipo = document.getElementById('mostrarTipoCita');
        const spanEstado = document.getElementById('mostrarEstadoCita');
        const spanFechaRegistro = document.getElementById('mostrarFechaRegistroCita');
        const spanReagendada = document.getElementById('mostrarReagendadaCita');
        const spanObservacion = document.getElementById('mostrarObservacionCita');

        try {
            const modalElement = document.getElementById('modalShowCita');
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }

            spanPaciente.innerHTML = "Cargando...";
            spanCedula.innerHTML = "Cargando...";
            spanMedico.innerHTML = "Cargando...";
            spanEspecialidad.innerHTML = "Cargando...";
            spanFecha.innerHTML = "Cargando...";
            spanTipo.innerHTML = "Cargando...";
            spanEstado.innerHTML = "Cargando...";
            spanFechaRegistro.innerHTML = "Cargando...";
            spanReagendada.innerHTML = "Cargando...";
            spanObservacion.innerHTML = "Cargando...";

            modalInstance.show();

            const response = await fetch(`/Citas/${citaId}/show`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Error al obtener datos');

            const data = await response.json();

            spanPaciente.innerHTML = data.paciente.nombre + ' ' + data.paciente.apellido;
            spanCedula.innerHTML = data.paciente.cedula;
            spanMedico.innerHTML = 'Dr. ' + data.calendario.medico.nombre + ' ' + data.calendario.medico.apellido;
            spanEspecialidad.innerHTML = data.calendario.medico.especialidad.nombre;
            spanFecha.innerHTML = new Date(data.fecha_cita).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
            spanTipo.innerHTML = data.tipo_paciente == 'primera_vez' ? 'Primera vez' : 'Control';
            spanEstado.innerHTML = data.estado;
            spanFechaRegistro.innerHTML = data.fecha_registro ? new Date(data.fecha_registro).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';
            spanReagendada.innerHTML = data.reagendada_contador + ' / 2';
            spanObservacion.innerHTML = data.observacion ? data.observacion : 'Sin observación';

        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos de la cita', 'error');
        }
    }
});
</script>
@endpush
