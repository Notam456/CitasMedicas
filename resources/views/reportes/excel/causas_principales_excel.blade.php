<table>
    <thead>
        <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">Patología (Diagnóstico)</th>
            <th rowspan="2">Especialidad</th>
            <th colspan="2">Masculino</th>
            <th colspan="2">Femenino</th>
            <th rowspan="2">Total</th>
        </tr>
        <tr>
            <th>1ra Vez</th>
            <th>Sucesivas</th>
            <th>1ra Vez</th>
            <th>Sucesivas</th>
        </tr>
    </thead>
    <tbody>
        @php $totales = ['m1' => 0, 'ms' => 0, 'f1' => 0, 'fs' => 0, 'total' => 0]; @endphp
        @foreach($data as $index => $row)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $row['patologia'] }}</td>
            <td>{{ $row['especialidad'] }}</td>
            <td>{{ $row['masculino_primera'] }}</td>
            <td>{{ $row['masculino_sucesivas'] }}</td>
            <td>{{ $row['femenino_primera'] }}</td>
            <td>{{ $row['femenino_sucesivas'] }}</td>
            <td>{{ $row['total'] }}</td>
        </tr>
        @php
            $totales['m1'] += $row['masculino_primera'];
            $totales['ms'] += $row['masculino_sucesivas'];
            $totales['f1'] += $row['femenino_primera'];
            $totales['fs'] += $row['femenino_sucesivas'];
            $totales['total'] += $row['total'];
        @endphp
        @endforeach
        <tr style="font-weight: bold; background-color: #c3e6cb;">
            <td colspan="3">TOTAL GENERAL</td>
            <td>{{ $totales['m1'] }}</td>
            <td>{{ $totales['ms'] }}</td>
            <td>{{ $totales['f1'] }}</td>
            <td>{{ $totales['fs'] }}</td>
            <td>{{ $totales['total'] }}</td>
        </tr>
    </tbody>
</table>

<p><strong>Período:</strong> {{ $fechaTexto }}</p>
<p>Reporte generado: {{ now()->format('d/m/Y H:i:s') }}</p>