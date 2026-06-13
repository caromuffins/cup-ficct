<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
                Historial de Asistencia
            </h2>
            <a href="{{ route('docente.asistencia') }}?grupo_id={{ $grupo->id }}&materia_id={{ $materia->id }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Registrar asistencia
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Encabezado del Grupo -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4" style="border-left-color: #1F4E79;">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $materia->nombre }}</h3>
                <p class="text-sm mt-0.5" style="color: #1F4E79;">
                    Grupo: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $grupo->nombre }} ({{ ucfirst($grupo->turno) }})</span>
                    &bull;
                    Gestión: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $gestion->periodo }} {{ $gestion->anio }}</span>
                </p>
            </div>

            <!-- Tabla Matriz de Historial -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Control de Fechas</h4>
                    <span class="text-xs text-gray-400 font-medium">Simbología: P = Presente, F = Falta, L = Licencia</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead style="background-color: #1F4E79;" class="text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold w-28">CI</th>
                                <th class="px-4 py-3 text-left font-semibold w-48">Alumno</th>
                                <!-- Fechas Registradas -->
                                @forelse($asistencias as $asist)
                                    <th class="px-3 py-3 text-center font-semibold text-xs whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($asist->fecha)->format('d/m') }}
                                    </th>
                                @empty
                                    <th class="px-3 py-3 text-center font-semibold text-xs">Sin fechas</th>
                                @endforelse
                                <!-- Totales -->
                                <th class="px-3 py-3 text-center font-semibold text-green-300 border-l border-blue-900 w-16">P</th>
                                <th class="px-3 py-3 text-center font-semibold text-red-300 w-16">F</th>
                                <th class="px-3 py-3 text-center font-semibold text-yellow-300 w-16">L</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($matriz as $reg)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $reg->ci }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-100">{{ $reg->nombre }}</td>
                                    
                                    <!-- Celdas de Fechas -->
                                    @foreach($asistencias as $asist)
                                        <td class="px-3 py-3 text-center">
                                            @php
                                                $estado = $reg->fechas[$asist->id];
                                                $claseColor = 'text-gray-300';
                                                $simbolo = '—';
                                                
                                                if ($estado === 'presente') {
                                                    $claseColor = 'text-green-600 dark:text-green-400 font-extrabold';
                                                    $simbolo = 'P';
                                                } elseif ($estado === 'falta') {
                                                    $claseColor = 'text-red-600 dark:text-red-400 font-extrabold';
                                                    $simbolo = 'F';
                                                } elseif ($estado === 'licencia') {
                                                    $claseColor = 'text-yellow-600 dark:text-yellow-400 font-extrabold';
                                                    $simbolo = 'L';
                                                }
                                            @endphp
                                            <span class="{{ $claseColor }}">{{ $simbolo }}</span>
                                        </td>
                                    @endforeach
                                    
                                    @if($asistencias->isEmpty())
                                        <td class="px-3 py-3 text-center text-gray-400 italic">No hay clases registradas.</td>
                                    @endif

                                    <!-- Totales -->
                                    <td class="px-3 py-3 text-center font-bold text-green-600 dark:text-green-400 border-l border-gray-100 dark:border-gray-700 bg-green-50/20">
                                        {{ $reg->totales['presente'] }}
                                    </td>
                                    <td class="px-3 py-3 text-center font-bold text-red-600 dark:text-red-400 bg-red-50/20">
                                        {{ $reg->totales['falta'] }}
                                    </td>
                                    <td class="px-3 py-3 text-center font-bold text-yellow-600 dark:text-yellow-400 bg-yellow-50/20">
                                        {{ $reg->totales['licencia'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100" class="px-4 py-8 text-center text-gray-400">
                                        No hay alumnos registrados en este grupo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
