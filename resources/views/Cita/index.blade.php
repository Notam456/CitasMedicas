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
        <table class="table table-hover">
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
                @forelse ($citas as $cita)
                    <tr>
                        <td>{{ $cita->paciente->nombre }} {{ $cita->paciente->apellido }}</td>
                        <td>{{ $cita->paciente->cedula }}</td>
                        <td>{{ $cita->calendario->medico->nombre }} {{ $cita->calendario->medico->apellido }}</td>
                        <td>{{ $cita->calendario->medico->especialidad->nombre }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha_registro)->format('d/m/Y') }}</td>
                        <td>
                            @if($cita->tipo_paciente == 'primera_vez')
                                <span class="badge bg-info">Primera vez</span>
                            @else
                                <span class="badge bg-warning">Control</span>
                            @endif
                        </td>
                        <td>
                            @if($cita->estado == 'Agendada')
                                <span class="badge bg-success">Agendada</span>
                            @elseif($cita->estado == 'Atendida')
                                <span class="badge bg-primary">Atendida</span>
                            @else
                                <span class="badge bg-secondary">{{ $cita->estado }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" data-id="{{ $cita->id }}"
                                    class="btn-show btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="{{ route('Citas.destroy', $cita->id) }}"
                                    class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover"
                                    data-confirm-delete="true">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No hay citas registradas</td>
                    </tr>
                @endforelse
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