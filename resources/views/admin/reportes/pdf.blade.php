<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #1F4E79; font-size: 16px; }
        h2 { color: #2E75B6; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #1F4E79; color: white; padding: 6px 8px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #EAF3FB; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSIDAD AUTONOMA GABRIEL RENE MORENO</h1>
        <h2>FACULTAD DE INGENIERIA EN CIENCIAS DE LA COMPUTACION Y TELECOMUNICACIONES</h2>
        <h2>{{ strtoupper($titulo) }} — Gestion {{ ucfirst($gestion->periodo) }} {{ $gestion->anio }}</h2>
        <p>Fecha de generacion: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if($datos->isNotEmpty())
                    @foreach(array_keys((array) $datos->first()) as $col)
                        <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $row)
            <tr>
                @foreach((array) $row as $val)
                    <td>{{ $val }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
