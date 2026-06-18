<table>
    <tr>
        <td colspan="7" style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">
            Morbilidad
        </td>
    </tr>
    <tr>
        <td colspan="7" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            @if($especialidad)
                <strong>Especialidad:</strong> {{ $especialidad }} &nbsp;|&nbsp;
            @endif
            <strong>Período:</strong> {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="7" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Paciente</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Cédula</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Fecha Cita</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Especialidad</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Médico</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Diagnóstico</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Observaciones</th>
    </tr>
    @foreach($morbilidades as $index => $m)
    <tr>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->paciente_nombre }} {{ $m->paciente_apellido }}</td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->paciente_cedula }}</td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ \Carbon\Carbon::parse($m->fecha_cita)->format('d/m/Y') }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->especialidad_nombre }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">Dr. {{ $m->medico_nombre }} {{ $m->medico_apellido }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">
            @php
                $diag = '';
                if (!empty($m->patologias_nombres)) {
                    $diag = $m->patologias_nombres;
                    if ($m->diagnostico_libre) {
                        $diag .= ' - ' . $m->diagnostico_libre;
                    }
                } else {
                    $diag = $m->diagnostico_libre ?: 'Sin diagnóstico';
                }
            @endphp
            {{ $diag }}
        </td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->cita_observacion ?: 'Asistió' }}</td>
    </tr>
    @endforeach
</table>
