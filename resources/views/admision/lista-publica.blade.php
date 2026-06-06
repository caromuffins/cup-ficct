<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Admitidos — CUP FICCT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <div class="bg-blue-900 text-white py-8">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h1 class="text-3xl font-bold mb-2">UNIVERSIDAD AUTONOMA GABRIEL RENE MORENO</h1>
            <h2 class="text-xl font-semibold mb-1">FACULTAD DE INGENIERIA EN CIENCIAS DE LA COMPUTACION Y TELECOMUNICACIONES</h2>
            <h3 class="text-lg mt-4">LISTA DE ADMITIDOS — CUP FICCT</h3>
            @if($gestion)
                <p class="text-blue-200 mt-1">Gestion {{ ucfirst($gestion->periodo) }} {{ $gestion->anio }}</p>
            @endif
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8">

        <!-- Estadisticas -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-4xl font-bold text-blue-600">{{ $admitidos->count() }}</p>
                <p class="text-gray-500 mt-1">Total Admitidos</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-4xl font-bold text-green-600">
                    {{ $admitidos->where('opcion_asignada', 'primera')->count() }}
                </p>
                <p class="text-gray-500 mt-1">Admitidos en 1ra Opcion</p>
            </div>
        </div>

        <!-- Busqueda -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <input type="text" id="buscar" placeholder="Buscar por CI o nombre..."
                onkeyup="filtrar()"
                class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm" id="tablaAdmitidos">
                <thead class="bg-blue-900 text-white">
                    <tr>
                        <th class="px-4 py-3 text-center">#</th>
                        <th class="px-4 py-3 text-left">CI</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-center">Promedio</th>
                        <th class="px-4 py-3 text-left">Carrera Asignada</th>
                        <th class="px-4 py-3 text-center">Opcion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admitidos as $i => $a)
                    <tr class="border-t {{ $i % 2 === 0 ? '' : 'bg-gray-50' }} hover:bg-blue-50">
                        <td class="px-4 py-3 text-center text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $a->ci }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $a->name }}</td>
                        <td class="px-4 py-3 text-center font-bold text-blue-600">
                            {{ number_format($a->promedio_general, 2) }}
                        </td>
                        <td class="px-4 py-3 text-gray-800">{{ $a->carrera }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                {{ $a->opcion_asignada === 'primera' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($a->opcion_asignada) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            La lista de admitidos aun no ha sido publicada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-500 text-sm">
            <p>© {{ date('Y') }} FICCT - UAGRM. Todos los derechos reservados.</p>
            <p class="mt-1">Para consultas dirigirse a la oficina de admisiones de la facultad.</p>
        </div>

    </div>

    <script>
    function filtrar() {
        const input = document.getElementById('buscar').value.toLowerCase();
        const rows = document.querySelectorAll('#tablaAdmitidos tbody tr');
        rows.forEach(row => {
            const texto = row.textContent.toLowerCase();
            row.style.display = texto.includes(input) ? '' : 'none';
        });
    }
    </script>

</body>
</html>
