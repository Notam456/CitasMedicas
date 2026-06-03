<div class="modal fade" id="{{ $modal_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $modal_title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ $form_action }}" method="GET" target="_blank" id="form-{{ $modal_id }}">
                <div class="modal-body">
                    {{ $slot }}
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">PDF</button>
                    @isset($excel_action)
                        <button type="button" class="btn btn-success" onclick="exportExcel('{{ $excel_action }}', '{{ $modal_id }}')">Excel</button>
                    @endisset
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@isset($excel_action)
<script>
    function exportExcel(url, modalId) {
        var form = document.getElementById('form-' + modalId);
        if (!form) return;
        
        // Validar el formulario antes de exportar
        if (!form.checkValidity()) {
            // Mostrar mensajes de validación nativos del navegador
            form.reportValidity();
            return;
        }
        
        // Si es válido, construir la URL con los parámetros
        var formData = new FormData(form);
        var params = new URLSearchParams(formData).toString();
        window.open(url + '?' + params, '_blank');
    }
</script>
@endisset