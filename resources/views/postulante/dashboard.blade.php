<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Postulante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold">Bienvenido, {{ auth()->user()->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1">Panel de Postulante - CUP FICCT</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Mi Inscripcion</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver estado de mi inscripcion y requisitos</p>
                </a>
                <a href="{{ route('postulante.grupo.index') }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Mi Grupo</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver grupo y horario asignado</p>
                </a>
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Mis Notas</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver calificaciones por materia</p>
                </a>
                <a href="#" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 hover:bg-blue-50 transition">
                    <h4 class="font-bold text-gray-800 dark:text-gray-100">Resultado de Admision</h4>
                    <p class="text-gray-500 text-sm mt-1">Ver si fui admitido a la facultad</p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>