<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
            Panel de Administrador
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Bienvenida -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4"
                 style="border-left-color: #1F4E79;">
                <div class="p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shrink-0"
                         style="background-color: #1F4E79;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Bienvenido, {{ auth()->user()->name }}</h3>
                        <p class="text-sm mt-0.5" style="color: #1F4E79;">Sistema de Gestión CUP &ndash; FICCT UAGRM</p>
                    </div>
                </div>
            </div>

            <!-- Cards de estadísticas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5 text-center border-t-2" style="border-top-color: #1F4E79;">
                    <p class="text-3xl font-bold" style="color: #1F4E79;">{{ $stats['total_postulantes'] }}</p>
                    <p class="text-gray-500 text-xs mt-1 font-medium uppercase tracking-wide">Total Postulantes</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 text-center border-t-2 border-green-500">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_habilitados'] }}</p>
                    <p class="text-gray-500 text-xs mt-1 font-medium uppercase tracking-wide">Habilitados</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 text-center border-t-2 border-yellow-500">
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['requisitos_pendientes'] }}</p>
                    <p class="text-gray-500 text-xs mt-1 font-medium uppercase tracking-wide">Req. Pendientes</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 text-center border-t-2 border-purple-500">
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_grupos'] }}</p>
                    <p class="text-gray-500 text-xs mt-1 font-medium uppercase tracking-wide">Grupos Creados</p>
                </div>
            </div>

            <!-- Menú de acciones -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">Módulos del Sistema</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                    <a href="{{ route('admin.postulantes.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Inscripciones</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Ver y validar inscripciones de postulantes</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.grupos.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Grupos</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Generar y administrar grupos del CUP</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.horarios.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Horarios</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Asignar docentes, aulas y horarios</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.docentes.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Docentes</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Registrar y asignar docentes</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.notas.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Evaluaciones</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Gestionar exámenes y notas</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.admision.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #7F0000;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-red-800">Admisión</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Calcular y publicar lista de admitidos</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.reportes.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Reportes</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Generar reportes en PDF y Excel</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.consultas.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Consultas</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Consultas dinámicas y filtros avanzados</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.bitacora.index') }}"
                       class="bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow group flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-white"
                             style="background-color: #1F4E79;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Bitácora</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Auditoría y registro de acciones del sistema</p>
                        </div>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
