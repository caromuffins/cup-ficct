<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color:#1F4E79;">Reporte de Notas por Grupo</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Filtros --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5">
                <form method="GET" action="{{ route('admin.reportes.notas') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Grupo</label>
                        <select name="grupo_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Seleccionar grupo...</option>
                            @foreach($grupos as $g)
                                <option value="{{ $g->id }}" {{ request('grupo_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nombre }} ({{ ucfirst($g->turno) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-40">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Materia (opcional)</label>
                        <select name="materia_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Todas las materias</option>
                            @foreach($materias as $m)
                                <option value="{{ $m->id }}" {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white rounded-md"
                        style="background-color:#1F4E79;">
                        Generar reporte
                    </button>
                    @if(request('grupo_id'))
                    <a href="{{ route('admin.reportes.exportar.notas', request()->query()) }}"
                       class="px-4 py-2 text-sm font-semibold rounded-md border text-gray-700 hover:bg-gray-50">
                        Exportar CSV
                    </a>
                    @endif
                </form>
            </div>

            @if(request('grupo_id') && $datos->isNotEmpty())

            {{-- Estadísticas --}}
            @if(request('materia_id'))
            @php
                $aprobados   = $datos->where('aprobado', true)->count();
                $reprobados  = $datos->where('aprobado', false)->count();
                $promedio    = $datos->avg('total');
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center border-t-2" style="border-top-color:#1F4E79;">
                    <p class="text-2xl font-bold" style="color:#1F4E79;">{{ $datos->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total alumnos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center border-t-2 border-green-500">
                    <p class="text-2xl font-bold text-green-600">{{ $aprobados }}</p>
                    <p class="text-xs text-gray-500 mt-1">Aprobados</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center border-t-2 border-red-500">
                    <p class="text-2xl font-bold text-red-600">{{ $reprobados }}</p>
                    <p class="text-xs text-gray-500 mt-1">Reprobados</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center border-t-2 border-purple-500">
                    <p class="text-2xl font-bold text-purple-600">{{ $promedio ? number_format($promedio, 1) : '—' }}</p>
                    <p class="text-xs text-gray-500 mt-1">Promedio grupo</p>
                </div>
            </div>
            @endif

            {{-- Tabla detallada --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm">
                        {{ request('materia_id') ? 'Notas por examen' : 'Resumen por materia' }}
                        &mdash; {{ $datos->count() }} alumnos
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">CI</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Nombre</th>
                                @if(request('materia_id'))
                                    @foreach($examenes as $ex)
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">
                                        {{ str_replace(['parcial1','parcial2','final'],['P1','P2','Final'],$ex->tipo) }}
                                        <span class="font-normal">({{ $ex->puntaje_maximo }})</span>
                                    </th>
                                    @endforeach
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Total</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                @else
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Materias y totales</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($datos as $alumno)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-2 text-gray-500 font-mono text-xs">{{ $alumno->ci }}</td>
                                <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $alumno->name }}</td>

                                @if(request('materia_id'))
                                    @foreach($examenes as $ex)
                                    <td class="px-4 py-2 text-center text-gray-700 dark:text-gray-200">
                                        {{ isset($alumno->notas[$ex->id]) ? number_format($alumno->notas[$ex->id], 1) : '—' }}
                                    </td>
                                    @endforeach
                                    <td class="px-4 py-2 text-center font-bold text-gray-800 dark:text-gray-100">
                                        {{ number_format($alumno->total, 1) }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                            {{ $alumno->aprobado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $alumno->aprobado ? 'Aprobado' : 'Reprobado' }}
                                        </span>
                                    </td>
                                @else
                                    <td class="px-4 py-2">
                                        @if($alumno->resultados->isEmpty())
                                            <span class="text-gray-300 text-xs italic">Sin notas</span>
                                        @else
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($alumno->resultados as $r)
                                                <span class="px-2 py-0.5 rounded text-xs {{ $r->aprobado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $r->materia }}: {{ number_format($r->total, 1) }}
                                                </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif(request('grupo_id'))
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-10 text-center text-gray-400">
                No hay datos registrados para este grupo.
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-10 text-center text-gray-400">
                Selecciona un grupo para generar el reporte.
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
