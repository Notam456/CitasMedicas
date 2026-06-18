<table>
    <tr>
        <td colspan="8" style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">
            25 Principales Causas de Consulta Externa
        </td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            <strong>Período:</strong> {{ $fechaTexto }}
        </td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">#</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Patología (Diagnóstico)</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Especialidad</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;" colspan="2">Masculino</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;" colspan="2">Femenino</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 11px; padding: 10px 6px; text-align: center; border: 1px solid #1B5E20;">Total</th>
    </tr>
    <tr>
        <th style="background-color: #388E3C; color: #FFFFFF; font-weight: bold; font-size: 10px; padding: 8px 4px; text-align: center; border: 1px solid #1B5E20;"></th>
        <th style="background-color: #388E3C; color: #FFFFFF; font-weight: bold; font-size: 10px; padding: 8px 4px; text-align: center; border: 1px solid #1B5E20;"></th>
        <th style="background-color: #388E3C; color: #FFFFFF; font-weight: bold; font-size: 10px; padding: 8px 4px; text-align: center; border: 1px solid #1B5E20;"></th>
        <th style="background-color: #388E3C; color: #FFFFFF; font-weight: bold; font-size: 10px; padding: 8px 4px; text-align: center; border: 1px solid #1B5E20;">1ra Vez</th>
        <th style="background-color: #388E3C; color: #FFFFFF; font-weight: bold; font-size: 10px; padding: 8px 4px; text-align: center; border: 1px solid #1B5E20;">Sucesivas</th>
        <th style="background-color: #388E3C; color: #FFFFFF; font-weight: bold; font-size: 10px; padding: 8px 4px; text-align: center; border: 1px solid #1B5E20;">1ra Vez</th>
        <th style="background-color: #388E3C; color: #FFFFFF; font-weight: bold; font-size: 10px; padding: 8px 4px; text-align: center; border: 1px solid #1B5E20;">Sucesivas</th>
        <th style="background-color: #388E3C; color: #FFFFFF; font-weight: bold; font-size: 10px; padding: 8px 4px; text-align: center; border: 1px solid #1B5E20;"></th>
    </tr>
    @php $totales = ['m1' => 0, 'ms' => 0, 'f1' => 0, 'fs' => 0, 'total' => 0]; @endphp
    @foreach($data as $index => $row)
    <tr>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $index + 1 }}</td>
        <td style="padding: 8px 4px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['patologia'] }}</td>
        <td style="padding: 8px 4px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['especialidad'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['masculino_primera'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['masculino_sucesivas'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['femenino_primera'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['femenino_sucesivas'] }}</td>
        <td style="padding: 8px 4px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['total'] }}</td>
    </tr>
    @php
        $totales['m1'] += $row['masculino_primera'];
        $totales['ms'] += $row['masculino_sucesivas'];
        $totales['f1'] += $row['femenino_primera'];
        $totales['fs'] += $row['femenino_sucesivas'];
        $totales['total'] += $row['total'];
    @endphp
    @endforeach
    <tr style="font-weight: bold; background-color: #A5D6A7;">
        <td colspan="3" style="padding: 10px; border: 1px solid #81C784; font-weight: bold; text-align: center;">TOTAL GENERAL</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['m1'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['ms'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['f1'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['fs'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['total'] }}</td>
    </tr>
</table>
