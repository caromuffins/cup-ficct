<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Administrador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Bienvenida -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold">Bienvenido, {{ auth()->user()->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1">Sistema de Gestion CUP - FICCT UAGRM</p>
                </div>
            </div>

            <!-- Tarjetas de estadisticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['postulantes'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Postulantes</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['grupos'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Grupos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['docentes'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Docentes</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['admitidos'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">Admitidos</p>
                </div>
            </div>

            <!-- Menu de acciones -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Gestionar Inscripciones</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver y validar inscripciones de postulantes</p>
                </a>
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Gestionar Grupos</h4>
                    <p class="text-gray-500 text-sm mt-1">Generar y administrar grupos del CUP</p>
                </a>
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Gestionar Docentes</h4>
                    <p class="text-gray-500 text-sm mt-1">Registrar y asignar docentes</p>
                </a>
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Evaluaciones</h4>
                    <p class="text-gray-500 text-sm mt-1">Gestionar examenes y notas</p>
                </a>
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Admision</h4>
                    <p class="text-gray-500 text-sm mt-1">Publicar lista de admitidos</p>
                </a>
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Reportes</h4>
                    <p class="text-gray-500 text-sm mt-1">Generar reportes en PDF y Excel</p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>