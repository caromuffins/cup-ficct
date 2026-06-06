<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Asignacion de Horarios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4">{{ session('error') }}</div>
            @endif

            <!-- Formulario de asignacion -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Asignar Horario</h3>
                <form method="POST" action="{{ route('admin.horarios.store') }}">
                    @csrf
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Grupo</label>
                            <select name="grupo_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Seleccionar...</option>
                                @foreach($grupos as $g)
                                    <option value="{{ $g->id }}" {{ old('grupo_id')==$g->id?'selected':'' }}>
                                        {{ $g->nombre }} ({{ ucfirst($g->turno) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('grupo_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Materia</label>
                            <select name="materia_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Seleccionar...</option>
                                @foreach($materias as $m)
                                    <option value="{{ $m->id }}" {{ old('materia_id')==$m->id?'selected':'' }}>
                                        {{ $m->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('materia_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Docente</label>
                            <select name="docente_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Seleccionar...</option>
                                @foreach($docentes as $d)
                                    <option value="{{ $d->id }}" {{ old('docente_id')==$d->id?'selected':'' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('docente_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Aula</label>
                            <select name="aula_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Seleccionar...</option>
                                @foreach($aulas as $a)
                                    <option value="{{ $a->id }}" {{ old('aula_id')==$a->id?'selected':'' }}>
                                        {{ $a->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('aula_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Dia</label>
                            <select name="dia" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">Seleccionar...</option>
                                @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $dia)
                                    <option value="{{ $dia }}" {{ old('dia')===$dia?'selected':'' }}>
                                        {{ ucfirst($dia) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dia')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Hora Inicio</label>
                            <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            @error('hora_inicio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Hora Fin</label>
                            <input type="time" name="hora_fin" value="{{ old('hora_fin') }}"
                                class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            @error('hora_fin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Asignar Horario
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Lista de horarios -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100">
                        Horarios de la Gestion {{ ucfirst($gestion->periodo) }} {{ $gestion->anio }}
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-blue-900 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Grupo</th>
                            <th class="px-4 py-3 text-left">Materia</th>
                            <th class="px-4 py-3 text-left">Docente</th>
                            <th class="px-4 py-3 text-left">Aula</th>
                            <th class="px-4 py-3 text-left">Dia</th>
                            <th class="px-4 py-3 text-left">Horario</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($horarios as $h)
                        <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                                {{ $h->grupo }}
                                <span class="text-xs px-1 py-0.5 rounded
                                    {{ $h->turno === 'maniana' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($h->turno) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $h->materia }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $h->docente }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $h->aula }}</td>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ ucfirst($h->dia) }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ substr($h->hora_inicio, 0, 5) }} - {{ substr($h->hora_fin, 0, 5) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('admin.horarios.destroy', $h->id) }}"
                                      onsubmit="return confirm('Eliminar este horario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No hay horarios asignados aun.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const materiaSelect = document.querySelector('select[name="materia_id"]');
        const docenteSelect = document.querySelector('select[name="docente_id"]');

        const docentes = @json($docentes);

        materiaSelect.addEventListener('change', function() {
            const materiaTexto = materiaSelect.options[materiaSelect.selectedIndex].text.toLowerCase();

            docenteSelect.innerHTML = '<option value="">Seleccionar...</option>';

            docentes.forEach(function(d) {
                const especialidad = (d.especialidad || '').toLowerCase();
                if (especialidad === '' || especialidad.includes(materiaTexto) || materiaTexto.includes(especialidad)) {
                    const option = document.createElement('option');
                    option.value = d.id;
                    option.textContent = d.name + (d.especialidad ? ' (' + d.especialidad + ')' : '');
                    docenteSelect.appendChild(option);
                }
            });

            // Si no hay docentes para esa materia, mostrar todos
            if (docenteSelect.options.length === 1) {
                docentes.forEach(function(d) {
                    const option = document.createElement('option');
                    option.value = d.id;
                    option.textContent = d.name + (d.especialidad ? ' (' + d.especialidad + ')' : '');
                    docenteSelect.appendChild(option);
                });
            }
        });
    });
    </script>
</x-app-layout>
