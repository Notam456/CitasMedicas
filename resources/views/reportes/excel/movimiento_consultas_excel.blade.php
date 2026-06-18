<table>
    <tr>
        <td colspan="4" style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">
            Movimiento de Consulta Externa
        </td>
    </tr>
    <tr>
        <td colspan="4" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            <strong>Tipo de paciente:</strong> {{ $tipoPaciente == 'adulto' ? 'Mayores de 12 años' : 'Pediatría (12 años o menos)' }} &nbsp;|&nbsp; <strong>Período:</strong> {{ $fechaTexto }}
        </td>
    </tr>
    <tr>
        <td colspan="4" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Especialidad</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Primera vez</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Sucesivas</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Total</th>
    </tr>
    @php $totales = ['primera' => 0, 'sucesivas' => 0, 'total' => 0]; @endphp
    @foreach($data as $index => $row)
    <tr>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['especialidad'] }}</td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['primera_vez'] }}</td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['sucesivas'] }}</td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['total'] }}</td>
    </tr>
    @php
        $totales['primera'] += $row['primera_vez'];
        $totales['sucesivas'] += $row['sucesivas'];
        $totales['total'] += $row['total'];
    @endphp
    @endforeach
    <tr style="font-weight: bold; background-color: #A5D6A7;">
        <td style="padding: 10px; border: 1px solid #81C784; font-weight: bold;">TOTAL GENERAL</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['primera'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['sucesivas'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales['total'] }}</td>
    </tr>
</table>
