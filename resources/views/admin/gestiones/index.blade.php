<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color:#1F4E79;">Gestiones</h2>
            <a href="{{ route('admin.gestiones.create') }}"
               class="text-white text-sm font-semibold px-4 py-2 rounded-md"
               style="background-color:#1F4E79;">
                + Nueva gestión
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead style="background-color:#1F4E79;">
                        <tr class="text-white text-left">
                            <th class="px-5 py-3 font-semibold">Año</th>
                            <th class="px-5 py-3 font-semibold">Período</th>
                            <th class="px-5 py-3 font-semibold">Inicio</th>
                            <th class="px-5 py-3 font-semibold">Fin</th>
                            <th class="px-5 py-3 font-semibold text-center">Cupo/Carrera</th>
                            <th class="px-5 py-3 font-semibold text-center">Monto (USD)</th>
                            <th class="px-5 py-3 font-semibold text-center">Estado</th>
                            <th class="px-5 py-3 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($gestiones as $g)
                        <tr class="{{ $g->activa ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30' }}">
                            <td class="px-5 py-3 font-bold text-gray-800 dark:text-gray-100">{{ $g->anio }}</td>
                            <td class="px-5 py-3 text-gray-700 dark:text-gray-200 capitalize">{{ $g->periodo }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($g->fecha_inicio)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($g->fecha_fin)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-center text-gray-700 dark:text-gray-200">{{ $g->cupo_por_carrera }}</td>
                            <td class="px-5 py-3 text-center text-gray-700 dark:text-gray-200">{{ number_format($g->monto_inscripcion, 2) }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($g->activa)
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Activa</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Inactiva</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    @if(!$g->activa)
                                    <form method="POST" action="{{ route('admin.gestiones.activar', $g->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs px-2 py-1 rounded font-semibold text-green-700 bg-green-100 hover:bg-green-200">
                                            Activar
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('admin.gestiones.edit', $g->id) }}"
                                       class="text-xs px-2 py-1 rounded font-semibold text-blue-700 bg-blue-100 hover:bg-blue-200">
                                        Editar
                                    </a>
                                    @if(!$g->activa)
                                    <form method="POST" action="{{ route('admin.gestiones.destroy', $g->id) }}" class="inline"
                                          onsubmit="return confirm('¿Eliminar esta gestión?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs px-2 py-1 rounded font-semibold text-red-700 bg-red-100 hover:bg-red-200">
                                            Eliminar
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-gray-400">No hay gestiones registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
