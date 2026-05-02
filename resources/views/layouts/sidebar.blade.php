<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="{{route('dashboard')}}" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary">SAGECIM</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
        </div>
        <div class="navbar-nav w-100">
            <a href="{{ route('dashboard') }}" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="{{ route('users.index') }}" class="nav-item nav-link "><i class="fa fa-th me-2"></i>Usuarios</a>
            <a href="{{ route('medicos.index') }}" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Médicos</a>
            <a href="{{ route('especialidades.index') }}" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Especialidad</a>
            <a href="{{ route('paciente.index') }}" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Paciente</a>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-map me-2"></i>Procedencia</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="{{ route('estados.index') }}" class="nav-item nav-link dropdown-item">Estado</a>
                    <a href="{{ route('municipios.index') }}" class="nav-item nav-link dropdown-item">Municipio</a>
                    <a href="{{ route('parroquias.index') }}" class="nav-item nav-link dropdown-item">Parroquia</a>
                </div>
            </div>
            
            <a href="#" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Planificación</a>
            <a href="{{ route('Citas.index') }}" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Agenda de Cita</a>
            <a href="{{route('reportes.index')}}" class="nav-link nav-item"><i class="far fa-file-alt me-2"></i>Reportes</a>
        </div>
    </nav>
</div>