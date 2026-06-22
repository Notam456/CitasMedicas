@extends('layouts.template')
@section('title', 'Lista de Citas Atendidas | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Citas Atendidas - Diagnósticos</h3>
        </div>

        <!-- Filtros -->
        <div class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                    <select id="especialidad_filtro" class="form-select shadow-none">
                        <option value="">Todas</option>
                        @foreach($especialidades ?? [] as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">Fecha desde</label>
                <input type="date" id="fecha_desde" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">Fecha hasta</label>
                <input type="date" id="fecha_hasta" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="button" id="btnFiltrar" class="btn btn-primary w-100 shadow-sm">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
            </div>
            <div class="col-md-2">
                <button type="button" id="btnLimpiar" class="btn btn-secondary w-100 shadow-sm">
                    <i class="fas fa-undo me-1"></i> Limpiar
                </button>
            </div>
        </div>

        <table class="table table-hover" id="tablaDiagnosticos">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Cédula</th>
                    <th>Fecha Cita</th>
                    <th>Especialidad</th>
                    <th>Médico</th>
                    <th>Diagnóstico</th>
                    <th>Estado</th>
                    <th>Registrado por</th>
                    <th>Fecha Registro</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    @include('partials.modal-editar-diagnostico')

    @include('partials.modal-mostrar-cita', ['modalId' => 'modalShowDiagnostico', 'showPdf' => false])

    @include('layouts.footer')
@endsection

@push('scripts')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
    <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#tablaDiagnosticos').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("diagnosticos.index") }}',
                    data: function (d) {
                        d.especialidad_id = $('#especialidad_filtro').val();
                        d.fecha_desde = $('#fecha_desde').val();
                        d.fecha_hasta = $('#fecha_hasta').val();
                    }
                },
                columnDefs: [
                    {
                        targets: 1,      
                        className: "text-nowrap"
                    }
                ],
                columns: [
                    { data: 0, name: 'paciente' },
                    { data: 1, name: 'cedula' },
                    { data: 2, name: 'fecha_cita' },
                    { data: 3, name: 'especialidad' },
                    { data: 4, name: 'medico' },
                    { data: 5, name: 'diagnostico' },
                    { data: 6, name: 'estado' },
                    { data: 7, name: 'user' },
                    { data: 8, name: 'fecha_registro' },
                    { data: 9, name: 'action', orderable: false, searchable: false, className: 'text-end' }
                ],
                language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
                pageLength: 10,
                order: [[2, 'desc']]

            });

            $('#btnFiltrar').on('click', function () { table.ajax.reload(); });
            $('#btnLimpiar').on('click', function () {
                $('#especialidad_filtro').val('');
                $('#fecha_desde').val('');
                $('#fecha_hasta').val('');
                table.ajax.reload();
            });
        });

        // ---------- Evento Editar ----------
        document.addEventListener('click', async (e) => {
            const btnEdit = e.target.closest('.btn-edit');
            const btnShow = e.target.closest('.btn-show');

            if (btnEdit) {
                const id = btnEdit.dataset.id;
                try {
                    const res = await fetch(`/diagnosticos/${id}/edit`, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    window.patologiasDisponibles = data.patologias_disponibles || [];
                    limpiarContenedoresEdicion();

                    const cita = data.cita;
                    $('#edit_id').val(cita.id);
                    $('#edit_diagnostico_libre').val(cita.diagnostico_libre || '');

                    $('#edit_info_paciente').text(`${cita.paciente.nombre} ${cita.paciente.apellido}`);
                    $('#edit_info_cedula').text(cita.paciente.cedula);
                    $('#edit_info_fecha').text(new Date(cita.fecha_cita).toLocaleDateString());
                    $('#edit_info_medico').text(`Dr. ${cita.medico.nombre} ${cita.medico.apellido}`);
                    $('#edit_info_especialidad').text(cita.medico.especialidad.nombre);

                    if (cita.patologias && cita.patologias.length) {
                        cita.patologias.forEach(pat => addEditPatologiaRow(pat.id));
                    } else {
                        addEditPatologiaRow(null);
                    }

                    document.getElementById('editForm').action = `/diagnosticos/${cita.id}`;
                    new bootstrap.Modal(document.getElementById('modalEditarDiagnostico')).show();
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo cargar la información para editar', 'error');
                }
            }

            // ---------- Evento Mostrar ----------
            if (btnShow) {
                const id = btnShow.dataset.id;
                try {
                    const res = await fetch(`/diagnosticos/${id}`, { headers: { 'Accept': 'application/json' } });
                    const cita = await res.json();
                    populateShowModal(cita);
                    new bootstrap.Modal(document.getElementById('modalShowDiagnostico')).show();
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo cargar la información', 'error');
                }
            }
        });
    </script>
@endpush