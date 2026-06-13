<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color:#1F4E79;">
                Bitácora del Sistema
            </h2>
            <div class="text-sm text-gray-500 font-medium">
                Auditoría y Registro de Actividades
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-3 rounded bg-green-100 text-green-800 text-sm shadow-sm border-l-4 border-green-500">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Panel de Filtros -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border-t-4" style="border-top-color:#1F4E79;">
                <div class="p-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtros de Búsqueda
                    </h3>
                    
                    <form method="GET" action="{{ route('admin.bitacora.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Buscar Usuario -->
                        <div>
                            <label for="usuario" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Usuario</label>
                            <input type="text" name="usuario" id="usuario" value="{{ request('usuario') }}" 
                                   placeholder="Nombre o correo..." 
                                   class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Módulo -->
                        <div>
                            <label for="modulo" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Módulo</label>
                            <select name="modulo" id="modulo" 
                                    class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Todos los módulos</option>
                                @foreach($modulos as $mod)
                                    <option value="{{ $mod }}" {{ request('modulo') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Acción -->
                        <div>
                            <label for="accion" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Acción</label>
                            <select name="accion" id="accion" 
                                    class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Todas las acciones</option>
                                @foreach($acciones as $acc)
                                    <option value="{{ $acc }}" {{ request('accion') == $acc ? 'selected' : '' }}>{{ $acc }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fecha Inicio -->
                        <div>
                            <label for="fecha_inicio" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Desde</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                                   class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Fecha Fin -->
                        <div>
                            <label for="fecha_fin" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Hasta</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" 
                                   class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Botones de Acción -->
                        <div class="md:col-span-5 flex justify-end gap-3 mt-2">
                            <a href="{{ route('admin.bitacora.index') }}" 
                               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Limpiar Filtros
                            </a>
                            <button type="submit" class="px-4 py-2 text-white font-semibold rounded-md text-sm hover:opacity-90 transition" style="background-color:#1F4E79;">
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead style="background-color:#1F4E79;">
                            <tr class="text-white text-left">
                                <th class="px-5 py-3 font-semibold w-12">ID</th>
                                <th class="px-5 py-3 font-semibold w-48">Fecha y Hora</th>
                                <th class="px-5 py-3 font-semibold w-40">Usuario</th>
                                <th class="px-5 py-3 font-semibold w-40 text-center">Acción</th>
                                <th class="px-5 py-3 font-semibold w-36">Módulo</th>
                                <th class="px-5 py-3 font-semibold">Descripción</th>
                                <th class="px-5 py-3 font-semibold w-32">Dirección IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3 text-gray-500 font-mono">{{ $log->id }}</td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    <span class="text-xs text-gray-400 block">{{ $log->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $log->user ? $log->user->name : 'Sistema (Auto)' }}
                                    </div>
                                    @if($log->user)
                                        <span class="text-xs text-gray-400 block">{{ $log->user->email }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php
                                        $accionNormalizada = strtoupper($log->accion);
                                        $badgeColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                        
                                        if (str_contains($accionNormalizada, 'CREAR')) {
                                            $badgeColor = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800/40';
                                        } elseif (str_contains($accionNormalizada, 'MODIFICAR')) {
                                            $badgeColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800/40';
                                        } elseif (str_contains($accionNormalizada, 'ELIMINAR')) {
                                            $badgeColor = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-800/40';
                                        } elseif (str_contains($accionNormalizada, 'VALIDAR') || str_contains($accionNormalizada, 'ACTIVAR') || str_contains($accionNormalizada, 'IMPORTAR') || str_contains($accionNormalizada, 'CALCULAR') || str_contains($accionNormalizada, 'ASIGNAR') || str_contains($accionNormalizada, 'PUBLICAR') || str_contains($accionNormalizada, 'GENERAR')) {
                                            $badgeColor = 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800/40';
                                        }
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $badgeColor }}">
                                        {{ $log->accion }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $log->modulo }}</span>
                                </td>
                                <td class="px-5 py-3 text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ $log->descripcion }}
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs whitespace-nowrap">
                                    {{ $log->ip ?? 'N/A' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span>No se encontraron registros en la bitácora que coincidan con los filtros.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($logs->hasPages())
                    <div class="px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
