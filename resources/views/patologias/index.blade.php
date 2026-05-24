@extends('layouts.template')
@section('title', 'Lista de Patologías | SAGECIM')

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="table-responsive bg-light rounded h-100 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Lista de Patologías</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPatologia">
            <i class="bi bi-plus-circle me-1"></i> Registrar Patología
        </button>
    </div>

    <table class="table table-hover" id="tablaPatologias">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Especialidad</th>
                <th>Descripción</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Registrar -->
<div class="modal fade" id="modalPatologia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Patología</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('patologias.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre" required>
                        <label>Nombre de la Patología</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="especialidad_id" required>
                            <option value="">Seleccione</option>
                            @foreach($especialidades as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                        <label>Especialidad</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" name="descripcion" style="height: 100px" placeholder="Descripción"></textarea>
                        <label>Descripción</label>
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

<!-- Modal Editar -->
<div class="modal fade" id="modalEditarPatologia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Patología</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        <label>Nombre de la Patología</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="edit_especialidad_id" name="especialidad_id" required>
                            <option value="">Seleccione</option>
                            @foreach($especialidades as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                        <label>Especialidad</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" style="height: 100px"></textarea>
                        <label>Descripción</label>
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

<!-- Modal Mostrar -->
<div class="modal fade" id="modalShowPatologia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos de la Patología</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <span id="show_nombre"></span></p>
                <p><strong>Especialidad:</strong> <span id="show_especialidad"></span></p>
                <p><strong>Descripción:</strong> <span id="show_descripcion"></span></p>
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
    $('#tablaPatologias').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("patologias.index") }}',
        columns: [
            { data: 0, name: 'nombre' },
            { data: 1, name: 'especialidad' },
            { data: 2, name: 'descripcion' },
            { data: 3, name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[0, 'asc']]
    });
});

document.addEventListener('click', async (e) => {
    const btnEdit = e.target.closest('.btn-edit');
    const btnShow = e.target.closest('.btn-show');
    if (btnEdit) {
        const id = btnEdit.dataset.id;
        try {
            const res = await fetch(`/patologias/${id}/edit`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_nombre').value = data.nombre;
            document.getElementById('edit_especialidad_id').value = data.especialidad_id;
            document.getElementById('edit_descripcion').value = data.descripcion || '';
            document.getElementById('editForm').action = `/patologias/${data.id}`;
            new bootstrap.Modal(document.getElementById('modalEditarPatologia')).show();
        } catch { Swal.fire('Error', 'No se pudo cargar', 'error'); }
    }
    if (btnShow) {
        const id = btnShow.dataset.id;
        try {
            const res = await fetch(`/patologias/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            document.getElementById('show_nombre').innerText = data.nombre;
            document.getElementById('show_especialidad').innerText = data.especialidad?.nombre || 'N/A';
            document.getElementById('show_descripcion').innerText = data.descripcion || '';
            new bootstrap.Modal(document.getElementById('modalShowPatologia')).show();
        } catch { Swal.fire('Error', 'No se pudo cargar', 'error'); }
    }
});
</script>
@endpush