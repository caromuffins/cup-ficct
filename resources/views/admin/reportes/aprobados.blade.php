<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reporte de Aprobados y Grupos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.reportes.aprobados') }}" class="flex gap-4 flex-wrap">
                    <select name="grupo_id" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Todos los grupos</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id }}" {{ request('grupo_id')==$g->id?'selected':'' }}>
                                {{ $g->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <select name="admitido" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Todos</option>
                        <option value="1" {{ request('admitido')==='1'?'selected':'' }}>Admitidos</option>
                        <option value="0" {{ request('admitido')==='0'?'selected':'' }}>No Admitidos</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.reportes.aprobados') }}"
                        class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Limpiar</a>
                    <a href="{{ route('admin.reportes.exportar.aprobados', array_merge(request()->all(), ['formato'=>'excel'])) }}"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Exportar CSV
                    </a>
                    <a href="{{ route('admin.reportes.exportar.aprobados', array_merge(request()->all(), ['formato'=>'pdf'])) }}"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Exportar PDF
                    </a>
                </form>
            </div>

            <!-- Estadisticas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $estadisticas['total'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Total</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $estadisticas['admitidos'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Admitidos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-red-600">{{ $estadisticas['no_admitidos'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">No Admitidos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-purple-600">{{ $estadisticas['promedio'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Promedio General</p>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">CI</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-center">Grupo</th>
                            <th class="px-4 py-3 text-center">Turno</th>
                            <th class="px-4 py-3 text-center">Promedio</th>
                            <th class="px-4 py-3 text-left">Carrera</th>
                            <th class="px-4 py-3 text-center">Opcion</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datos as $i => $d)
                        <tr class="border-t {{ $i%2===0?'':'bg-gray-50 dark:bg-gray-700' }} hover:bg-blue-50">
                            <td class="px-4 py-3 text-gray-600">{{ $d->ci }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $d->name }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $d->grupo }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $d->turno==='maniana'?'bg-yellow-100 text-yellow-800':'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($d->turno) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600">
                                {{ $d->promedio_general ? number_format($d->promedio_general, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $d->carrera ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($d->opcion_asignada)
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $d->opcion_asignada==='primera'?'bg-green-100 text-green-800':'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($d->opcion_asignada) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($d->admitido)
                                    <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">ADMITIDO</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">NO ADMITIDO</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No hay datos para mostrar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.reportes.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">
                    Volver a Reportes
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
