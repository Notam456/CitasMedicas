@extends('layouts.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Registro de Nuevo Paciente</h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('pacientes.store') }}" method="POST">
                        @csrf 

                        <h5 class="text-primary">Datos Personales</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Pérez" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cédula / Identificación</label>
                                <input type="text" name="cedula" class="form-control" placeholder="V-0000000" required>
                            </div>
                        </div>

                        <hr>

                        <h5 class="text-primary">Procedencia (Ubicación)</h5>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="estado_id" class="form-label">Estado</label>
                                <select id="estado_id" name="estado_id" class="form-select" required>
                                    <option value="">Seleccione un Estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="municipio_id" class="form-label">Municipio</label>
                                <select id="municipio_id" name="municipio_id" class="form-select" disabled required>
                                    <option value="">Seleccione Municipio</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="parroquia_id" class="form-label">Parroquia</label>
                                <select id="parroquia_id" name="parroquia_id" class="form-select" disabled required>
                                    <option value="">Seleccione Parroquia</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('pacientes.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Paciente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const estadoSelect = document.getElementById('estado_id');
        const municipioSelect = document.getElementById('municipio_id');
        const parroquiaSelect = document.getElementById('parroquia_id');

        // URL BASE Dinámica de Laravel
        const baseUrl = "{{ url('/') }}";

        // 1. CAMBIO DE ESTADO -> CARGAR MUNICIPIOS
        estadoSelect.addEventListener('change', async (e) => {
            const id = e.target.value;
            
            municipioSelect.innerHTML = '<option value="">Cargando...</option>';
            municipioSelect.disabled = true;
            parroquiaSelect.innerHTML = '<option value="">Seleccione Parroquia</option>';
            parroquiaSelect.disabled = true;
            
            if (id) {
                try {
                    // CAMBIO AQUÍ: Usamos baseUrl para que la ruta sea correcta
                    const response = await axios.get(`${baseUrl}/municipios-por-estado/${id}`);
                    municipioSelect.innerHTML = '<option value="">Seleccione Municipio</option>';
                    
                    response.data.forEach(m => {
                        const option = document.createElement('option');
                        option.value = m.id;
                        option.textContent = m.nombre;
                        municipioSelect.appendChild(option);
                    });
                    municipioSelect.disabled = false;
                } catch (error) {
                    console.error('Error municipios:', error);
                    municipioSelect.innerHTML = '<option value="">Error al cargar</option>';
                }
            } else {
                municipioSelect.innerHTML = '<option value="">Seleccione Municipio</option>';
            }
        });

        // 2. CAMBIO DE MUNICIPIO -> CARGAR PARROQUIAS
        municipioSelect.addEventListener('change', async (e) => {
            const id = e.target.value;
            
            parroquiaSelect.innerHTML = '<option value="">Cargando...</option>';
            parroquiaSelect.disabled = true;

            if (id) {
                try {
                    // CAMBIO AQUÍ: Usamos baseUrl
                    const response = await axios.get(`${baseUrl}/parroquias-por-municipio/${id}`);
                    parroquiaSelect.innerHTML = '<option value="">Seleccione Parroquia</option>';
                    
                    response.data.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.nombre;
                        parroquiaSelect.appendChild(option);
                    });
                    parroquiaSelect.disabled = false;
                } catch (error) {
                    console.error('Error parroquias:', error);
                    parroquiaSelect.innerHTML = '<option value="">Error al cargar</option>';
                }
            } else {
                parroquiaSelect.innerHTML = '<option value="">Seleccione Parroquia</option>';
            }
        });
    });
</script>
@endsection