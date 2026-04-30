@extends('layouts.template')

@section('title', 'Dashboard | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Parroquias</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalParroquia">
                <i class="bi bi-plus-lg me-1"></i> Registrar Parroquia
            </button>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Municipio</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parroquias as $parroquia)
                    <tr>
                        <td>{{ $parroquia->nombre }}</td>
                        <td>{{ $parroquia->municipio->nombre ?? 'N/A' }}</td>
                        <td>{{ $parroquia->municipio->estado->nombre ?? 'N/A' }}</td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <a href="{{ route('parroquias.show', $parroquia->id) }}" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('parroquias.edit', $parroquia->id) }}" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('parroquias.destroy', $parroquia->id) }}" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Registrar Parroquia -->
    <div class="modal fade" id="modalParroquia" tabindex="-1" aria-labelledby="modalParroquiaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalParroquiaLabel">Registrar Parroquia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('parroquias.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" id="nombreParroquia" placeholder="Nombre de la parroquia" required>
                            <label for="nombreParroquia">Nombre</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="municipio_id" class="form-label">Municipio</label>
                            <select name="municipio_id" id="municipio_id" class="form-select" required>
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id }}" {{ old('municipio_id') == $municipio->id ? 'selected' : '' }}>
                                        {{ $municipio->nombre }} ({{ $municipio->estado->nombre }})
                                    </option>
                                @endforeach
                            </select>
                            @error('municipio_id')
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

    <!-- Modal Editar Parroquia -->
    <div class="modal fade" id="modalEditarParroquia" tabindex="-1" aria-labelledby="modalEditarParroquiaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarParroquiaLabel">Editar Parroquia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ isset($parroquiaToEdit) ? route('parroquias.update', $parroquiaToEdit->id) : '#' }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre" value="{{ old('nombre', $parroquiaToEdit->nombre ?? '') }}" class="form-control" id="editarNombreParroquia" placeholder="Nombre de la parroquia" required>
                            <label for="editarNombreParroquia">Nombre</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="editarMunicipioParroquia" class="form-label">Municipio</label>
                            <select name="municipio_id" id="editarMunicipioParroquia" class="form-select" required>
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id }}" {{ old('municipio_id', $parroquiaToEdit->municipio_id ?? '') == $municipio->id ? 'selected' : '' }}>
                                        {{ $municipio->nombre }} ({{ $municipio->estado->nombre }})
                                    </option>
                                @endforeach
                            </select>
                            @error('municipio_id')
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

    <!-- Modal Mostrar Parroquia -->
    <div class="modal fade" id="modalShowParroquia" tabindex="-1" aria-labelledby="modalShowParroquiaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowParroquiaLabel">Datos de la Parroquia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <p class="form-control">{{ $parroquiaToshow->nombre ?? '' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Municipio</label>
                        <p class="form-control">{{ $parroquiaToshow->municipio->nombre ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <p class="form-control">{{ $parroquiaToshow->municipio->estado->nombre ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@if(isset($parroquiaToEdit))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalEditarParroquia');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif

@if(isset($parroquiaToshow))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalShowParroquia');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif

@if($errors->any() && !isset($parroquiaToEdit) && !isset($parroquiaToshow))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalParroquia');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif

@include('layouts.footer')
@endsection