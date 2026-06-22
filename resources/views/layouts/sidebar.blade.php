<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="{{route('dashboard')}}" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary">SAGECIM</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
        </div>
        <div class="navbar-nav w-100">
            @can('Dashboard')
            <a href="{{ route('dashboard') }}" class="nav-item nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            @endcan

            @can('Usuarios')
            <a href="{{ route('users.index') }}" class="nav-item nav-link "><i class="bi bi-people-fill me-2"></i>Usuarios</a>
            @endcan

            @can('Médicos')
            <a href="{{ route('medicos.index') }}" class="nav-item nav-link"><i class="bi bi-person-badge me-2"></i>Médicos</a>
            @endcan

            @can('Especialidad')
            <a href="{{ route('especialidades.index') }}" class="nav-item nav-link"><i class="bi bi-bookmark-star-fill me-2"></i>Especialidad</a>
            @endcan

            @can('Patologia')
            <a href="{{ route('patologias.index') }}" class="nav-item nav-link"><i class="bi bi-file-medical-fill me-2"></i>Patologias</a>
            @endcan

            @can('Paciente')
            <a href="{{ route('paciente.index') }}" class="nav-item nav-link"><i class="bi bi-person-fill me-2"></i>Paciente</a>
            @endcan

            @can('Procedencia')
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-geo-alt-fill me-2"></i>Procedencia</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="{{ route('estados.index') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-flag-fill me-2"></i>Estado</a>
                    <a href="{{ route('municipios.index') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-building me-2"></i>Municipio</a>
                    <a href="{{ route('parroquias.index') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-geo-fill me-2"></i>Parroquia</a>
                    <a href="{{ route('distritos.index') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-pin-fill me-2"></i>Distrito</a>
                </div>
            </div>
            @endcan

            @can('Planificación')
            <a href="{{ route('calendario.index') }}" class="nav-item nav-link"><i class="bi bi-calendar-event-fill me-2"></i>Planificación</a>
            @endcan

            @canany(['Citas', 'Morbilidad'])
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-calendar-check-fill me-2"></i>Citas</a>
                <div class="dropdown-menu bg-transparent border-0">
                    @can('Citas')
                    <a href="{{ route('Citas.create') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-plus-circle me-2"></i>Agendar Citas</a>
                    <!-- <a href="{{ route('Citas.index') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-list-check me-2"></i>Citas Agendadas</a> -->
                    @endcan
                    @can('Morbilidad')
                    <a href="{{ route('morbilidad.pendientes') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-person-check-fill me-2"></i>Atender Citas</a>
                   <!-- <a href="{{ route('diagnosticos.index') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-check2-all me-2"></i>Citas Atendidas</a> -->
                    
                    <a href="{{ route('morbilidad.index') }}" class="nav-item nav-link dropdown-item"><i class="bi bi-file-earmark-text me-2"></i>Reporte de Citas</a>
                    @endcan
                </div>
            </div>
            @endcanany

            @can('Reportes')
            <a href="{{route('reportes.index')}}" class="nav-link nav-item"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Reportes</a>
            @endcan
        </div>
    </nav>
</div>
