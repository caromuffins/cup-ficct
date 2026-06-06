<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admision — Gestion {{ ucfirst($gestion->periodo) }} {{ $gestion->anio }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif

            <!-- Acciones -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Proceso de Admision</h3>
                <div class="flex gap-4 flex-wrap">
                    <form method="POST" action="{{ route('admin.admision.calcular') }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Calcular resultados de todos los postulantes?')"
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            1. Calcular Resultados
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.admision.asignarCarreras') }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Asignar carreras segun promedio y cupo?')"
                            class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                            2. Asignar Carreras
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.admision.publicar') }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Publicar lista de admitidos?')"
                            class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">
                            3. Publicar Lista
                        </button>
                    </form>
                </div>
                <p class="text-gray-500 text-sm mt-3">
                    Primero calcula los resultados, luego asigna carreras segun promedio y cupo disponible.
                </p>
            </div>

            <!-- Estadisticas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $resultados->count() }}</p>
                    <p class="text-gray-500 text-sm mt-1">Total Evaluados</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $resultados->where('apto', true)->count() }}</p>
                    <p class="text-gray-500 text-sm mt-1">Aptos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-purple-600">{{ $resultados->where('admitido', true)->count() }}</p>
                    <p class="text-gray-500 text-sm mt-1">Admitidos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-red-600">{{ $resultados->where('apto', false)->count() }}</p>
                    <p class="text-gray-500 text-sm mt-1">No Aptos</p>
                </div>
            </div>

            <!-- Tabla de resultados -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">Resultados por Postulante</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">CI</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-center">Grupo</th>
                            <th class="px-4 py-3 text-center">Materias Aprobadas</th>
                            <th class="px-4 py-3 text-center">Promedio</th>
                            <th class="px-4 py-3 text-center">Apto</th>
                            <th class="px-4 py-3 text-center">Carrera Asignada</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultados as $r)
                        <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-600">{{ $r->ci }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $r->name }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $r->grupo }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="{{ $r->apto ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    {{ $r->materias_aprobadas }}/{{ $r->total_materias }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-gray-800 dark:text-gray-100">
                                {{ $r->promedio_general ? number_format($r->promedio_general, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($r->apto)
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">APTO</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">NO APTO</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                @if($r->carrera_asignada)
                                    {{ $r->carrera_asignada }}
                                    <span class="text-gray-400">({{ ucfirst($r->opcion_asignada) }})</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($r->admitido)
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">ADMITIDO</span>
                                @elseif($r->apto)
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">PENDIENTE</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">NO ADMITIDO</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No hay postulantes evaluados aun. Primero registra las notas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
