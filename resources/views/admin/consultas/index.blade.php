<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Consultas Dinamicas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Filtros de Consulta</h3>
                <form method="GET" action="{{ route('admin.consultas.ejecutar') }}">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Grupo</label>
                            <select name="grupo_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Todos los grupos</option>
                                @foreach($grupos as $g)
                                    <option value="{{ $g->id }}" {{ request('grupo_id')==$g->id?'selected':'' }}>
                                        {{ $g->nombre }} ({{ ucfirst($g->turno) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Sexo</label>
                            <select name="sexo" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Todos</option>
                                <option value="M" {{ request('sexo')==='M'?'selected':'' }}>Masculino</option>
                                <option value="F" {{ request('sexo')==='F'?'selected':'' }}>Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Estado Admision</label>
                            <select name="admitido" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Todos</option>
                                <option value="1" {{ request('admitido')==='1'?'selected':'' }}>Admitidos</option>
                                <option value="0" {{ request('admitido')==='0'?'selected':'' }}>No Admitidos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Carrera Asignada</label>
                            <select name="carrera_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Todas las carreras</option>
                                @foreach($carreras as $c)
                                    <option value="{{ $c->id }}" {{ request('carrera_id')==$c->id?'selected':'' }}>
                                        {{ $c->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Promedio Minimo</label>
                            <input type="number" name="promedio_min" value="{{ request('promedio_min') }}"
                                min="0" max="100" step="0.01" placeholder="Ej: 60"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Promedio Maximo</label>
                            <input type="number" name="promedio_max" value="{{ request('promedio_max') }}"
                                min="0" max="100" step="0.01" placeholder="Ej: 100"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            Ejecutar Consulta
                        </button>
                        <a href="{{ route('admin.consultas.index') }}"
                            class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            @isset($resultados)

            <!-- Estadisticas -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $estadisticas['total'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Total</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $estadisticas['admitidos'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Admitidos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-purple-600">{{ $estadisticas['promedio'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Promedio</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-blue-400">{{ $estadisticas['masculino'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Masculino</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-pink-500">{{ $estadisticas['femenino'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Femenino</p>
                </div>
            </div>

            <!-- Tabla resultados -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">
                        Resultados ({{ $resultados->count() }} registros)
                    </h3>
                    <a href="{{ route('admin.reportes.exportar.aprobados', array_merge(request()->all(), ['formato'=>'excel'])) }}"
                        class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                        Exportar CSV
                    </a>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">CI</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-center">Sexo</th>
                            <th class="px-4 py-3 text-center">Grupo</th>
                            <th class="px-4 py-3 text-center">Materias Aprobadas</th>
                            <th class="px-4 py-3 text-center">Promedio</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-left">Carrera</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultados as $i => $r)
                        <tr class="border-t {{ $i%2===0?'':'bg-gray-50 dark:bg-gray-700' }} hover:bg-blue-50">
                            <td class="px-4 py-3 text-gray-600">{{ $r->ci }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $r->nombre }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="{{ $r->sexo==='M'?'text-blue-500':'text-pink-500' }} font-medium">
                                    {{ $r->sexo==='M'?'M':'F' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">
                                {{ $r->grupo }}
                                <span class="text-xs px-1 py-0.5 rounded
                                    {{ $r->turno==='maniana'?'bg-yellow-100 text-yellow-800':'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($r->turno) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="{{ $r->materias_aprobadas >= 4 ?'text-green-600':'text-red-600' }} font-medium">
                                    {{ $r->materias_aprobadas }}/4
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600">
                                {{ $r->promedio ? number_format($r->promedio, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($r->admitido)
                                    <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">ADMITIDO</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">NO ADMITIDO</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $r->carrera_asignada ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron resultados con los filtros seleccionados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @endisset

        </div>
    </div>
</x-app-layout>
