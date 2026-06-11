<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
            Panel del Docente
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Bienvenida + estado contratación --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4"
                 style="border-left-color: #1F4E79;">
                <div class="p-6 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shrink-0"
                             style="background-color: #1F4E79;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                                Bienvenido, {{ auth()->user()->name }}
                            </h3>
                            <p class="text-sm mt-0.5" style="color: #1F4E79;">
                                Docente &mdash; Sistema CUP FICCT &ndash; UAGRM
                            </p>
                            @if($docente)
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Especialidad: {{ $docente->especialidad ?? 'No especificada' }}
                                    @if($docente->titulo_profesional)
                                        &bull; {{ $docente->titulo_profesional }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Estado de contratación --}}
                    @if($docente)
                    <div class="shrink-0">
                        @php
                            $estadoClases = match($docente->estado_contratacion ?? 'pendiente') {
                                'contratado' => 'bg-green-100 text-green-800 border-green-200',
                                'rechazado'  => 'bg-red-100 text-red-800 border-red-200',
                                default      => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            };
                            $estadoLabel = match($docente->estado_contratacion ?? 'pendiente') {
                                'contratado' => 'Contratado',
                                'rechazado'  => 'Rechazado',
                                default      => 'Pendiente',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $estadoClases }}">
                            <span class="w-2 h-2 rounded-full
                                {{ $docente->estado_contratacion === 'contratado' ? 'bg-green-500' :
                                   ($docente->estado_contratacion === 'rechazado' ? 'bg-red-500' : 'bg-yellow-500') }}">
                            </span>
                            Estado: {{ $estadoLabel }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Cards de estadísticas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5 text-center border-t-2"
                     style="border-top-color: #1F4E79;">
                    <p class="text-3xl font-bold" style="color: #1F4E79;">{{ $stats['grupos'] }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1 font-medium uppercase tracking-wide">
                        Grupos Asignados
                    </p>
                    @if($gestion)
                    <p class="text-xs text-gray-400 mt-0.5">Gestión {{ $gestion->nombre ?? $gestion->anio ?? '' }}</p>
                    @endif
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5 text-center border-t-2 border-green-500">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['alumnos'] }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1 font-medium uppercase tracking-wide">
                        Alumnos a Cargo
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">en todos los grupos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-5 text-center border-t-2 border-purple-500">
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['materias'] }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1 font-medium uppercase tracking-wide">
                        Materias Impartidas
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">distintas</p>
                </div>
            </div>

            {{-- Módulos de navegación --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                    Acceso Rápido
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <a href="{{ route('docente.grupos') }}"
                       class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 group-hover:text-blue-700">
                                Mis Grupos
                            </h4>
                            <p class="text-gray-500 text-xs mt-0.5">Ver grupos y alumnos asignados</p>
                        </div>
                    </a>

                    <a href="{{ route('docente.notas') }}"
                       class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 group-hover:text-blue-700">
                                Registrar Notas
                            </h4>
                            <p class="text-gray-500 text-xs mt-0.5">Ingresar calificaciones de exámenes</p>
                        </div>
                    </a>

                    <a href="{{ route('docente.horario') }}"
                       class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 group-hover:text-blue-700">
                                Mi Horario
                            </h4>
                            <p class="text-gray-500 text-xs mt-0.5">Ver carga horaria asignada</p>
                        </div>
                    </a>

                </div>
            </div>

            {{-- Preview de asignaciones actuales --}}
            @if($asignaciones->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">Asignaciones Actuales</h3>
                    <a href="{{ route('docente.grupos') }}"
                       class="text-xs font-medium hover:underline" style="color: #1F4E79;">
                        Ver detalle →
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($asignaciones as $a)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full shrink-0" style="background-color: #1F4E79;"></div>
                            <div>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $a->grupo_nombre }}</span>
                                <span class="text-xs text-gray-400 ml-2">({{ ucfirst($a->turno) }})</span>
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full text-white" style="background-color: #1F4E79;">
                            {{ $a->materia_nombre }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 text-center">
                <p class="text-gray-400 text-sm">No tienes grupos asignados en la gestión activa.</p>
                <p class="text-gray-400 text-xs mt-1">Contacta al administrador para que te asigne grupos.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
