<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestion de Docentes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.docentes.index') }}" class="flex gap-4 flex-wrap">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nombre o email..."
                        class="border rounded px-3 py-2 flex-1 min-w-48 dark:bg-gray-700 dark:text-gray-100">
                    <select name="estado" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado')==='pendiente'?'selected':'' }}>Pendiente</option>
                        <option value="contratado" {{ request('estado')==='contratado'?'selected':'' }}>Contratado</option>
                        <option value="rechazado" {{ request('estado')==='rechazado'?'selected':'' }}>Rechazado</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Buscar</button>
                    <a href="{{ route('admin.docentes.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Limpiar</a>
                    <a href="{{ route('admin.docentes.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Nuevo Docente</a>
                </form>
            </div>

            <!-- Tabla -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Especialidad</th>
                            <th class="px-4 py-3 text-center">Maestria</th>
                            <th class="px-4 py-3 text-center">Diplomado</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($docentes as $d)
                        <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $d->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $d->email }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $d->especialidad ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($d->tiene_maestria)
                                    <span class="text-green-600">✓</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($d->tiene_diplomado)
                                    <span class="text-green-600">✓</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $d->estado_contratacion === 'contratado' ? 'bg-green-100 text-green-800' :
                                       ($d->estado_contratacion === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($d->estado_contratacion) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.docentes.show', $d->id) }}"
                                   class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 mr-1">Ver</a>
                                <a href="{{ route('admin.docentes.edit', $d->id) }}"
                                   class="bg-yellow-500 text-white px-2 py-1 rounded text-xs hover:bg-yellow-600 mr-1">Editar</a>
                                <form method="POST" action="{{ route('admin.docentes.destroy', $d->id) }}" class="inline"
                                      onsubmit="return confirm('Seguro que deseas eliminar este docente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No se encontraron docentes.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">
                    {{ $docentes->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
