<table>
    <tr>
        <td style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">Inasistencias en Citas</td>
    </tr>
    <tr>
        <td style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;"><strong>Período:</strong> {{ $fechaTexto }}</td>
    </tr>
    <tr>
        <td style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">Reporte generado: {{ now()->format('d/m/Y H:i:s') }}</td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">#</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Especialidad</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Aus. Pac. Cant.</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Aus. Pac. %</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Aus. Méd. Cant.</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Aus. Méd. %</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Canceladas sin Motivo</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Total Inasist.</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Citas Cerradas</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Pendientes</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Tasa Inasis.</th>
    </tr>
    @foreach($data as $index => $row)
    <tr>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $index + 1 }}</td>
        <td style="padding: 8px 4px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['especialidad'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['ausencia_paciente'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['ausencia_paciente_pct'] / 100 }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['ausencia_medico'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['ausencia_medico_pct'] / 100 }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['sin_motivo'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['total_inasistencias'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['total_citas'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['pendientes'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['tasa_inasistencia'] / 100 }}</td>
    </tr>
    @endforeach
    <tr style="font-weight: bold; background-color: #A5D6A7;">
        <td colspan="2" style="padding: 10px; border: 1px solid #81C784; font-weight: bold;">TOTAL GENERAL</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['ausencia_paciente'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['ausencia_paciente_pct'] / 100 }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['ausencia_medico'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['ausencia_medico_pct'] / 100 }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['sin_motivo'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['total_inasistencias'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['total_citas'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['pendientes'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['tasa_inasistencia'] / 100 }}</td>
    </tr>
</table>
<p style="font-size: 9px; color: #888888;"><em>* La tasa se calcula sobre citas cerradas (atendidas + canceladas); las pendientes se informan aparte. "Ausencia Médico" incluye las citas canceladas automáticamente por suspensión del médico sin suplente.</em></p>
