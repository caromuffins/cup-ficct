<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reportes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Reporte Aprobados -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-2">
                        Reporte de Aprobados y Grupos
                    </h3>
                    <p class="text-gray-500 text-sm mb-4">
                        Ver resultados de admision con filtros por grupo y estado.
                    </p>
                    <a href="{{ route('admin.reportes.aprobados') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        Ver Reporte
                    </a>
                </div>

                <!-- Reporte Docentes -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-2">
                        Reporte de Docentes
                    </h3>
                    <p class="text-gray-500 text-sm mb-4">
                        Ver informacion de docentes, contratacion y carga horaria.
                    </p>
                    <a href="{{ route('admin.reportes.docentes') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        Ver Reporte
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
