@extends('layouts.template')
@section('title', 'Editar Especialidad | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Editar Especialidad</h3>
        </div>

        <form action="{{ route('especialidades.update', $especialidad->id_especialidad) }}" method="POST">
            @csrf
            @method('PUT')
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

    @include('layouts.footer')
@endsection