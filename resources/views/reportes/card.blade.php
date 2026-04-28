<div class="col-md-6 col-xl-4">
    <div class="bg-light rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">{{$card_title}}</h5>
            <i class="bi bi-printer fa-2x text-primary"></i>
        </div>
        <p class="mb-3">{{$card_desc}}</p>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="{{$reporte_bs_target}}">
                Generar Reporte
            </button>
            
            <a href="{{$reporte_excel}}" class="btn btn-success">
                Exportar a Excel
            </a>
        </div>
    </div>
</div>