<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestion de Postulantes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif

            <!-- Acciones -->
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.postulantes.importar') }}"
                   class="inline-flex items-center gap-2 text-white text-sm font-semibold px-4 py-2 rounded-md"
                   style="background-color: #1F4E79;"
                   onmouseover="this.style.backgroundColor='#163a5f'"
                   onmouseout="this.style.backgroundColor='#1F4E79'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Carga masiva (Excel/CSV)
                </a>
            </div>

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.postulantes.index') }}" class="flex gap-4 flex-wrap">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nombre, CI o email..."
                        class="border rounded px-3 py-2 flex-1 min-w-48 dark:bg-gray-700 dark:text-gray-100">
                    <select name="estado" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado')==='pendiente'?'selected':'' }}>Pendiente</option>
                        <option value="habilitado" {{ request('estado')==='habilitado'?'selected':'' }}>Habilitado</option>
                        <option value="inscrito" {{ request('estado')==='inscrito'?'selected':'' }}>Inscrito</option>
                        <option value="admitido" {{ request('estado')==='admitido'?'selected':'' }}>Admitido</option>
                        <option value="rechazado" {{ request('estado')==='rechazado'?'selected':'' }}>Rechazado</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Buscar</button>
                    <a href="{{ route('admin.postulantes.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Limpiar</a>
                </form>
            </div>

            <!-- Tabla -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">CI</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Ciudad</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($postulantes as $p)
                        <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $p->ci }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $p->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $p->email }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $p->ciudad }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $p->estado === 'admitido' ? 'bg-green-100 text-green-800' :
                                       ($p->estado === 'habilitado' ? 'bg-blue-100 text-blue-800' :
                                       ($p->estado === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst($p->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.postulantes.show', $p->id) }}"
                                   class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 mr-1">Ver</a>
                                <a href="{{ route('admin.postulantes.edit', $p->id) }}"
                                   class="bg-yellow-500 text-white px-2 py-1 rounded text-xs hover:bg-yellow-600 mr-1">Editar</a>
                                <form method="POST" action="{{ route('admin.postulantes.destroy', $p->id) }}" class="inline"
                                      onsubmit="return confirm('Seguro que deseas eliminar este postulante?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No se encontraron postulantes.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">
                    {{ $postulantes->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
