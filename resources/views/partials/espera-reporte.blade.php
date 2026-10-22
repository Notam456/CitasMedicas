<style>
    .espera-reporte-overlay {
        position: fixed;
        inset: 0;
        z-index: 1090;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(15, 23, 42, .6);
        outline: none;
    }

    .espera-reporte-overlay.activo {
        display: flex;
    }

    .espera-reporte-card {
        width: 100%;
        max-width: 360px;
        padding: 1.5rem;
        border-radius: .75rem;
        background: #fff;
        color: #212529;
        text-align: center;
        box-shadow: 0 .75rem 2rem rgba(0, 0, 0, .25);
    }

    html.dark .espera-reporte-card {
        background: #1f2937;
        color: #f8f9fa;
    }

    html.dark .espera-reporte-card .text-muted {
        color: #adb5bd !important;
    }

    .espera-reporte-progreso {
        height: 8px;
        overflow: hidden;
        border-radius: .25rem;
        background-color: #e9ecef;
    }

    html.dark .espera-reporte-progreso {
        background-color: #374151;
    }

    .espera-reporte-barra {
        width: 40%;
        height: 100%;
        border-radius: .25rem;
        background-color: #198754;
        animation: esperaReporteBarra 1.4s ease-in-out infinite;
        will-change: transform;
    }

    @keyframes esperaReporteBarra {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(250%);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .espera-reporte-barra {
            width: 100%;
            animation: none;
        }
    }

    .espera-reporte-aviso {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        z-index: 1095;
        display: none;
        max-width: 360px;
        padding: .75rem 2.25rem .75rem .9rem;
        border-radius: .5rem;
        background: #ffc107;
        color: #212529;
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .2);
    }

    .espera-reporte-aviso.activo {
        display: block;
    }

    .espera-reporte-aviso button {
        position: absolute;
        top: .35rem;
        right: .5rem;
        border: 0;
        background: transparent;
        color: #212529;
        font-size: 1.2rem;
        line-height: 1;
        cursor: pointer;
    }
</style>

<div id="esperaReporteOverlay" class="espera-reporte-overlay" role="dialog" aria-modal="true"
    aria-labelledby="esperaReporteTitulo" aria-describedby="esperaReporteDescripcion" aria-hidden="true" tabindex="-1">
    <div class="espera-reporte-card">
        <div class="spinner-border text-success mb-3" style="width: 2.5rem; height: 2.5rem;" role="status"
            aria-hidden="true"></div>
        <h6 class="mb-2" id="esperaReporteTitulo">Generando reporte</h6>
        <p class="small text-muted mb-3" id="esperaReporteDescripcion">
            Esto puede tardar según la cantidad de registros.<br>
            No cierres ni recargues esta ventana.
        </p>
        <div class="espera-reporte-progreso" role="progressbar" aria-label="Generando reporte"
            aria-valuetext="Generando">
            <div class="espera-reporte-barra"></div>
        </div>
        <div class="small text-muted mt-2">
            Tiempo transcurrido: <span id="esperaReporteSegundos">0</span> s
        </div>
    </div>
</div>

<div id="esperaReporteAviso" class="espera-reporte-aviso" role="alert" aria-live="assertive">
    <span id="esperaReporteAvisoTexto"></span>
    <button type="button" id="esperaReporteAvisoCerrar" aria-label="Cerrar aviso">&times;</button>
</div>

<script>
    (function () {
        var overlay = document.getElementById('esperaReporteOverlay');
        var segundosElemento = document.getElementById('esperaReporteSegundos');
        var aviso = document.getElementById('esperaReporteAviso');
        var avisoTexto = document.getElementById('esperaReporteAvisoTexto');
        var avisoCerrar = document.getElementById('esperaReporteAvisoCerrar');

        if (!overlay || !segundosElemento || !aviso || !avisoTexto) {
            return;
        }

        var activo = false;
        var token = null;
        var inicio = 0;
        var tickTimer = null;
        var timeoutTimer = null;
        var avisoTimer = null;

        function crearToken() {
            return (Date.now().toString(36) + Math.random().toString(36).slice(2, 8)).replace(/[^a-z0-9]/g, '');
        }

        function limpiarTemporizadores() {
            clearInterval(tickTimer);
            clearTimeout(timeoutTimer);
            clearTimeout(avisoTimer);
            tickTimer = null;
            timeoutTimer = null;
            avisoTimer = null;
        }

        function limpiarCookie() {
            if (token) {
                document.cookie = 'reporte_descarga_' + token + '=; Max-Age=0; path=/; SameSite=Lax';
            }
        }

        function mostrarAviso(mensaje) {
            avisoTexto.textContent = mensaje;
            aviso.classList.add('activo');
            clearTimeout(avisoTimer);
            avisoTimer = setTimeout(function () {
                aviso.classList.remove('activo');
            }, 10000);
        }

        function cookieLista() {
            return document.cookie.split('; ').some(function (cookie) {
                return cookie.indexOf('reporte_descarga_' + token + '=') === 0;
            });
        }

        function finalizar() {
            if (!activo) {
                return;
            }

            activo = false;
            limpiarTemporizadores();
            limpiarCookie();
            overlay.classList.remove('activo');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            window.reporteEnProceso = false;
            token = null;
        }

        window.mostrarEsperaReporte = function (iniciarDescarga) {
            if (activo || window.reporteEnProceso) {
                return;
            }

            activo = true;
            window.reporteEnProceso = true;
            token = crearToken();
            inicio = Date.now();
            segundosElemento.textContent = '0';

            overlay.classList.add('activo');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            overlay.focus();

            var iniciada;

            try {
                iniciada = iniciarDescarga(token);
            } catch (error) {
                finalizar();
                mostrarAviso('No se pudo iniciar la descarga del reporte.');
                return;
            }

            if (iniciada === false) {
                finalizar();
                mostrarAviso('El navegador bloqueó la descarga. Permite las ventanas emergentes e intenta de nuevo.');
                return;
            }

            tickTimer = setInterval(function () {
                segundosElemento.textContent = Math.floor((Date.now() - inicio) / 1000);

                if (cookieLista()) {
                    finalizar();
                }
            }, 1000);

            timeoutTimer = setTimeout(function () {
                finalizar();
                mostrarAviso('El reporte está tardando más de lo esperado. Revisa la pestaña de descargas o intenta con filtros más específicos.');
            }, 5 * 60 * 1000);
        };

        if (avisoCerrar) {
            avisoCerrar.addEventListener('click', function () {
                aviso.classList.remove('activo');
            });
        }
    })();
</script>
