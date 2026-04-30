@extends('layouts.template')

@section('title', 'Dashboard | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Municipios</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMunicipio">
                <i class="bi bi-plus-lg me-1"></i> Registrar Municipio
            </button>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($municipios as $municipio)
                    <tr>
                        <td>{{ $municipio->nombre }}</td>
                        <td>{{ $municipio->estado->nombre ?? 'N/A' }}</td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <a href="{{ route('municipios.show', $municipio->id) }}" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('municipios.edit', $municipio->id) }}" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('municipios.destroy', $municipio->id) }}" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Registrar Municipio -->
    <div class="modal fade" id="modalMunicipio" tabindex="-1" aria-labelledby="modalMunicipioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMunicipioLabel">Registrar Municipio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('municipios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" id="nombreMunicipio" placeholder="Nombre del municipio" required>
                            <label for="nombreMunicipio">Nombre</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="estado_id" class="form-label">Estado</label>
                            <select name="estado_id" id="estado_id" class="form-select" required>
                                <option value="">Seleccione un estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                            @error('estado_id')
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

    <!-- Modal Editar Municipio -->
    <div class="modal fade" id="modalEditarMunicipio" tabindex="-1" aria-labelledby="modalEditarMunicipioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarMunicipioLabel">Editar Municipio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ isset($municipioToEdit) ? route('municipios.update', $municipioToEdit->id) : '#' }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nombre" value="{{ old('nombre', $municipioToEdit->nombre ?? '') }}" class="form-control" id="editarNombreMunicipio" placeholder="Nombre del municipio" required>
                            <label for="editarNombreMunicipio">Nombre</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="editarEstadoMunicipio" class="form-label">Estado</label>
                            <select name="estado_id" id="editarEstadoMunicipio" class="form-select" required>
                                <option value="">Seleccione un estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}" {{ old('estado_id', $municipioToEdit->estado_id ?? '') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                            @error('estado_id')
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

    <!-- Modal Mostrar Municipio -->
    <div class="modal fade" id="modalShowMunicipio" tabindex="-1" aria-labelledby="modalShowMunicipioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowMunicipioLabel">Datos del Municipio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <p class="form-control">{{ $municipioToshow->nombre ?? '' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <p class="form-control">{{ $municipioToshow->estado->nombre ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@if(isset($municipioToEdit))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalEditarMunicipio');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif

@if(isset($municipioToshow))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalShowMunicipio');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif

@if($errors->any() && !isset($municipioToEdit) && !isset($municipioToshow))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalMunicipio');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif

@include('layouts.footer')
@endsection