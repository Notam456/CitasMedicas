<div class="modal fade" id="modalEditarDiagnostico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Editar Cita (Diagnóstico y Patologías)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf @method('PUT')
                <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
                    <input type="hidden" id="edit_id" name="id">

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title text-primary">Información de la Cita</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Paciente:</strong> <span id="edit_info_paciente"></span></p>
                                    <p class="mb-1"><strong>Cédula:</strong> <span id="edit_info_cedula"></span></p>
                                    <p class="mb-0"><strong>Fecha de cita:</strong> <span id="edit_info_fecha"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Médico:</strong> <span id="edit_info_medico"></span></p>
                                    <p class="mb-0"><strong>Especialidad:</strong> <span id="edit_info_especialidad"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Diagnóstico libre (impresión diagnóstica)</label>
                        <textarea name="diagnostico_libre" id="edit_diagnostico_libre" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Patologías diagnosticadas</label>
                        <div id="edit_patologias_container"></div>
                        <button type="button" class="btn btn-sm btn-secondary mt-1" id="edit_add_patologia"><i class="bi bi-plus-circle"></i> Agregar patología</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function addEditPatologiaRow(selectedId) {
    const container = $('#edit_patologias_container');
    const row = $(`
        <div class="input-group mb-2 patologia-edit-item">
            <select name="patologias[]" class="form-select">
                <option value="">Seleccione una patología</option>
            </select>
            <button type="button" class="btn btn-outline-danger btn-remove-edit-patologia"><i class="bi bi-trash"></i></button>
        </div>
    `);
    const select = row.find('select');
    (window.patologiasDisponibles || []).forEach(function(pat) {
        select.append('<option value="' + pat.id + '"' + (pat.id == selectedId ? ' selected' : '') + '>' + pat.nombre + '</option>');
    });
    container.append(row);
}

function limpiarContenedoresEdicion() {
    $('#edit_patologias_container').empty();
}

$(document).on('click', '#edit_add_patologia', function() { addEditPatologiaRow(null); });
$(document).on('click', '.btn-remove-edit-patologia', function() { $(this).closest('.patologia-edit-item').remove(); });

$('#modalEditarDiagnostico').on('hidden.bs.modal', function() {
    limpiarContenedoresEdicion();
});
</script>
@endpush
