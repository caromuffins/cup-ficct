<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mi Grupo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($asignacion)

                <!-- Info del grupo -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-4">
                        Informacion de tu Grupo
                    </h3>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Grupo asignado</p>
                            <p class="font-bold text-2xl text-blue-600">{{ $asignacion->nombre }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Turno</p>
                            <span class="px-3 py-1 rounded text-sm font-medium
                                {{ $asignacion->turno === 'maniana' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($asignacion->turno) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-500">Gestion</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100">
                                {{ ucfirst($asignacion->periodo) }} {{ $asignacion->anio }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Horario -->
                @if($horarios->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden mb-6">
                    <div class="p-4 border-b">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100">Horario de Clases</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-blue-900 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left">Dia</th>
                                <th class="px-4 py-3 text-left">Materia</th>
                                <th class="px-4 py-3 text-left">Docente</th>
                                <th class="px-4 py-3 text-left">Aula</th>
                                <th class="px-4 py-3 text-left">Horario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($horarios as $h)
                            <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ ucfirst($h->dia) }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $h->materia }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $h->docente }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $h->aula }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $h->hora_inicio }} - {{ $h->hora_fin }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded mb-6">
                    El horario de tu grupo aun no ha sido asignado. Consulta mas tarde.
                </div>
                @endif

                <!-- Materias del CUP -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Materias del CUP</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['Matematicas', 'Fisica', 'Computacion', 'Ingles'] as $materia)
                        <div class="border rounded p-4 flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-gray-800 dark:text-gray-100 font-medium">{{ $materia }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

            @else
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 text-center">
                    <p class="text-gray-500 text-lg mb-2">Aun no tienes un grupo asignado.</p>
                    <p class="text-gray-400 text-sm">Los grupos se asignan despues de completar tu inscripcion y pago.</p>
                    <a href="{{ route('postulante.inscripcion.index') }}"
                       class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Ver mi inscripcion
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
