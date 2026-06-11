<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
                Carga Masiva de Postulantes
            </h2>
            <a href="{{ route('admin.postulantes.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Volver a postulantes
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Resultado de la importación --}}
            @if(session('import_importados') !== null)
            <div class="bg-white shadow-sm rounded-lg border-l-4 p-6
                {{ session('import_importados') > 0 ? 'border-green-500' : 'border-yellow-500' }}">
                <h3 class="font-bold text-gray-800 mb-3">Resultado de la importación</h3>
                <div class="flex flex-wrap gap-6 text-sm mb-3">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                        <strong class="text-green-700">{{ session('import_importados') }}</strong>
                        <span class="text-gray-600">postulantes importados</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                        <strong class="text-red-700">{{ session('import_omitidos') }}</strong>
                        <span class="text-gray-600">filas omitidas</span>
                    </span>
                </div>
                @if(session('import_errores') && count(session('import_errores')) > 0)
                <details class="mt-3">
                    <summary class="cursor-pointer text-sm font-medium text-gray-600 hover:text-gray-800 select-none">
                        Ver detalle de errores ({{ count(session('import_errores')) }})
                    </summary>
                    <ul class="mt-3 text-xs text-red-700 space-y-1 list-disc list-inside bg-red-50 rounded p-3 max-h-48 overflow-y-auto">
                        @foreach(session('import_errores') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </details>
                @endif
            </div>
            @endif

            {{-- Paso 1: Descargar plantilla --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full text-white flex items-center justify-center text-sm font-bold shrink-0"
                         style="background-color: #1F4E79;">1</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 mb-1">Descargar plantilla</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Usa esta plantilla CSV como base. Los campos <strong>ci</strong>, <strong>nombre</strong>
                            y <strong>email</strong> son obligatorios.
                        </p>
                        <a href="{{ route('admin.postulantes.plantilla') }}"
                           class="inline-flex items-center gap-2 text-white text-sm font-semibold px-4 py-2 rounded-md"
                           style="background-color: #374151;"
                           onmouseover="this.style.backgroundColor='#1f2937'"
                           onmouseout="this.style.backgroundColor='#374151'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Descargar plantilla_postulantes.csv
                        </a>
                    </div>
                </div>
            </div>

            {{-- Paso 2: Subir archivo --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full text-white flex items-center justify-center text-sm font-bold shrink-0"
                         style="background-color: #1F4E79;">2</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 mb-1">Subir archivo</h3>
                        <p class="text-sm text-gray-500 mb-1">
                            Formatos soportados: <strong>.xlsx</strong>, <strong>.xls</strong> o <strong>.csv</strong> (máx. 4 MB).
                        </p>
                        <p class="text-sm text-gray-500 mb-4">
                            La contraseña inicial de cada postulante será su número de <strong>CI</strong>.
                            Se omitirán filas con CI o email duplicados.
                        </p>

                        <form method="POST" action="{{ route('admin.postulantes.importar.procesar') }}"
                              enctype="multipart/form-data">
                            @csrf

                            @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-md mb-4 text-sm">
                                {{ $errors->first() }}
                            </div>
                            @endif

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Archivo de postulantes
                                </label>
                                <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required
                                       class="block w-full text-sm text-gray-600
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:text-white file:cursor-pointer
                                              border border-gray-200 rounded-lg p-2"
                                       style="--tw-file-bg: #1F4E79;"
                                       onfocus="this.style.borderColor='#1F4E79'"
                                       onblur="this.style.borderColor='#e5e7eb'">
                                <style>
                                    input[type="file"]::file-selector-button {
                                        background-color: #1F4E79;
                                        color: white;
                                        padding: 6px 16px;
                                        border-radius: 6px;
                                        border: none;
                                        font-size: 13px;
                                        font-weight: 600;
                                        cursor: pointer;
                                        margin-right: 12px;
                                    }
                                    input[type="file"]::file-selector-button:hover {
                                        background-color: #163a5f;
                                    }
                                </style>
                            </div>

                            <button type="submit"
                                class="text-white font-semibold text-sm py-2 px-6 rounded-md transition-colors"
                                style="background-color: #1F4E79;"
                                onmouseover="this.style.backgroundColor='#163a5f'"
                                onmouseout="this.style.backgroundColor='#1F4E79'">
                                Importar postulantes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Referencia de columnas --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Referencia de columnas del archivo</h3>
                </div>
                <table class="w-full text-sm">
                    <thead style="background-color: #1F4E79;" class="text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Columna</th>
                            <th class="px-4 py-3 text-left font-medium">Obligatorio</th>
                            <th class="px-4 py-3 text-left font-medium">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs bg-gray-50 font-semibold">ci</td>
                            <td class="px-4 py-3"><span class="text-red-600 font-bold">Sí</span></td>
                            <td class="px-4 py-3 text-gray-600">Número de carnet de identidad (único en el sistema)</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs bg-gray-50 font-semibold">nombre</td>
                            <td class="px-4 py-3"><span class="text-red-600 font-bold">Sí</span></td>
                            <td class="px-4 py-3 text-gray-600">Nombre completo del postulante</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs bg-gray-50 font-semibold">email</td>
                            <td class="px-4 py-3"><span class="text-red-600 font-bold">Sí</span></td>
                            <td class="px-4 py-3 text-gray-600">Correo electrónico (único en el sistema)</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs bg-gray-50">telefono</td>
                            <td class="px-4 py-3 text-gray-400">No</td>
                            <td class="px-4 py-3 text-gray-600">Número de teléfono o celular</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs bg-gray-50">ciudad</td>
                            <td class="px-4 py-3 text-gray-400">No</td>
                            <td class="px-4 py-3 text-gray-600">Ciudad de procedencia</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs bg-gray-50">colegio</td>
                            <td class="px-4 py-3 text-gray-400">No</td>
                            <td class="px-4 py-3 text-gray-600">Nombre del colegio de egreso</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs bg-gray-50">fecha_nacimiento</td>
                            <td class="px-4 py-3 text-gray-400">No</td>
                            <td class="px-4 py-3 text-gray-600">Formato: <code class="bg-gray-100 px-1 rounded">YYYY-MM-DD</code> (ej. 2001-03-22)</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs bg-gray-50">sexo</td>
                            <td class="px-4 py-3 text-gray-400">No</td>
                            <td class="px-4 py-3 text-gray-600">Solo <code class="bg-gray-100 px-1 rounded">M</code> (masculino) o <code class="bg-gray-100 px-1 rounded">F</code> (femenino)</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
