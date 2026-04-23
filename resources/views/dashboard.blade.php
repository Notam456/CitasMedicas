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
                                    <p class="mb-2">Pacientes Atendidos este Mes</p>
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
                                    <p class="mb-2">Procedencia con mas Pacientes</p>
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
    
    
                <!-- pacientes recientes tabla -->
                <div class="container-fluid pt-4 px-4">
                    <div class="bg-light text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">Ultimas Citas</h6>
                            <a href="">Mostrar Todas</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-start align-middle table-bordered table-hover mb-0">
                                <thead>
                                    <tr class="text-dark">
                                        <th scope="col"><input class="form-check-input" type="checkbox"></th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">Paciente</th>
                                        <th scope="col">Especialidad</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Reporte</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input class="form-check-input" type="checkbox"></td>
                                        <td>01 Jun 2026</td>
                                        <td>01</td>
                                        <td>Jhon Garcia</td>
                                        <td>Oftalmología</td>
                                        <td>Pendiente</td>
                                        <td><a class="btn btn-sm btn-primary" href="">PDF</a></td>
                                    </tr>
                                    <tr>
                                        <td><input class="form-check-input" type="checkbox"></td>
                                        <td>01 Jun 2026</td>
                                        <td>02</td>
                                        <td>Julia Dante</td>
                                        <td>Aro</td>
                                        <td>Pendiente</td>
                                        <td><a class="btn btn-sm btn-primary" href="">PDF</a></td>
                                    </tr>
                                    <tr>
                                        <td><input class="form-check-input" type="checkbox"></td>
                                        <td>01 Jun 2026</td>
                                        <td>03</td>
                                        <td>Gunnar Henderson</td>
                                        <td>Oftalmología</td>
                                        <td>Pendiente</td>
                                        <td><a class="btn btn-sm btn-primary" href="">PDF</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- pacientes recientes tabla -->
    
    
                <!-- Widgets Start 
                <div class="container-fluid pt-4 px-4">
                    <div class="row g-4">
                        <div class="col-sm-12 col-md-6 col-xl-4">
                            <div class="h-100 bg-light rounded p-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0">Messages</h6>
                                    <a href="">Show All</a>
                                </div>
                                <div class="d-flex align-items-center border-bottom py-3">
                                    <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-0">Jhon Doe</h6>
                                            <small>15 minutes ago</small>
                                        </div>
                                        <span>Short message goes here...</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center border-bottom py-3">
                                    <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-0">Jhon Doe</h6>
                                            <small>15 minutes ago</small>
                                        </div>
                                        <span>Short message goes here...</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center border-bottom py-3">
                                    <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-0">Jhon Doe</h6>
                                            <small>15 minutes ago</small>
                                        </div>
                                        <span>Short message goes here...</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center pt-3">
                                    <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-0">Jhon Doe</h6>
                                            <small>15 minutes ago</small>
                                        </div>
                                        <span>Short message goes here...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-xl-4">
                            <div class="h-100 bg-light rounded p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h6 class="mb-0">Calender</h6>
                                    <a href="">Show All</a>
                                </div>
                                <div id="calender"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-xl-4">
                            <div class="h-100 bg-light rounded p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h6 class="mb-0">To Do List</h6>
                                    <a href="">Show All</a>
                                </div>
                                <div class="d-flex mb-2">
                                    <input class="form-control bg-transparent" type="text" placeholder="Enter task">
                                    <button type="button" class="btn btn-primary ms-2">Add</button>
                                </div>
                                <div class="d-flex align-items-center border-bottom py-2">
                                    <input class="form-check-input m-0" type="checkbox">
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 align-items-center justify-content-between">
                                            <span>Short task goes here...</span>
                                            <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center border-bottom py-2">
                                    <input class="form-check-input m-0" type="checkbox">
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 align-items-center justify-content-between">
                                            <span>Short task goes here...</span>
                                            <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center border-bottom py-2">
                                    <input class="form-check-input m-0" type="checkbox" checked>
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 align-items-center justify-content-between">
                                            <span><del>Short task goes here...</del></span>
                                            <button class="btn btn-sm text-primary"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center border-bottom py-2">
                                    <input class="form-check-input m-0" type="checkbox">
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 align-items-center justify-content-between">
                                            <span>Short task goes here...</span>
                                            <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center pt-2">
                                    <input class="form-check-input m-0" type="checkbox">
                                    <div class="w-100 ms-3">
                                        <div class="d-flex w-100 align-items-center justify-content-between">
                                            <span>Short task goes here...</span>
                                            <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             Widgets End -->

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