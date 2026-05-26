<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestion de Grupos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4">{{ session('error') }}</div>
            @endif

            <!-- Estadisticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $totalInscritos }}</p>
                    <p class="text-gray-500 text-sm mt-1">Postulantes habilitados</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $grupos->count() }}</p>
                    <p class="text-gray-500 text-sm mt-1">Grupos creados</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-bold text-yellow-600">{{ $sinGrupo }}</p>
                    <p class="text-gray-500 text-sm mt-1">Sin grupo asignado</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-bold text-purple-600">{{ $gruposNecesarios }}</p>
                    <p class="text-gray-500 text-sm mt-1">Grupos necesarios</p>
                </div>
            </div>

            <!-- Boton generar -->
            @if($sinGrupo > 0)
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-2">Generar grupos automaticamente</h3>
                <p class="text-gray-500 text-sm mb-4">
                    Hay <strong>{{ $sinGrupo }}</strong> postulantes sin grupo.
                    Se crearan <strong>{{ ceil($sinGrupo / 70) }}</strong> grupo(s) nuevo(s) de hasta 70 alumnos.
                </p>
                <form method="POST" action="{{ route('admin.grupos.generar') }}">
                    @csrf
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
                        onclick="return confirm('Generar grupos para los postulantes sin asignar?')">
                        Generar Grupos
                    </button>
                </form>
            </div>
            @endif

            <!-- Lista de grupos -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">
                        Grupos de la gestion {{ $gestion->periodo }} {{ $gestion->anio }}
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Grupo</th>
                            <th class="px-4 py-3 text-left">Turno</th>
                            <th class="px-4 py-3 text-center">Alumnos</th>
                            <th class="px-4 py-3 text-center">Cupo</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grupos as $grupo)
                        <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $grupo->nombre }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $grupo->turno === 'maniana' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($grupo->turno) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-800 dark:text-gray-100">{{ $grupo->cupo_actual }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full"
                                         style="width: {{ ($grupo->cupo_actual / $grupo->cupo_maximo) * 100 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $grupo->cupo_actual }}/{{ $grupo->cupo_maximo }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.grupos.show', $grupo->id) }}"
                                   class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No hay grupos creados aun.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
