@extends('layouts.template')
@section('title','Inicio de Sesión | SAGECIM')

@push('body-class', 'login-page')

<div class="login-wrapper">
    <!-- Panel izquierdo: Branding con Carrusel -->
    <div class="login-brand">
        <div id="loginCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('assets/img/carousel-1.jpg') }}" alt="Agenda de Citas">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-caption-custom">
                        <div class="carousel-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Agenda tus Citas</h3>
                        <p>Reserva y gestiona tus citas médicas de forma rápida y sencilla</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('assets/img/carousel-2.jpg') }}" alt="Atención Médica">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-caption-custom">
                        <div class="carousel-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h3>Atención Médica</h3>
                        <p>Registra diagnósticos y gestiona la atención de pacientes diariamente</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('assets/img/carousel-3.jpg') }}" alt="Reportes Estadísticos">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-caption-custom">
                        <div class="carousel-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Reportes Estadísticos</h3>
                        <p>Genera informes de morbilidad, procedencia y causas de consulta</p>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#loginCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#loginCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>

            <div class="carousel-indicators" id="loginIndicators">
                <button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
                <button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="2"></button>
            </div>
        </div>

        <div class="brand-footer">
            <div class="brand-name">SAGECIM</div>
            <div class="brand-tagline">Sistema de Agenda de Citas Médicas</div>
        </div>
    </div>

    <!-- Panel derecho: Formulario -->
    <div class="login-form-panel">
        <div class="login-card">
            <h2>Bienvenido</h2>
            <p class="subtitle">Ingresa tus credenciales para acceder al sistema</p>

            @if ($errors->any())
                <div class="login-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('iniciar-sesion') }}">
                @csrf

                <div class="input-group-login">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text"
                        name="name"
                        class="form-control login-input"
                        placeholder="Usuario"
                        value="{{ old('name') }}"
                        required
                        autocomplete="username">
                </div>

                <div class="input-group-login">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password"
                        name="password"
                        id="password"
                        class="form-control login-input"
                        placeholder="Contraseña"
                        required
                        autocomplete="current-password">
                    <button type="button" class="btn-toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-arrow-right-to-bracket me-2"></i>Iniciar Sesión
                </button>
            </form>

            <div class="login-footer">
                SAGECIM &copy; {{ date('Y') }}
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
function togglePassword() {
    var input = document.getElementById('password');
    var icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
