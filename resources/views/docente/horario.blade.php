<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
                Mi Horario
            </h2>
            <a href="{{ route('docente.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Panel del docente
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if($gestion)
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Gestión activa: <strong class="text-gray-700 dark:text-gray-200">{{ $gestion->nombre ?? $gestion->anio ?? $gestion->id }}</strong>
            </p>
            @endif

            @if($horario->isEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-10 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-500 font-medium">No tienes horario asignado</p>
                <p class="text-gray-400 text-sm mt-1">El administrador debe asignarte horarios.</p>
            </div>
            @else

            {{-- Vista de tabla --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead style="background-color: #1F4E79;" class="text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Día</th>
                            <th class="px-4 py-3 text-left font-semibold">Hora</th>
                            <th class="px-4 py-3 text-left font-semibold">Materia</th>
                            <th class="px-4 py-3 text-left font-semibold">Grupo</th>
                            <th class="px-4 py-3 text-left font-semibold">Aula</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php $diaActual = null; @endphp
                        @foreach($horario as $h)
                        @if($h->dia !== $diaActual)
                            @php $diaActual = $h->dia; @endphp
                            <tr>
                                <td colspan="5"
                                    class="px-4 py-2 text-xs font-bold uppercase tracking-wider"
                                    style="background-color: rgba(31,78,121,0.07); color: #1F4E79;">
                                    {{ ucfirst($h->dia) }}
                                </td>
                            </tr>
                        @endif
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-400 text-xs capitalize pl-8">
                                {{ $h->dia }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ substr($h->hora_inicio, 0, 5) }}
                                </span>
                                <span class="text-gray-400 mx-1">&ndash;</span>
                                <span class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ substr($h->hora_fin, 0, 5) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $h->materia_nombre }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-700 dark:text-gray-300">{{ $h->grupo_nombre }}</span>
                                <span class="ml-1 text-xs px-1.5 py-0.5 rounded text-white"
                                      style="background-color: #1F4E79; opacity: 0.7;">
                                    {{ ucfirst($h->turno) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-600 dark:text-gray-300">{{ $h->aula_nombre }}</span>
                                @if($h->aula_codigo)
                                <span class="ml-1 text-xs font-mono px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-500">
                                    {{ $h->aula_codigo }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Resumen de carga horaria --}}
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    $diasUnicos    = $horario->pluck('dia')->unique()->count();
                    $totalClases   = $horario->count();
                    $totalMinutos  = $horario->reduce(function($carry, $h) {
                        $inicio = \Carbon\Carbon::parse($h->hora_inicio);
                        $fin    = \Carbon\Carbon::parse($h->hora_fin);
                        return $carry + $inicio->diffInMinutes($fin);
                    }, 0);
                    $totalHoras = round($totalMinutos / 60, 1);
                @endphp
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold" style="color: #1F4E79;">{{ $diasUnicos }}</p>
                    <p class="text-xs text-gray-500 mt-1">Días por semana</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $totalClases }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sesiones totales</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $totalHoras }}</p>
                    <p class="text-xs text-gray-500 mt-1">Horas semanales</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-orange-600">
                        {{ $horario->pluck('materia_nombre')->unique()->count() }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Materias</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
