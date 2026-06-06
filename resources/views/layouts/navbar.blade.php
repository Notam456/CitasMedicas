<div class="content">
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
        <a href="{{ route("dashboard") }}" class="navbar-brand d-flex d-lg-none me-4">
            <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
        </a>
        <a href="#" class="sidebar-toggler flex-shrink-0">
            <i class="fa fa-bars"></i>
        </a>
        <div class="navbar-nav align-items-center ms-auto">
            <!-- <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa fa-envelope me-lg-2"></i>
                <span class="d-none d-lg-inline-flex">Mensajes</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                <a href="#" class="dropdown-item">
                    <div class="d-flex align-items-center">
                        <div class="ms-2">
                            <h6 class="fw-normal mb-0">Te han enviado mensajes</h6>
                            <small>Hace 15 minutos</small>
                        </div>
                    </div>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item text-center">Ver todos los mensajes</a>
            </div>
        </div> -->
            @if(!request()->routeIs('notificaciones.index'))
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle position-relative" data-bs-toggle="dropdown" id="notifDropdown">
                    <i class="fa fa-bell me-lg-2"></i>
                    <span class="d-none d-lg-inline-flex">Notificaciones</span>
                    <span id="notif-count" class="badge rounded-pill bg-danger" style="display:none; position:absolute; top:2px; right:2px; font-size:0.65rem; min-width:18px;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0" style="min-width: 320px;" id="notif-dropdown-menu">
                    <div id="notif-lista">
                        <div class="text-center py-3 text-muted small">
                            <i class="bi bi-bell-slash"></i> Cargando notificaciones...
                        </div>
                    </div>
                    <hr class="dropdown-divider my-1">
                    <a href="{{ route('notificaciones.index') }}" class="dropdown-item text-center small">
                        Ver todas las notificaciones
                    </a>
                </div>
            </div>
            @endif
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img class="rounded-circle me-lg-2" src="{{ asset('assets/img/user.jpg') }}" alt=""
                        style="width: 40px; height: 40px;">
                    <span class="d-none d-lg-inline-flex"> @auth {{ Auth::user()->name }}@endauth </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link align-middle p-0 m-0 border-0">
                                <i class="bi bi-box-arrow-left me-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
