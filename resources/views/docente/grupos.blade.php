<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
                Mis Grupos
            </h2>
            <a href="{{ route('docente.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Panel del docente
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($gestion)
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Gestión activa: <strong class="text-gray-700 dark:text-gray-200">{{ $gestion->nombre ?? $gestion->anio ?? $gestion->id }}</strong>
            </p>
            @endif

            @if($grupos->isEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-10 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-500 font-medium">No tienes grupos asignados</p>
                <p class="text-gray-400 text-sm mt-1">El administrador debe asignarte grupos para esta gestión.</p>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($grupos as $g)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                    {{-- Header de la card --}}
                    <div class="px-5 py-4 flex items-center justify-between" style="background-color: #1F4E79;">
                        <div>
                            <h3 class="text-white font-bold text-base">{{ $g->grupo_nombre }}</h3>
                            <p class="text-blue-200 text-xs mt-0.5">
                                Turno {{ ucfirst($g->turno) }}
                                &bull;
                                <span class="font-medium text-white">{{ $g->materia_nombre }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-bold text-xl">{{ $g->alumnos_count }}</p>
                            <p class="text-blue-200 text-xs">alumnos</p>
                        </div>
                    </div>

                    {{-- Datos de horario --}}
                    <div class="px-5 py-4 space-y-2">
                        @if($g->horario)
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="capitalize font-medium">{{ $g->horario->dia }}</span>
                            <span class="text-gray-400">&bull;</span>
                            <span>{{ substr($g->horario->hora_inicio, 0, 5) }} &ndash; {{ substr($g->horario->hora_fin, 0, 5) }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>{{ $g->horario->aula_nombre ?? 'Aula' }}</span>
                            @if($g->horario->aula_codigo)
                                <span class="text-xs px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded font-mono text-gray-500">
                                    {{ $g->horario->aula_codigo }}
                                </span>
                            @endif
                        </div>
                        @else
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Sin horario asignado
                        </p>
                        @endif
                    </div>

                    {{-- Botones de acción --}}
                    <div class="px-5 pb-4 flex gap-3">
                        <a href="{{ route('docente.asistencia') }}?grupo_id={{ $g->grupo_id }}&materia_id={{ $g->materia_id }}"
                           class="flex-1 text-center text-white text-sm font-semibold py-2 rounded-md hover:opacity-90 transition-opacity"
                           style="background-color: #10B981;">
                            Asistencia
                        </a>
                        <a href="{{ route('docente.notas') }}?grupo_id={{ $g->grupo_id }}&materia_id={{ $g->materia_id }}"
                           class="flex-1 text-center text-white text-sm font-semibold py-2 rounded-md transition-colors"
                           style="background-color: #1F4E79;"
                           onmouseover="this.style.backgroundColor='#163a5f'"
                           onmouseout="this.style.backgroundColor='#1F4E79'">
                            Registrar Notas
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
