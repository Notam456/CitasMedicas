@extends('layouts.template')
@section('title', 'Citas Agendadas | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Citas Agendadas</h3>
            <a href="{{ route('Citas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus me-1"></i> Nueva Cita
            </a>
        </div>
        <table class="table table-hover" id="tablaCitas" >
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Cédula</th>
                    <th>Médico</th>
                    <th>Especialidad</th>
                    <th>Fecha Cita</th>
                    <th>Registro</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            
            </tbody>
        </table>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '• {{ $error }}\n';
                @endforeach
                Swal.fire({
                    icon: 'error',
                    title: '¡Ups! Algo salió mal en tu accion intentalo de nuevo',
                    text: errorMessages,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Entendido'
                });
            });
        </script>
    @endif

    @include('layouts.footer')
@endsection

@push('scripts')

<link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

<script>
$(document).ready(function() {
    
    $('#tablaCitas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('Citas.index') }}", 
            type: 'GET'
        },
        columns: [
            { data: '0', name: 'paciente' },
            { data: '1', name: 'cedula' },
            { data: '2', name: 'medico' },
            { data: '3', name: 'especialidad' },
            { data: '4', name: 'fecha_cita' },
            { data: '5', name: 'fecha_registro' },
            { data: '6', name: 'tipo_paciente', orderable: false, searchable: false },
            { data: '7', name: 'estado' },
            { data: '8', name: 'acciones', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: "{{ asset('vendor/datatables/es-ES.json') }}"
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[4, 'desc']]
    });
});
</script>
@endpush