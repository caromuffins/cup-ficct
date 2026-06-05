<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Docente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.docentes.update', $docente->id) }}">
                    @csrf
                    @method('PUT')

                    <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Datos Personales</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Nombre completo</label>
                            <input type="text" name="name" value="{{ old('name', $docente->name) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Especialidad</label>
                            <input type="text" name="especialidad" value="{{ old('especialidad', $docente->especialidad) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Max. Grupos</label>
                            <select name="max_grupos" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                @foreach([1,2,3,4] as $n)
                                    <option value="{{ $n }}" {{ $docente->max_grupos == $n ? 'selected' : '' }}>{{ $n }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Requisitos de Contratacion</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="col-span-2">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Titulo Profesional</label>
                            <input type="text" name="titulo_profesional" value="{{ old('titulo_profesional', $docente->titulo_profesional) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">
                                <input type="checkbox" name="tiene_maestria" value="1" {{ $docente->tiene_maestria ? 'checked' : '' }}>
                                Tiene Maestria
                            </label>
                            <input type="text" name="area_maestria" value="{{ old('area_maestria', $docente->area_maestria) }}"
                                placeholder="Area de maestria"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">
                                <input type="checkbox" name="tiene_diplomado" value="1" {{ $docente->tiene_diplomado ? 'checked' : '' }}>
                                Tiene Diplomado en Educacion Superior
                            </label>
                            <input type="text" name="area_diplomado" value="{{ old('area_diplomado', $docente->area_diplomado) }}"
                                placeholder="Area del diplomado"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Estado de Contratacion</label>
                            <select name="estado_contratacion" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                @foreach(['pendiente', 'contratado', 'rechazado'] as $estado)
                                    <option value="{{ $estado }}" {{ $docente->estado_contratacion === $estado ? 'selected' : '' }}>
                                        {{ ucfirst($estado) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Guardar cambios</button>
                        <a href="{{ route('admin.docentes.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
