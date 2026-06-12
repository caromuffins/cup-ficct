<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>{{ $titulo }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #111; padding: 20px; }
    .header { background: #1F4E79; color: white; padding: 12px 16px;
              margin-bottom: 16px; }
    .header h1 { font-size: 14px; font-weight: bold; }
    .header p  { font-size: 9px; opacity: 0.85; margin-top: 2px; }
    .meta { font-size: 9px; color: #6b7280; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #1F4E79; color: white; padding: 5px 7px;
         text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.04em; }
    td { padding: 4px 7px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
    tr.even td { background: #f9fafb; }
    .footer { margin-top: 14px; font-size: 8px; color: #9ca3af; text-align: right; }
</style>
</head>
<body>

<div class="header">
    <h1>{{ $titulo }}</h1>
    @if($gestion)
    <p>Gestión {{ $gestion->anio }} &mdash; {{ ucfirst($gestion->periodo) }} &nbsp;|&nbsp; CUP FICCT &ndash; UAGRM</p>
    @else
    <p>CUP FICCT &ndash; UAGRM</p>
    @endif
</div>

<p class="meta">Generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</p>

@if($datos->isNotEmpty())
<table>
    <thead>
        <tr>
            @foreach(array_keys((array)$datos->first()) as $col)
            <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $i => $row)
        <tr class="{{ $i % 2 === 0 ? '' : 'even' }}">
            @foreach((array)$row as $val)
            <td>{{ $val ?? '—' }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
<p class="footer">{{ $datos->count() }} registros</p>
@else
<p style="color:#6b7280; padding:20px 0; text-align:center;">No hay datos para mostrar.</p>
@endif

</body>
</html>
