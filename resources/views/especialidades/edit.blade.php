@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Editar Especialidad</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('especialidades.update', $especialidad->id_especialidad) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $especialidad->nombre }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ $especialidad->descripcion }}</textarea>
                </div>
                <div class="mb-3 form-check">
                    <input type="hidden" name="estado" value="0">
                    <input type="checkbox" name="estado" value="1" class="form-check-input" {{ $especialidad->estado ? 'checked' : '' }}>
                    <label class="form-check-label">Activo</label>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('especialidades.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
