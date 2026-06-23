<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cita</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1 { color: #20356B; text-align: center; font-size: 18px; margin: 10px 0; }
        h2 { color: #20356B; font-size: 14px; margin: 15px 0 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 5px 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; width: 30%; }
        .info-table td:first-child { font-weight: bold; width: 30%; background-color: #f9f9f9; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; }
        .fecha { text-align: right; font-size: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    @if(isset($membrete) && file_exists($membrete))
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>Reporte de Cita</h1>
    </div>

    <div class="fecha">
        <p>Fecha del reporte: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <h2>Información del Paciente</h2>
    <table class="info-table">
        <tr><td>Paciente</td><td>{{ $cita->paciente->nombre }} {{ $cita->paciente->apellido }}</td></tr>
        <tr><td>Cédula</td><td>{{ $cita->paciente->cedula }}</td></tr>
        <tr><td>Teléfono</td><td>{{ $cita->paciente->telefono ?? 'N/E' }}</td></tr>
        <tr><td>Dirección</td><td>{{ $cita->paciente->direccion ?? 'N/E' }}</td></tr>
    </table>

    <h2>Información de la Cita</h2>
    <table class="info-table">
        <tr><td>Fecha de la Cita</td><td>{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</td></tr>
        <tr><td>Especialidad</td><td>{{ $cita->medico->especialidad->nombre }}</td></tr>
        <tr><td>Médico</td><td>Dr. {{ $cita->medico->nombre }} {{ $cita->medico->apellido }}</td></tr>
        <tr><td>Estado</td><td>{{ $cita->estado }}</td></tr>
        <tr><td>Tipo de Paciente</td><td>{{ $cita->tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($cita->tipo_paciente === 'control' ? 'Control' : 'Orden Médica') }}</td></tr>
        <tr><td>Observaciones</td><td>{{ $cita->observacion ?? 'Ninguna' }}</td></tr>
    </table>

    <h2>Diagnóstico</h2>
    <table class="info-table">
        <tr><td>Diagnóstico libre</td><td>{{ $cita->diagnostico_libre ?? 'No registrado' }}</td></tr>
        <tr><td>Patologías</td>
            <td>
                @if($cita->patologias->count())
                    {{ $cita->patologias->pluck('nombre')->implode(', ') }}
                @else
                    Ninguna
                @endif
            </td>
        </tr>
    </table>

    <h2>Atención</h2>
    <table class="info-table">
        <tr><td>Atendido por</td><td>{{ $cita->atendidoPor->name ?? 'No asignado' }}</td></tr>
        <tr><td>Fecha de registro</td><td>{{ \Carbon\Carbon::parse($cita->created_at)->format('d/m/Y H:i') }}</td></tr>
    </table>
</body>
</html>
