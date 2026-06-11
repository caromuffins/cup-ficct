<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Postulante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Bienvenida -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold">Bienvenido, {{ auth()->user()->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1">Panel de Postulante - CUP FICCT</p>
                </div>
            </div>

            <!-- Mi Estado -->
            @if($postulante)
            @php
                $estado = $postulante->estado ?? 'pendiente';

                // Determinar paso activo (1-6)
                $pasoActual = 1; // Registrado siempre
                if ($inscripcion) $pasoActual = 2;
                if ($inscripcion && $tieneRequisitos) $pasoActual = 3;
                if (in_array($estado, ['habilitado', 'inscrito', 'admitido'])) $pasoActual = 4;
                if ($inscripcion && $inscripcion->estado === 'pagada') $pasoActual = 5;
                if ($tieneGrupo) $pasoActual = 6;

                $pasos = [
                    1 => ['label' => 'Registrado',             'icon' => '👤'],
                    2 => ['label' => 'Inscrito',               'icon' => '📝'],
                    3 => ['label' => 'Requisitos entregados',  'icon' => '📄'],
                    4 => ['label' => 'Habilitado',             'icon' => '✅'],
                    5 => ['label' => 'Pago completado',        'icon' => '💳'],
                    6 => ['label' => 'Grupo asignado',         'icon' => '🎓'],
                ];
            @endphp

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-6">Mi Estado</h3>

                <!-- Stepper desktop -->
                <div class="hidden md:flex items-center justify-between relative">
                    <!-- Línea de fondo -->
                    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-600 z-0"></div>
                    <!-- Línea de progreso -->
                    <div class="absolute top-5 left-0 h-1 bg-blue-500 z-0 transition-all duration-500"
                         style="width: {{ (($pasoActual - 1) / 5) * 100 }}%"></div>

                    @foreach($pasos as $num => $paso)
                    @php
                        $completado = $num < $pasoActual;
                        $activo     = $num === $pasoActual;
                    @endphp
                    <div class="flex flex-col items-center z-10 flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all
                            {{ $completado ? 'bg-blue-500 border-blue-500 text-white' :
                               ($activo    ? 'bg-white border-blue-500 text-blue-600 ring-4 ring-blue-100 dark:bg-gray-800' :
                                             'bg-white border-gray-300 text-gray-400 dark:bg-gray-700 dark:border-gray-500') }}">
                            @if($completado)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <p class="text-xs mt-2 text-center font-medium
                            {{ $completado ? 'text-blue-600' :
                               ($activo    ? 'text-blue-700 dark:text-blue-400 font-bold' :
                                             'text-gray-400 dark:text-gray-500') }}">
                            {{ $paso['label'] }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <!-- Stepper mobile (vertical) -->
                <div class="md:hidden space-y-3">
                    @foreach($pasos as $num => $paso)
                    @php
                        $completado = $num < $pasoActual;
                        $activo     = $num === $pasoActual;
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 shrink-0
                            {{ $completado ? 'bg-blue-500 border-blue-500 text-white' :
                               ($activo    ? 'bg-white border-blue-500 text-blue-600 ring-4 ring-blue-100 dark:bg-gray-800' :
                                             'bg-gray-100 border-gray-300 text-gray-400 dark:bg-gray-700 dark:border-gray-500') }}">
                            @if($completado)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span class="text-sm
                            {{ $completado ? 'text-blue-600 line-through' :
                               ($activo    ? 'text-blue-700 dark:text-blue-400 font-bold' :
                                             'text-gray-400 dark:text-gray-500') }}">
                            {{ $paso['icon'] }} {{ $paso['label'] }}
                        </span>
                        @if($activo)
                            <span class="ml-auto text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Actual</span>
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Mensaje del paso actual -->
                <div class="mt-6 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-sm text-blue-800 dark:text-blue-300">
                    @if($pasoActual === 1)
                        Completa tu inscripcion para continuar con el proceso.
                    @elseif($pasoActual === 2)
                        Sube los requisitos solicitados para que el administrador los valide.
                    @elseif($pasoActual === 3)
                        Tus requisitos estan siendo revisados. Espera la habilitacion.
                    @elseif($pasoActual === 4)
                        Estas habilitado. Realiza el pago de inscripcion para avanzar.
                    @elseif($pasoActual === 5)
                        Pago confirmado. Espera la asignacion de tu grupo.
                    @else
                        Proceso completado. Ya tienes grupo asignado.
                    @endif
                </div>
            </div>
            @endif

            <!-- Menu de acciones -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('postulante.inscripcion.index') }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Mi Inscripcion</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver estado de mi inscripcion y requisitos</p>
                </a>
                <a href="{{ route('postulante.grupo.index') }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Mi Grupo</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver grupo y horario asignado</p>
                </a>
                <a href="{{ route('postulante.notas.index') }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Mis Notas</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver calificaciones por materia</p>
                </a>
                <a href="{{ route('postulante.admision.index') }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Resultado de Admisión</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver si fui admitido a la facultad</p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
