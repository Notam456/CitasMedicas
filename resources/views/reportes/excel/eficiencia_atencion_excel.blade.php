<table>
    <tr>
        <td style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">Eficiencia de Atención</td>
    </tr>
    <tr>
        <td style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;"><strong>Período:</strong> {{ $fechaTexto }}</td>
    </tr>
    <tr>
        <td style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">Reporte generado: {{ now()->format('d/m/Y H:i:s') }}</td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">#</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Mes</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Total Citas</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Atendidas</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Tasa Atención</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Canceladas</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Tasa Cancel.</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">1ª Vez</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">% 1ª Vez</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Control</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">% Control</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Hist. Traída</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">% Hist.</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Días Prom. Espera</th>
    </tr>
    @foreach($data as $index => $row)
    <tr>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $index + 1 }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['mes'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['total'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['atendidas'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['tasa_atencion'] / 100 }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['canceladas'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['tasa_cancelacion'] / 100 }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['primera_vez'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['pct_primera_vez'] / 100 }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['control'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['pct_control'] / 100 }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['historia_traida'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['pct_historia_traida'] / 100 }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['promedio_dias_espera'] }}</td>
    </tr>
    @endforeach
    <tr style="font-weight: bold; background-color: #A5D6A7;">
        <td style="padding: 10px; border: 1px solid #81C784; font-weight: bold;">TOTAL</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ count($data) }} mes(es)</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['total'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['atendidas'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['tasa_atencion'] / 100 }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['canceladas'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['tasa_cancelacion'] / 100 }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['primera_vez'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['pct_primera_vez'] / 100 }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['control'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['pct_control'] / 100 }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['historia_traida'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['pct_historia_traida'] / 100 }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['promedio_dias_espera'] }}</td>
    </tr>
</table>
