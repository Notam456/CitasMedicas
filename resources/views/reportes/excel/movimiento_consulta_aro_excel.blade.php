@php
    $totalCols = 4;
@endphp
<table>
    <tr>
        <td colspan="{{ $totalCols }}" style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">
            {{ $titulo }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalCols }}" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            <strong> Período: </strong> {{ $fechaTexto }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalCols }}" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Mes</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">&lt;13 Sem (1ra Vez)</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Adolesc 10-19 (1ra Vez)</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Total Consultas Aro</th>
    </tr>
    @foreach($data as $index => $row)
    <tr>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m', $row['mes'])->locale('es')->translatedFormat('F Y') }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ $row['menos_13_sem'] }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ $row['adolescentes'] }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ $row['total_consultas'] }}</td>
    </tr>
    @endforeach
    <tr style="background-color: #c3e6cb; font-weight: bold;">
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9;"><strong>TOTAL GENERAL</strong></td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9;"><strong>{{ $totales['menos_13_sem'] }}</strong></td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9;"><strong>{{ $totales['adolescentes'] }}</strong></td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9;"><strong>{{ $totales['total_consultas'] }}</strong></td>
    </tr>
</table>
