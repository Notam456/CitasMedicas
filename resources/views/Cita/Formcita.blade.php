@extends('layouts.template')
@section('title', 'Agendar Cita')

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="container-fluid px-4 py-4">
    
    <div class="mb-4">
        <h2 class="fw-bold text-primary border-bottom pb-2">Agendar Cita: {{ $especialidad->nombre }}</h2>
    </div>

    <form action="{{ route('Citas.store') }}" method="POST" class="card shadow-sm border-0">
        @csrf
        <input type="hidden" name="especialidad_id" value="{{ $especialidad->id }}">
        
        <div class="card-body p-4">
            
            <h4 class="text-secondary mb-3"><i class="fas fa-user-injured me-2"></i>Datos del Paciente</h4>
            
            <div class="row g-3 bg-light p-3 rounded mb-4 border">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Cédula del Paciente *</label>
                    <div class="input-group">
                        <input type="text" name="cedula" id="input_cedula" class="form-control" placeholder="V-12345678" required>
                        <button type="button" class="btn btn-secondary" id="btn_buscar_cedula">Buscar</button>
                    </div>
                    <small id="mensaje_cedula" class="form-text mt-1 text-primary">Ingrese cédula para buscar.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" id="input_nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" id="input_apellido" class="form-control @error('aepllido') is-invalid @enderror" value="{{ old('apellido') }}" required>
                    @error('apellido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">RIF</label>
                    <input type="text" name="rif" id="input_rif" class="form-control @error('rif') is-invalid @enderror" value="{{ old('rif') }}" required>
                    @error('rif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">F. Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="input_fecha" class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento') }}" required>
                    @error('fecha_nacimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="input_telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" required>
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select id="select-estado" class="form-select">
                        <option value="">Seleccione Estado</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            
                <div class="col-md-4">
                    <label class="form-label">Municipio</label>
                    <select id="select-municipio" class="form-select">
                        <option value="">Seleccione Municipio</option>
                    </select>
                </div>
            
                <div class="col-md-4">
                    <label class="form-label">Parroquia</label>
                    <select name="parroquia_id" id="select-parroquia" class="form-select @error('parroquia_id') is-invalid @enderror" required>
                        <option value="">Seleccione Parroquia</option>
                    </select>
                    @error('parroquia_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Dirección exacta</label>
                    <input type="text" name="direccion" id="input_direccion" class="form-control">
                </div>
            </div>

            <h4 class="text-secondary mb-3"><i class="fas fa-calendar-check me-2"></i>Datos de la Cita</h4>

            <div class="row g-3 bg-light p-3 rounded mb-4 border">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cupo</label>
                        <input type="number" name="calendario_id" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Cita</label>
                        <input type="date" name="fecha_cita" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Observación</label>
                        <textarea name="observacion" class="form-control" rows="1" placeholder="Síntomas o nota..."></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-white text-end py-3">
            <a href="{{ route('Citas.agendar.especialidad', $especialidad->id) }}" class="btn btn-outline-danger me-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-5">Confirmar y Agendar Cita</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('btn_buscar_cedula').addEventListener('click', function() {
        let cedula = document.getElementById('input_cedula').value;
        let mensaje = document.getElementById('mensaje_cedula');
        
        if(cedula.length < 5) return;

        mensaje.innerHTML = 'Buscando...';
        mensaje.className = 'form-text mt-1 text-warning';

        fetch(`/api/paciente/buscar/${cedula}`)
            .then(response => response.json())
            .then(data => {
                if (data.encontrado) {
                    document.getElementById('input_nombre').value = data.datos.nombre;
                    document.getElementById('input_apellido').value = data.datos.apellido;
                    document.getElementById('input_fecha').value = data.datos.fecha_nacimiento;
                    document.getElementById('input_telefono').value = data.datos.telefono;
                    document.getElementById('input_direccion').value = data.datos.direccion;

                    const p = data.datos.parroquia;
                    const m = p.municipio;
                    const e = m.estado;
                    
                    document.getElementById('select-estado').value = e.id;
                    
                    let selectM = document.getElementById('select-municipio');
                    selectM.innerHTML = `<option value="${m.id}" selected>${m.nombre}</option>`;
                    
                    let selectP = document.getElementById('select-parroquia');
                    selectP.innerHTML = `<option value="${p.id}" selected>${p.nombre}</option>`;

                    document.getElementById('select-estado').disabled = true;
                    document.getElementById('select-municipio').disabled = true;
                    document.getElementById('select-parroquia').disabled = true;
                    if(!document.getElementById('hidden_parroquia')) {
                        let hiddenP = document.createElement('input');
                        hiddenP.type = 'hidden';
                        hiddenP.name = 'parroquia_id';
                        hiddenP.id = 'hidden_parroquia';
                        hiddenP.value = p.id;
                        document.querySelector('form').appendChild(hiddenP);
                    } else {
                        document.getElementById('hidden_parroquia').value = p.id;
                    }
                    
                    document.querySelectorAll('#input_nombre, #input_apellido, #input_fecha, #input_telefono, #input_direccion').forEach(el => el.readOnly = true);
                    
                    mensaje.innerHTML = 'Paciente encontrado. Datos autocompletados.';
                    mensaje.className = 'form-text mt-1 text-success fw-bold';
                } else {
                    document.getElementById('input_nombre').value = '';
                    document.getElementById('input_apellido').value = '';
                    document.getElementById('input_fecha').value = '';
                    document.getElementById('input_telefono').value = '';
                    document.getElementById('input_direccion').value = '';
                    
                    document.getElementById('select-estado').value = '';
                    document.getElementById('select-estado').disabled = false;
                    document.getElementById('select-municipio').innerHTML = '<option value="">Seleccione Municipio</option>';
                    document.getElementById('select-municipio').disabled = false;
                    document.getElementById('select-parroquia').innerHTML = '<option value="">Seleccione Parroquia</option>';
                    document.getElementById('select-parroquia').disabled = false;
                    
                    let hiddenP = document.getElementById('hidden_parroquia');
                    if(hiddenP) hiddenP.remove();
                    
                    document.querySelectorAll('#input_nombre, #input_apellido, #input_fecha, #input_telefono, #input_direccion').forEach(el => el.readOnly = false);
                    
                    mensaje.innerHTML = 'Paciente nuevo. Por favor llene todos los campos.';
                    mensaje.className = 'form-text mt-1 text-primary fw-bold';
                }
            })
            .catch(error => {
                console.error(error);
                mensaje.innerHTML = 'Error al procesar la solicitud.';
                mensaje.className = 'form-text mt-1 text-danger';
            });
    });

    document.getElementById('select-estado').addEventListener('change', function() {
        let estadoId = this.value;
        let selectMunicipio = document.getElementById('select-municipio');
        let selectParroquia = document.getElementById('select-parroquia');
    
        selectMunicipio.innerHTML = '<option value="">Cargando...</option>';
        selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';
    
        if(estadoId) {
            fetch(`/api/municipios/${estadoId}`)
                .then(res => res.json())
                .then(data => {
                    selectMunicipio.innerHTML = '<option value="">Seleccione Municipio</option>';
                    data.forEach(m => {
                        selectMunicipio.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
                    });
                })
                .catch(err => console.error(err));
        }
    });
    
    document.getElementById('select-municipio').addEventListener('change', function() {
        let municipioId = this.value;
        let selectParroquia = document.getElementById('select-parroquia');
    
        selectParroquia.innerHTML = '<option value="">Cargando...</option>';
    
        if(municipioId) {
            fetch(`/api/parroquias/${municipioId}`)
                .then(res => res.json())
                .then(data => {
                    selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';
                    data.forEach(p => {
                        selectParroquia.innerHTML += `<option value="${p.id}">${p.nombre}</option>`;
                    });
                })
                .catch(err => console.error(err));
        }
    });
</script>


@include('layouts.footer')
@endsection