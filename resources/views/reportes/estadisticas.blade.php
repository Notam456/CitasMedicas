<section id="estadistica-section" class="mt-4 mb-4">
    <div class="bg-light rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Resumen Estadístico</h5>
            <small class="text-muted">Datos del mes actual</small>
        </div>

        <div id="estadistica-loading" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="text-muted mt-2 mb-0">Cargando estadísticas...</p>
        </div>

        <div id="estadistica-error" class="alert alert-warning d-none" role="alert">
            No se pudieron cargar las estadísticas en este momento.
        </div>

        <div id="estadistica-contenido" class="d-none">
            <div id="estadistica-tablas" class="row g-3 mb-4"></div>
            <div id="estadistica-charts" class="row g-3"></div>
        </div>
    </div>
</section>
