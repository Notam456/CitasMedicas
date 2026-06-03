<table>
    <thead>
        <tr>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Cédula</th>
            <th>Teléfono</th>
            <th>Especialidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($medicos as $medico)
        <tr>
            <td>{{ $medico->nombre }}</td>
            <td>{{ $medico->apellido }}</td>
            <td>{{ $medico->cedula }}</td>
            <td>{{ $medico->telefono }}</td>
            <td>{{ $medico->especialidad->nombre ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p>
    <strong>Reporte generado el:</strong> {{ now()->format('d/m/Y H:i:s') }}<br>
    @if($especialidad)
        <strong>Especialidad:</strong> {{ $especialidad->nombre }}
    @else
        <strong>Especialidad:</strong> Todas
    @endif
</p>