<table>
    <tr>
        <td colspan="5" style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">
            Médicos por Especialidad
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            @if($especialidad)
                <strong>Especialidad:</strong> {{ $especialidad->nombre }}
            @else
                <strong>Especialidad:</strong> Todas
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Nombres</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Apellidos</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Cédula</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Teléfono</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Especialidad</th>
    </tr>
    @foreach($medicos as $index => $medico)
    <tr>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $medico->nombre }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $medico->apellido }}</td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $medico->cedula }}</td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $medico->telefono }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $medico->especialidad->nombre ?? 'N/A' }}</td>
    </tr>
    @endforeach
</table>
