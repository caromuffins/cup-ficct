<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Registrar Docente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.docentes.store') }}">
                    @csrf

                    @if(session('error'))
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
                    @endif

                    <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Datos de Acceso</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Nombre completo</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">CI</label>
                            <input type="text" name="ci" value="{{ old('ci') }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            @error('ci')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Password</label>
                            <input type="password" name="password"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Especialidad</label>
                            <select name="especialidad" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Seleccionar...</option>
                                @foreach(['Matematicas','Fisica','Computacion','Ingles'] as $esp)
                                    <option value="{{ $esp }}" {{ old('especialidad')===$esp?'selected':'' }}>{{ $esp }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Requisitos de Contratación</h3>
                    <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded p-2 mb-4">
                        Para ser contratado, el docente debe tener al menos Maestría o Diplomado en Educación Superior.
                    </p>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="col-span-2">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Título Profesional</label>
                            <input type="text" name="titulo_profesional" value="{{ old('titulo_profesional') }}"
                                placeholder="Ej: Ingeniero en Sistemas"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">
                                <input type="checkbox" name="tiene_maestria" value="1" {{ old('tiene_maestria') ? 'checked' : '' }}>
                                Tiene Maestría
                            </label>
                            <input type="text" name="area_maestria" value="{{ old('area_maestria') }}"
                                placeholder="Área de maestría"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">
                                <input type="checkbox" name="tiene_diplomado" value="1" {{ old('tiene_diplomado') ? 'checked' : '' }}>
                                Tiene Diplomado en Educación Superior
                            </label>
                            <input type="text" name="area_diplomado" value="{{ old('area_diplomado') }}"
                                placeholder="Área del diplomado"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Estado de Contratación</label>
                            <select name="estado_contratacion" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="pendiente" {{ old('estado_contratacion')==='pendiente'?'selected':'' }}>Pendiente</option>
                                <option value="contratado" {{ old('estado_contratacion')==='contratado'?'selected':'' }}>Contratado</option>
                                <option value="rechazado" {{ old('estado_contratacion')==='rechazado'?'selected':'' }}>Rechazado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Máx. grupos a dictar</label>
                            <select name="max_grupos" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                @foreach([1,2,3,4] as $n)
                                    <option value="{{ $n }}" {{ old('max_grupos', 1) == $n ? 'selected' : '' }}>{{ $n }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Registrar Docente</button>
                        <a href="{{ route('admin.docentes.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
