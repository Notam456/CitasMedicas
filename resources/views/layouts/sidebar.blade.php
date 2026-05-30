<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="{{route('dashboard')}}" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary">SAGECIM</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
        </div>
        <div class="navbar-nav w-100">
            @can('Dashboard')
            <a href="{{ route('dashboard') }}" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
            @endcan

            @can('Usuarios')
            <a href="{{ route('users.index') }}" class="nav-item nav-link "><i class="fa fa-th me-2"></i>Usuarios</a>
            @endcan

            @can('Médicos')
            <a href="{{ route('medicos.index') }}" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Médicos</a>
            @endcan

            @can('Especialidad')
            <a href="{{ route('especialidades.index') }}" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Especialidad</a>
            @endcan

            @can('Patologia')
            <a href="{{ route('patologias.index') }}" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Patologias</a>
            @endcan

            @can('Paciente')
            <a href="{{ route('paciente.index') }}" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Paciente</a>
            @endcan

            @can('Procedencia')
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-map me-2"></i>Procedencia</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="{{ route('estados.index') }}" class="nav-item nav-link dropdown-item">Estado</a>
                    <a href="{{ route('municipios.index') }}" class="nav-item nav-link dropdown-item">Municipio</a>
                    <a href="{{ route('parroquias.index') }}" class="nav-item nav-link dropdown-item">Parroquia</a>
                    <a href="{{ route('distritos.index') }}" class="nav-item nav-link dropdown-item">Distrito</a>
                </div>
            </div>
            @endcan
            
            @can('Planificación')
            <a href="{{ route('calendario.index') }}" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Planificación</a>
            @endcan

            @can('Citas')
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-table me-2"></i>Citas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="{{ route('Citas.index') }}" class="nav-item nav-link dropdown-item">Agendar Citas</a>
                    <a href="{{ route('morbilidad.pendientes') }}" class="nav-item nav-link dropdown-item">Atender Citas</a>
                    <a href="{{ route('diagnosticos.index') }}" class="nav-item nav-link dropdown-item">Gestion Citas Atendidas</a>
                    @can('Morbilidad')
                    <a href="{{ route('morbilidad.index') }}" class="nav-item nav-link dropdown-item">Reporte de Citas</a>
                    @endcan
                </div>
            </div>
            @endcan

            @can('Reportes')
            <a href="{{route('reportes.index')}}" class="nav-link nav-item"><i class="far fa-file-alt me-2"></i>Reportes</a>
            @endcan
        </div>
    </nav>
</div>