<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Detalles de la Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Información del Paciente</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Paciente:</strong> <span id="show_paciente"></span></p>
                                <p class="mb-1"><strong>Cédula:</strong> <span id="show_cedula"></span></p>
                                <p class="mb-0"><strong>Teléfono:</strong> <span id="show_telefono"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Dirección:</strong> <span id="show_direccion"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Información de la Cita</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Médico:</strong> <span id="show_medico"></span></p>
                                <p class="mb-1"><strong>Especialidad:</strong> <span id="show_especialidad"></span></p>
                                <p class="mb-0"><strong>Fecha Cita:</strong> <span id="show_fecha_cita"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Estado:</strong> <span id="show_estado"></span></p>
                                <p class="mb-1"><strong>Tipo:</strong> <span id="show_tipo"></span></p>
                                <p class="mb-0"><strong>Observaciones:</strong> <span id="show_observaciones"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Diagnóstico</h6>
                        <p class="mb-1"><strong>Diagnóstico libre:</strong> <span id="show_diagnostico_libre"></span></p>
                        <p class="mb-0"><strong>Patologías:</strong> <span id="show_patologias"></span></p>
                    </div>
                </div>
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Atención</h6>
                        <p class="mb-1"><strong>Atendido por:</strong> <span id="show_atendido_por"></span></p>
                        <p class="mb-0"><strong>Fecha registro:</strong> <span id="show_fecha_registro"></span></p>
                    </div>
                </div>
            </div>
            @if($showPdf ?? false)
            <div class="text-end px-3 pb-3">
                <a href="#" id="btnPdfCita" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Reporte PDF
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function populateShowModal(cita, pdfUrl) {
    document.getElementById('show_paciente').innerText = `${cita.paciente.nombre} ${cita.paciente.apellido}`;
    document.getElementById('show_cedula').innerText = cita.paciente.cedula;
    document.getElementById('show_telefono').innerText = cita.paciente.telefono || 'N/E';
    document.getElementById('show_direccion').innerText = cita.paciente.direccion || 'N/E';
    document.getElementById('show_medico').innerText = `Dr. ${cita.medico.nombre} ${cita.medico.apellido}`;
    document.getElementById('show_especialidad').innerText = cita.medico.especialidad.nombre;
    document.getElementById('show_fecha_cita').innerText = new Date(cita.fecha_cita).toLocaleDateString();
    document.getElementById('show_estado').innerText = cita.estado;
    document.getElementById('show_tipo').innerText = cita.tipo_paciente === 'primera_vez' ? 'Primera Vez' : cita.tipo_paciente === 'control' ? 'Control' : 'Orden Médica';
    document.getElementById('show_observaciones').innerText = cita.observacion || 'Ninguna';
    document.getElementById('show_diagnostico_libre').innerText = cita.diagnostico_libre || 'No registrado';
    document.getElementById('show_atendido_por').innerText = cita.atendido_por ? cita.atendido_por.name : 'No asignado';
    document.getElementById('show_fecha_registro').innerText = new Date(cita.created_at).toLocaleString();

    const patologiasSpan = document.getElementById('show_patologias');
    if (cita.patologias && cita.patologias.length) {
        patologiasSpan.innerText = cita.patologias.map(p => p.nombre).join(', ');
    } else {
        patologiasSpan.innerText = 'Ninguna';
    }

    if (pdfUrl) {
        document.getElementById('btnPdfCita').href = pdfUrl;
    }
}
</script>
@endpush
