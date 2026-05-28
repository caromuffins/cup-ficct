<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Postulante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('admin.postulantes.update', $postulante->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Nombre completo</label>
                            <input type="text" name="name" value="{{ old('name', $postulante->name) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Telefono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $postulante->telefono) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Colegio</label>
                            <input type="text" name="colegio" value="{{ old('colegio', $postulante->colegio) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Ciudad</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad', $postulante->ciudad) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Fecha de nacimiento</label>
                            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $postulante->fecha_nacimiento) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Sexo</label>
                            <select name="sexo" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Seleccionar...</option>
                                <option value="M" {{ $postulante->sexo === 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ $postulante->sexo === 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Direccion</label>
                            <input type="text" name="direccion" value="{{ old('direccion', $postulante->direccion) }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Estado</label>
                            <select name="estado" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                @foreach(['pendiente','habilitado','inscrito','admitido','rechazado'] as $estado)
                                    <option value="{{ $estado }}" {{ $postulante->estado === $estado ? 'selected' : '' }}>
                                        {{ ucfirst($estado) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Guardar cambios</button>
                        <a href="{{ route('admin.postulantes.index') }}"
                            class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Cancelar</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
