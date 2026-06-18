<table>
    <tr>
        <td colspan="5" style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">
            Procedencia de Pacientes
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            <strong>Período:</strong> {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Distrito</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Municipio</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Citas Agendadas</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Citas Atendidas</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Total Pacientes</th>
    </tr>
    @php
        $grandTotal = ['agendadas' => 0, 'atendidas' => 0, 'todos' => 0];
        $globalIndex = 0;
    @endphp
    @foreach($reporteFinal as $distritoInfo)
        @foreach($distritoInfo['municipios'] as $municipio)
        <tr>
            <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $globalIndex % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $distritoInfo['distrito'] }}</td>
            <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $globalIndex % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $municipio['nombre'] }}</td>
            <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $globalIndex % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $municipio['agendadas'] }}</td>
            <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $globalIndex % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $municipio['atendidas'] }}</td>
            <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $globalIndex % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $municipio['total'] }}</td>
        </tr>
        @php $globalIndex++; @endphp
        @endforeach
        @if(count($distritoInfo['municipios']) > 0)
        <tr>
            <td colspan="2" style="padding: 10px; border: 1px solid #81C784; font-weight: bold; background-color: #C8E6C9;"><strong>Subtotal {{ $distritoInfo['distrito'] }}</strong></td>
            <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold; background-color: #C8E6C9;"><strong>{{ $distritoInfo['subtotal']['agendadas'] }}</strong></td>
            <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold; background-color: #C8E6C9;"><strong>{{ $distritoInfo['subtotal']['atendidas'] }}</strong></td>
            <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold; background-color: #C8E6C9;"><strong>{{ $distritoInfo['subtotal']['total'] }}</strong></td>
        </tr>
        @endif
        @php
            $grandTotal['agendadas'] += $distritoInfo['subtotal']['agendadas'];
            $grandTotal['atendidas'] += $distritoInfo['subtotal']['atendidas'];
            $grandTotal['todos'] += $distritoInfo['subtotal']['total'];
        @endphp
    @endforeach
    <tr style="font-weight: bold; background-color: #A5D6A7;">
        <td colspan="2" style="padding: 10px; border: 1px solid #81C784; font-weight: bold;">TOTAL GENERAL</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $grandTotal['agendadas'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $grandTotal['atendidas'] }}</td>
        <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $grandTotal['todos'] }}</td>
    </tr>
</table>
<p style="font-size: 9px; color: #888888;"><em>* Total Pacientes: pacientes únicos con al menos una cita (Agendada o Atendida) en el período.</em></p>
