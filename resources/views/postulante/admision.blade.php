<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
                Resultado de Admisión
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Volver al panel
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Lista no publicada aún --}}
            @if(!$publicada)
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 text-center border-t-4 border-yellow-400">
                <svg class="w-14 h-14 mx-auto text-yellow-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">Lista aún no publicada</h3>
                <p class="text-gray-500 text-sm">
                    Los resultados de admisión todavía no han sido publicados por el administrador.
                    Vuelve a revisar más tarde.
                </p>
            </div>

            {{-- Resumen de materias mientras espera --}}
            @if($materiasAprobadas->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">Mi rendimiento académico</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Basado en las notas registradas hasta ahora</p>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-5 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Materia</th>
                            <th class="px-5 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                            <th class="px-5 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($materiasAprobadas as $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-gray-800 dark:text-gray-100">{{ $m->nombre }}</td>
                            <td class="px-5 py-3 text-center font-bold text-gray-800 dark:text-gray-100">
                                {{ number_format($m->total, 1) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $m->aprobado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $m->aprobado ? 'Aprobado' : 'Reprobado' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <td class="px-5 py-3 font-bold text-gray-700 dark:text-gray-200">Promedio general</td>
                            <td class="px-5 py-3 text-center font-bold text-gray-800 dark:text-gray-100">
                                {{ number_format($materiasAprobadas->avg('total'), 1) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-xs text-gray-400">
                                    {{ $materiasAprobadas->where('aprobado', true)->count() }}/{{ $materiasAprobadas->count() }} aprobadas
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif

            @else
            {{-- Lista publicada --}}

            @if($admision && $admision->admitido)
            {{-- ADMITIDO --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden border-t-4 border-green-500">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-green-700 mb-2">¡Felicitaciones!</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-base">Fuiste admitido al CUP FICCT</p>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                    <div class="px-6 py-4 flex justify-between items-center">
                        <span class="text-sm text-gray-500">Carrera asignada</span>
                        <span class="font-bold text-gray-800 dark:text-gray-100">{{ $admision->carrera_nombre }}</span>
                    </div>
                    <div class="px-6 py-4 flex justify-between items-center">
                        <span class="text-sm text-gray-500">Código</span>
                        <span class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $admision->carrera_codigo }}</span>
                    </div>
                    <div class="px-6 py-4 flex justify-between items-center">
                        <span class="text-sm text-gray-500">Opción</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $admision->opcion_asignada === 'primera' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ucfirst($admision->opcion_asignada) }} opción
                        </span>
                    </div>
                    <div class="px-6 py-4 flex justify-between items-center">
                        <span class="text-sm text-gray-500">Promedio general</span>
                        <span class="font-bold text-gray-800 dark:text-gray-100 text-lg">
                            {{ number_format($admision->promedio_general, 2) }}
                        </span>
                    </div>
                </div>

                <div class="px-6 py-4 bg-green-50 dark:bg-green-900/20">
                    <p class="text-xs text-green-700 dark:text-green-400 text-center">
                        Puedes consultar la lista completa de admitidos en
                        <a href="{{ route('admision.lista-publica') }}" class="font-semibold underline" target="_blank">
                            la lista pública
                        </a>.
                    </p>
                </div>
            </div>

            @elseif($admision && !$admision->admitido)
            {{-- NO ADMITIDO --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 text-center border-t-4" style="border-top-color: #7F0000;">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">No fuiste admitido</h3>
                <p class="text-gray-500 text-sm max-w-sm mx-auto">
                    Lamentablemente no obtuviste un lugar en esta gestión.
                    Las carreras seleccionadas no tenían cupo disponible en base a tu promedio.
                </p>
                @if($admision->promedio_general > 0)
                <p class="mt-4 text-gray-500 text-sm">
                    Tu promedio general fue: <strong class="text-gray-800 dark:text-gray-100">{{ number_format($admision->promedio_general, 2) }}</strong>
                </p>
                @endif
            </div>

            @else
            {{-- SIN REGISTRO DE ADMISIÓN --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 text-center border-t-4 border-gray-300">
                <svg class="w-14 h-14 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-2">Sin resultado registrado</h3>
                <p class="text-gray-400 text-sm">
                    No se encontró un resultado de admisión para tu cuenta en esta gestión.
                </p>
            </div>
            @endif

            {{-- Resumen de materias (siempre visible si hay datos) --}}
            @if($materiasAprobadas->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">Detalle por materia</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-5 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Materia</th>
                            <th class="px-5 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                            <th class="px-5 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($materiasAprobadas as $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-gray-800 dark:text-gray-100">{{ $m->nombre }}</td>
                            <td class="px-5 py-3 text-center font-bold text-gray-800 dark:text-gray-100">
                                {{ number_format($m->total, 1) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $m->aprobado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $m->aprobado ? 'Aprobado' : 'Reprobado' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @endif {{-- fin $publicada --}}

        </div>
    </div>
</x-app-layout>
