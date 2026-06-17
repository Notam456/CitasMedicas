<table>
    <thead>
        <tr>
            <th>Especialidad</th>
            <th>Primera vez</th>
            <th>Sucesivas</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php $totales = ['primera' => 0, 'sucesivas' => 0, 'total' => 0]; @endphp
        @foreach($data as $row)
        <tr>
            <td>{{ $row['especialidad'] }}</td>
            <td>{{ $row['primera_vez'] }}</td>
            <td>{{ $row['sucesivas'] }}</td>
            <td>{{ $row['total'] }}</td>
        </tr>
        @php
            $totales['primera'] += $row['primera_vez'];
            $totales['sucesivas'] += $row['sucesivas'];
            $totales['total'] += $row['total'];
        @endphp
        @endforeach
        <tr style="font-weight: bold; background-color: #c3e6cb;">
            <td>TOTAL GENERAL</td>
            <td>{{ $totales['primera'] }}</td>
            <td>{{ $totales['sucesivas'] }}</td>
            <td>{{ $totales['total'] }}</td>
        </tr>
    </tbody>
</table>

<p><strong>Tipo de paciente:</strong> {{ $tipoPaciente == 'adulto' ? 'Mayores de 12 años' : 'Pediatría (12 años o menos)' }}</p>
<p><strong>Período:</strong> {{ $fechaTexto }}</p>
<p>Reporte generado: {{ now()->format('d/m/Y H:i:s') }}</p>