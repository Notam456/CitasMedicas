@extends('layouts.template')
@section('title','Dashboard | SAGECIM')
    
@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')
                <!-- estadisticas basicas -->
                <div class="container-fluid pt-4 px-4">
                    <div class="row g-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                                <i class="fa fa-chart-line fa-3x text-primary"></i>
                                <div class="ms-3">
                                    <p class="mb-2">Especialidad con Demanda</p>
                                    <h6 class="mb-0">{{ $especialidadDemanda }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                                <i class="fa fa-chart-bar fa-3x text-primary"></i>
                                <div class="ms-3">
                                    <p class="mb-2">Pacientes este Mes</p>
                                    <h6 class="mb-0">{{ $pacientesAtendidosMes }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                                <i class="fa fa-chart-area fa-3x text-primary"></i>
                                <div class="ms-3">
                                    <p class="mb-2">Pacientes del Día</p>
                                    <h6 class="mb-0">{{ $pacientesDelDia }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                                <i class="fa fa-map fa-3x text-primary"></i>
                                <div class="ms-3">
                                    <p class="mb-2">Municipio con Demanda</p>
                                    <h6 class="mb-0">{{ $procedenciaMasPacientes }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- estadisticas basicas -->
    
    
                <!-- municipios con mas pacientes chart.js -->
                <div class="row g-4 mx-0">
                    <div class="col-sm-12 col-md-6">
                        <div class="bg-light text-center rounded p-4">
                            <h6 class="mb-0">Top 5 Municipios</h6>
                            <canvas id="municipiosChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="bg-light text-center rounded p-4">
                            <h6 class="mb-0">Top 5 Especialidades</h6>
                            <canvas id="especialidadesChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- municipios con mas pacientes chart.js -->
    
    
                 <!-- ultimas citas tabla -->
                 <div class="container-fluid pt-4 px-4">
                    <div class="bg-light text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">Ultimas Citas</h6>
                            <a target="_blank" href="{{ route('Citas.index') }}">Mostrar Todas</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-start align-middle table-bordered table-hover mb-0">
                                <thead>
                                    <tr class="text-dark">
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Paciente</th>
                                        <th scope="col">Especialidad</th>
                                        <th scope="col">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ultimasCitas as $cita)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</td>
                                        <td>{{ $cita->paciente_nombre }} {{ $cita->paciente_apellido }}</td>
                                        <td>{{ $cita->especialidad_nombre }}</td>
                                        <td>
                                            @if($cita->estado == 'Agendada')
                                                <span class="badge bg-success">Agendada</span>
                                            @elseif($cita->estado == 'Atendida')
                                                <span class="badge bg-primary">Atendida</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $cita->estado }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No hay citas registradas.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- ultimas citas tabla -->

             <!-- funcion de los charts de municipios y especialidades -->
             <script>
                window.municipiosLabels = @json($municipiosLabels);
                window.municipiosData = @json($municipiosData);
                window.especialidadesLabels = @json($especialidadesLabels);
                window.especialidadesData = @json($especialidadesData);
            </script>
             <script src="{{asset('assets/js/dashboard.js')}}""></script>
             <!-- funcion de los charts de municipios y especialidades -->

             @include('layouts.footer')
@endsection