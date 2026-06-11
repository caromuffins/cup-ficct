<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
                Mis Notas
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Volver al panel
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if($gestion)
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Gestión activa: <strong class="text-gray-700 dark:text-gray-200">
                    {{ $gestion->nombre ?? $gestion->anio ?? $gestion->id }}
                </strong>
            </p>
            @endif

            @if($resultados->isEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-10 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-500 font-medium">Aún no hay notas registradas</p>
                <p class="text-gray-400 text-sm mt-1">Las notas aparecerán aquí una vez que el docente las ingrese.</p>
            </div>
            @else

            {{-- Resumen general --}}
            @php
                $totalMaterias  = $resultados->count();
                $aprobadas      = $resultados->filter(fn($r) => $r->resultado && $r->resultado->aprobado)->count();
                $reprobadas     = $resultados->filter(fn($r) => $r->resultado && !$r->resultado->aprobado)->count();
                $sinResultado   = $totalMaterias - $aprobadas - $reprobadas;
                $promedioGral   = $resultados->filter(fn($r) => $r->resultado)->avg(fn($r) => $r->resultado->total);
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center border-t-2" style="border-top-color: #1F4E79;">
                    <p class="text-2xl font-bold" style="color: #1F4E79;">{{ $totalMaterias }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total materias</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center border-t-2 border-green-500">
                    <p class="text-2xl font-bold text-green-600">{{ $aprobadas }}</p>
                    <p class="text-xs text-gray-500 mt-1">Aprobadas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center border-t-2 border-red-500">
                    <p class="text-2xl font-bold text-red-600">{{ $reprobadas }}</p>
                    <p class="text-xs text-gray-500 mt-1">Reprobadas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center border-t-2 border-purple-500">
                    <p class="text-2xl font-bold text-purple-600">{{ $promedioGral ? number_format($promedioGral, 1) : '—' }}</p>
                    <p class="text-xs text-gray-500 mt-1">Promedio</p>
                </div>
            </div>

            {{-- Tabla por materia --}}
            @foreach($resultados as $r)
            @php
                $aprobado = $r->resultado && $r->resultado->aprobado;
                $hayNotas = $r->notas->isNotEmpty();
            @endphp
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-5 py-3 flex items-center justify-between"
                     style="{{ $hayNotas ? ($aprobado ? 'background-color: #f0fdf4; border-left: 4px solid #22c55e;' : 'background-color: #fef2f2; border-left: 4px solid #ef4444;') : 'background-color: #f9fafb; border-left: 4px solid #d1d5db;' }}">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ $r->materia->nombre }}</h3>
                    @if($r->resultado)
                        <span class="text-sm font-bold px-3 py-1 rounded-full
                            {{ $aprobado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $aprobado ? 'APROBADO' : 'REPROBADO' }}
                            &bull; {{ number_format($r->resultado->total, 1) }} pts
                        </span>
                    @else
                        <span class="text-xs text-gray-400 italic">Sin calificación</span>
                    @endif
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-5 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Evaluación</th>
                            <th class="px-5 py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Puntaje máx.</th>
                            <th class="px-5 py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Mi nota</th>
                            <th class="px-5 py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($r->examenes as $examen)
                        @php
                            $nota = $r->notas->get($examen->id);
                            $puntaje = $nota ? $nota->puntaje : null;
                            $porcentaje = ($puntaje !== null && $examen->puntaje_maximo > 0)
                                ? ($puntaje / $examen->puntaje_maximo) * 100
                                : null;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200 capitalize">
                                {{ str_replace(['parcial1','parcial2','final'], ['Parcial 1','Parcial 2','Final'], $examen->tipo) }}
                            </td>
                            <td class="px-5 py-3 text-center text-gray-500">{{ $examen->puntaje_maximo }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($puntaje !== null)
                                    <span class="font-bold text-gray-800 dark:text-gray-100">{{ number_format($puntaje, 1) }}</span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($porcentaje !== null)
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $porcentaje >= 60 ? 'bg-green-500' : 'bg-red-400' }}"
                                             style="width: {{ min($porcentaje, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ number_format($porcentaje, 0) }}%</span>
                                </div>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        {{-- Fila de total --}}
                        @if($r->resultado)
                        <tr class="font-bold {{ $aprobado ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                            <td class="px-5 py-3 text-gray-800 dark:text-gray-100" colspan="2">Total</td>
                            <td class="px-5 py-3 text-center text-gray-800 dark:text-gray-100 text-base">
                                {{ number_format($r->resultado->total, 1) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="{{ $aprobado ? 'text-green-700' : 'text-red-700' }} text-xs font-bold">
                                    {{ $aprobado ? '✓ APROBADO' : '✗ REPROBADO' }}
                                </span>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endforeach
            @endif

        </div>
    </div>
</x-app-layout>
