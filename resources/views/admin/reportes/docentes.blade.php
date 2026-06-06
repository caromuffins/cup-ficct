<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reporte de Docentes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.reportes.docentes') }}" class="flex gap-4 flex-wrap">
                    <select name="estado_contratacion" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Todos los estados</option>
                        <option value="contratado" {{ request('estado_contratacion')==='contratado'?'selected':'' }}>Contratados</option>
                        <option value="pendiente" {{ request('estado_contratacion')==='pendiente'?'selected':'' }}>Pendientes</option>
                        <option value="rechazado" {{ request('estado_contratacion')==='rechazado'?'selected':'' }}>Rechazados</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.reportes.docentes') }}"
                        class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Limpiar</a>
                    <a href="{{ route('admin.reportes.exportar.docentes', ['formato'=>'excel']) }}"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Exportar CSV
                    </a>
                </form>
            </div>

            <!-- Tabla -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Especialidad</th>
                            <th class="px-4 py-3 text-left">Titulo</th>
                            <th class="px-4 py-3 text-center">Maestria</th>
                            <th class="px-4 py-3 text-center">Diplomado</th>
                            <th class="px-4 py-3 text-center">Grupos</th>
                            <th class="px-4 py-3 text-left">Materias</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datos as $i => $d)
                        <tr class="border-t {{ $i%2===0?'':'bg-gray-50 dark:bg-gray-700' }}">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $d->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $d->especialidad ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $d->titulo_profesional ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                {{ $d->tiene_maestria ? 'Si' : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $d->tiene_diplomado ? 'Si' : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600">
                                {{ $d->total_grupos }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $d->materias_asignadas ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $d->estado_contratacion==='contratado'?'bg-green-100 text-green-800':
                                       ($d->estado_contratacion==='rechazado'?'bg-red-100 text-red-800':'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($d->estado_contratacion) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No hay docentes para mostrar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.reportes.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">
                    Volver a Reportes
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
