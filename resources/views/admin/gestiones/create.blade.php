<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.gestiones.index') }}" class="text-gray-400 hover:text-gray-600">&larr;</a>
            <h2 class="font-semibold text-xl leading-tight" style="color:#1F4E79;">Nueva Gestión</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.gestiones.store') }}">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="anio" value="Año" />
                            <x-text-input id="anio" name="anio" type="number" class="mt-1 block w-full"
                                :value="old('anio', date('Y'))" min="2020" max="2099" required />
                            <x-input-error :messages="$errors->get('anio')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="periodo" value="Período" />
                            <select id="periodo" name="periodo"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-100">
                                <option value="primero" {{ old('periodo')==='primero'?'selected':'' }}>Primero</option>
                                <option value="segundo" {{ old('periodo')==='segundo'?'selected':'' }}>Segundo</option>
                            </select>
                            <x-input-error :messages="$errors->get('periodo')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="fecha_inicio" value="Fecha de inicio" />
                            <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="mt-1 block w-full"
                                :value="old('fecha_inicio')" required />
                            <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="fecha_fin" value="Fecha de fin" />
                            <x-text-input id="fecha_fin" name="fecha_fin" type="date" class="mt-1 block w-full"
                                :value="old('fecha_fin')" required />
                            <x-input-error :messages="$errors->get('fecha_fin')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="cupo_por_carrera" value="Cupo por carrera" />
                            <x-text-input id="cupo_por_carrera" name="cupo_por_carrera" type="number"
                                class="mt-1 block w-full" :value="old('cupo_por_carrera', 80)" min="1" required />
                            <x-input-error :messages="$errors->get('cupo_por_carrera')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="monto_inscripcion" value="Monto inscripción (USD)" />
                            <x-text-input id="monto_inscripcion" name="monto_inscripcion" type="number"
                                class="mt-1 block w-full" :value="old('monto_inscripcion', 200)" min="0" step="0.01" required />
                            <x-input-error :messages="$errors->get('monto_inscripcion')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('admin.gestiones.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white rounded-md"
                            style="background-color:#1F4E79;">
                            Crear gestión
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
