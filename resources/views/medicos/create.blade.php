@extends('layouts.template')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="card">
        <div class="card-header">
            <h3>Crear Médico</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('medicos.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="nombres" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cédula Profesional</label>
                        <input type="text" name="cedula" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Especialidad</label>
                    <select name="id_especialidad" class="form-control" required>
                        <option value="">Seleccione una especialidad</option>
                        @foreach($especialidades as $e)
                            <option value="{{ $e->id_especialidad }}">{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input type="hidden" name="estado" value="0">
                    <input type="checkbox" name="estado" value="1" class="form-check-input" checked>
                    <label class="form-check-label">Activo</label>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('medicos.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection