<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $grupo->nombre }} — Detalle
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Info del grupo -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nombre</p>
                        <p class="font-bold text-gray-800 dark:text-gray-100">{{ $grupo->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Turno</p>
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $grupo->turno === 'maniana' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($grupo->turno) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-500">Alumnos</p>
                        <p class="font-bold text-gray-800 dark:text-gray-100">{{ $grupo->cupo_actual }} / {{ $grupo->cupo_maximo }}</p>
                    </div>
                </div>
            </div>

            <!-- Horarios -->
            @if($horarios->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden mb-6">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">Horario</h3>
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
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ ucfirst($h->dia) }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $h->materia }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $h->docente }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $h->aula }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $h->hora_inicio }} - {{ $h->hora_fin }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Lista de alumnos -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">
                        Alumnos ({{ $postulantes->count() }})
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">CI</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Ciudad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($postulantes as $p)
                        <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $p->ci }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $p->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $p->email }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $p->ciudad }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay alumnos.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.grupos.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Volver</a>
            </div>

        </div>
    </div>
</x-app-layout>
