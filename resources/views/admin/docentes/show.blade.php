<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detalle del Docente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Datos personales -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100">Datos del Docente</h3>
                    <span class="px-3 py-1 rounded text-sm font-medium
                        {{ $docente->estado_contratacion === 'contratado' ? 'bg-green-100 text-green-800' :
                           ($docente->estado_contratacion === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($docente->estado_contratacion) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Nombre</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $docente->name }}</p></div>
                    <div><p class="text-gray-500">Email</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $docente->email }}</p></div>
                    <div><p class="text-gray-500">Especialidad</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $docente->especialidad ?? '—' }}</p></div>
                    <div><p class="text-gray-500">Titulo Profesional</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $docente->titulo_profesional ?? '—' }}</p></div>
                    <div>
                        <p class="text-gray-500">Maestria</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $docente->tiene_maestria ? '✓ ' . ($docente->area_maestria ?? '') : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Diplomado en Educacion Superior</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $docente->tiene_diplomado ? '✓ ' . ($docente->area_diplomado ?? '') : '—' }}
                        </p>
                    </div>
                    <div><p class="text-gray-500">Max. Grupos</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $docente->max_grupos }}</p></div>
                </div>
            </div>

            <!-- Grupos asignados -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-4">Grupos Asignados</h3>
                @forelse($grupos as $g)
                <div class="border rounded p-3 mb-2 flex justify-between items-center">
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $g->grupo }}</span>
                    <span class="text-gray-500 text-sm">{{ $g->materia }}</span>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $g->turno === 'maniana' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($g->turno) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No tiene grupos asignados aun.</p>
                @endforelse
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.docentes.edit', $docente->id) }}"
                   class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Editar</a>
                <a href="{{ route('admin.docentes.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Volver</a>
            </div>

        </div>
    </div>
</x-app-layout>
