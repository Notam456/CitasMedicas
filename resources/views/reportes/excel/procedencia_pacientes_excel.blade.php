<table>
    <thead>
        <tr>
            <th>Distrito</th>
            <th>Municipio</th>
            <th>Citas Agendadas<br>(Pacientes únicos)</th>
            <th>Citas Atendidas<br>(Pacientes únicos)</th>
            <th>Total Pacientes*</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandTotal = ['agendadas' => 0, 'atendidas' => 0, 'todos' => 0];
        @endphp
        @foreach($reporteFinal as $distritoInfo)
            @foreach($distritoInfo['municipios'] as $municipio)
                <tr>
                    <td>{{ $distritoInfo['distrito'] }}</td>
                    <td>{{ $municipio['nombre'] }}</td>
                    <td>{{ $municipio['agendadas'] }}</td>
                    <td>{{ $municipio['atendidas'] }}</td>
                    <td>{{ $municipio['total'] }}</td>
                </tr>
            @endforeach
            @if(count($distritoInfo['municipios']) > 0)
                <tr>
                    <td colspan="2"><strong>Subtotal {{ $distritoInfo['distrito'] }}</strong></td>
                    <td><strong>{{ $distritoInfo['subtotal']['agendadas'] }}</strong></td>
                    <td><strong>{{ $distritoInfo['subtotal']['atendidas'] }}</strong></td>
                    <td><strong>{{ $distritoInfo['subtotal']['total'] }}</strong></td>
                </tr>
            @endif
            @php
                $grandTotal['agendadas'] += $distritoInfo['subtotal']['agendadas'];
                $grandTotal['atendidas'] += $distritoInfo['subtotal']['atendidas'];
                $grandTotal['todos'] += $distritoInfo['subtotal']['total'];
            @endphp
        @endforeach
        <tr>
            <td colspan="2"><strong>TOTAL GENERAL</strong></td>
            <td><strong>{{ $grandTotal['agendadas'] }}</strong></td>
            <td><strong>{{ $grandTotal['atendidas'] }}</strong></td>
            <td><strong>{{ $grandTotal['todos'] }}</strong></td>
        </tr>
    </tbody>
</table>
<p><small>* Total Pacientes: pacientes únicos con al menos una cita (Agendada o Atendida) en el período.</small></p>
<p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
<p>Rango de fechas: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}</p>