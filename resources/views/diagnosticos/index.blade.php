@extends('layouts.template')
@section('title', 'Lista de Diagnósticos | SAGECIM')

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="table-responsive bg-light rounded h-100 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Lista de Diagnósticos</h3>
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
                <th>Asistió</th>
                <th>Registrado por</th>
                <th>Fecha Registro</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Editar Diagnóstico -->
<div class="modal fade" id="modalEditarDiagnostico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Diagnóstico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Patología</label>
                        <select name="patologia_id" id="edit_patologia_id" class="form-select">
                            <option value="">Seleccione</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Diagnóstico libre</label>
                        <textarea name="diagnostico_libre" id="edit_diagnostico_libre" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="asistio" value="1" class="form-check-input" id="edit_asistio">
                        <label class="form-check-label">Paciente asistió</label>
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

<!-- Modal Mostrar Diagnóstico -->
<div class="modal fade" id="modalShowDiagnostico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Diagnóstico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Paciente:</strong> <span id="show_paciente"></span></p>
                <p><strong>Cédula:</strong> <span id="show_cedula"></span></p>
                <p><strong>Fecha Cita:</strong> <span id="show_fecha_cita"></span></p>
                <p><strong>Especialidad:</strong> <span id="show_especialidad"></span></p>
                <p><strong>Médico:</strong> <span id="show_medico"></span></p>
                <p><strong>Patología:</strong> <span id="show_patologia"></span></p>
                <p><strong>Diagnóstico libre:</strong> <span id="show_diagnostico_libre"></span></p>
                <p><strong>Asistió:</strong> <span id="show_asistio"></span></p>
                <p><strong>Registrado por:</strong> <span id="show_user"></span></p>
                <p><strong>Fecha registro:</strong> <span id="show_fecha_registro"></span></p>
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
    $('#tablaDiagnosticos').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("diagnosticos.index") }}',
        columns: [
            { data: 0, name: 'paciente' },
            { data: 1, name: 'cedula' },
            { data: 2, name: 'fecha_cita' },
            { data: 3, name: 'especialidad' },
            { data: 4, name: 'medico' },
            { data: 5, name: 'diagnostico' },
            { data: 6, name: 'asistio' },
            { data: 7, name: 'user' },
            { data: 8, name: 'fecha_registro' },
            { data: 9, name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
        pageLength: 10,
        order: [[2, 'desc']]
    });
});

// Editar
document.addEventListener('click', async (e) => {
    const btnEdit = e.target.closest('.btn-edit');
    const btnShow = e.target.closest('.btn-show');

    if (btnEdit) {
        const id = btnEdit.dataset.id;
        try {
            const res = await fetch(`/diagnosticos/${id}/edit`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            document.getElementById('edit_id').value = data.diagnostico.id;
            document.getElementById('edit_diagnostico_libre').value = data.diagnostico.diagnostico_libre || '';
            document.getElementById('edit_asistio').checked = data.diagnostico.asistio == 1;
            const select = document.getElementById('edit_patologia_id');
            select.innerHTML = '<option value="">Seleccione</option>';
            data.patologias.forEach(pat => {
                const selected = pat.id == data.diagnostico.patologia_id ? 'selected' : '';
                select.innerHTML += `<option value="${pat.id}" ${selected}>${pat.nombre}</option>`;
            });
            document.getElementById('editForm').action = `/diagnosticos/${data.diagnostico.id}`;
            new bootstrap.Modal(document.getElementById('modalEditarDiagnostico')).show();
        } catch { Swal.fire('Error', 'No se pudo cargar', 'error'); }
    }

    if (btnShow) {
        const id = btnShow.dataset.id;
        try {
            const res = await fetch(`/diagnosticos/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            document.getElementById('show_paciente').innerText = `${data.cita.paciente.nombre} ${data.cita.paciente.apellido}`;
            document.getElementById('show_cedula').innerText = data.cita.paciente.cedula;
            document.getElementById('show_fecha_cita').innerText = new Date(data.cita.fecha_cita).toLocaleDateString();
            document.getElementById('show_especialidad').innerText = data.cita.medico.especialidad.nombre;
            document.getElementById('show_medico').innerText = `Dr. ${data.cita.medico.nombre} ${data.cita.medico.apellido}`;
            document.getElementById('show_patologia').innerText = data.patologia?.nombre || 'Ninguna';
            document.getElementById('show_diagnostico_libre').innerText = data.diagnostico_libre || '';
            document.getElementById('show_asistio').innerText = data.asistio ? 'Sí' : 'No';
            document.getElementById('show_user').innerText = data.user.name;
            document.getElementById('show_fecha_registro').innerText = new Date(data.created_at).toLocaleString();
            new bootstrap.Modal(document.getElementById('modalShowDiagnostico')).show();
        } catch { Swal.fire('Error', 'No se pudo cargar', 'error'); }
    }
});
</script>
@endpush