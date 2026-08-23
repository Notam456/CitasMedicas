<table>
    <tr>
        <td style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">Citas sin Diagnóstico</td>
    </tr>
    <tr>
        <td style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;"><strong>Período:</strong> {{ $fechaTexto }} | <strong>Especialidad:</strong> {{ $especialidadNombre }}</td>
    </tr>
    <tr>
        <td style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">Reporte generado: {{ now()->format('d/m/Y H:i:s') }}</td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">#</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Médico</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Especialidad</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Paciente</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Cédula</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Fecha Cita</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Observación</th>
    </tr>
    @foreach($data as $index => $row)
    <tr>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $index + 1 }}</td>
        <td style="padding: 8px 4px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['medico'] }}</td>
        <td style="padding: 8px 4px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['especialidad'] }}</td>
        <td style="padding: 8px 4px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['paciente'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['cedula'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['fecha_cita'] }}</td>
        <td style="padding: 8px 4px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['observacion'] ?? '-' }}</td>
    </tr>
    @endforeach
    <tr style="font-weight: bold; background-color: #A5D6A7;">
        <td style="padding: 10px; border: 1px solid #81C784; font-weight: bold;">TOTAL</td>
        <td colspan="5" style="padding: 10px; border: 1px solid #81C784;"></td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['sin_diagnostico'] }} citas</td>
    </tr>
</table>
