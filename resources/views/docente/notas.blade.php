<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
                Registrar Notas
            </h2>
            <a href="{{ route('docente.grupos') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Mis grupos
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Selector de grupo y materia --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Seleccionar Grupo y Materia</h3>

                @if($grupos->isEmpty())
                <p class="text-yellow-600 text-sm">No tienes grupos asignados en la gestión activa.</p>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Grupo</label>
                        <select id="grupo_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2"
                                style="--tw-ring-color: #1F4E79;">
                            <option value="">Seleccionar grupo...</option>
                            @foreach($grupos as $g)
                                <option value="{{ $g->id }}"
                                    {{ request('grupo_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nombre }} ({{ ucfirst($g->turno) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Materia</label>
                        <select id="materia_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2">
                            <option value="">Seleccionar materia...</option>
                            @foreach($materias as $m)
                                <option value="{{ $m->id }}"
                                    {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button onclick="cargarAlumnos()"
                    class="mt-4 text-white font-semibold px-6 py-2 rounded-md transition-colors"
                    style="background-color: #1F4E79;"
                    onmouseover="this.style.backgroundColor='#163a5f'"
                    onmouseout="this.style.backgroundColor='#1F4E79'">
                    Cargar Alumnos
                </button>
                @endif
            </div>

            {{-- Tabla de notas (cargada vía AJAX) --}}
            <div id="tablaNotas" class="hidden">
                <form method="POST" action="{{ route('docente.notas.store') }}">
                    @csrf
                    <input type="hidden" name="materia_id" id="hidden_materia_id">
                    <input type="hidden" name="grupo_id" id="hidden_grupo_id">

                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 id="tablaTitle" class="font-bold text-gray-800 dark:text-gray-100">Notas</h3>
                            <button type="submit"
                                class="text-white font-semibold text-sm px-5 py-2 rounded-md transition-colors"
                                style="background-color: #1F4E79;"
                                onmouseover="this.style.backgroundColor='#163a5f'"
                                onmouseout="this.style.backgroundColor='#1F4E79'">
                                Guardar Notas
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead id="tablaHead" style="background-color: #1F4E79;" class="text-white">
                                    <tr id="theadRow"></tr>
                                </thead>
                                <tbody id="tablaBody" class="divide-y divide-gray-100 dark:divide-gray-700">
                                </tbody>
                            </table>
                        </div>
                        <div id="totalesFooter" class="p-4 bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-400 hidden">
                            <p>* Los campos en blanco no se guardan. La nota aprobatoria es &ge; 60 puntos.</p>
                        </div>
                    </div>
                </form>
            </div>

            <div id="loadingMsg" class="hidden text-center py-8 text-gray-400">
                Cargando alumnos...
            </div>

        </div>
    </div>

    <script>
    // Pre-cargar si vienen parámetros desde la URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const grupoParam   = urlParams.get('grupo_id');
        const materiaParam = urlParams.get('materia_id');

        if (grupoParam) {
            const sel = document.getElementById('grupo_id');
            if (sel) sel.value = grupoParam;
        }
        if (materiaParam) {
            const sel = document.getElementById('materia_id');
            if (sel) sel.value = materiaParam;
        }
        if (grupoParam && materiaParam) cargarAlumnos();
    });

    // Filtrar materias compatibles con el grupo seleccionado
    document.addEventListener('change', function(e) {
        if (e.target.id === 'grupo_id') filtrarMaterias();
    });

    function filtrarMaterias() {
        const grupoId = document.getElementById('grupo_id').value;
        const asignaciones = @json($asignaciones);
        const sel = document.getElementById('materia_id');

        sel.innerHTML = '<option value="">Seleccionar materia...</option>';
        asignaciones
            .filter(a => String(a.grupo_id) === String(grupoId))
            .forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.materia_id;
                opt.textContent = a.materia_nombre;
                sel.appendChild(opt);
            });

        // Auto-seleccionar si sólo hay una
        if (sel.options.length === 2) sel.selectedIndex = 1;
    }

    function cargarAlumnos() {
        const grupoId   = document.getElementById('grupo_id').value;
        const materiaId = document.getElementById('materia_id').value;

        if (!grupoId || !materiaId) {
            alert('Selecciona un grupo y una materia.');
            return;
        }

        document.getElementById('tablaNotas').classList.add('hidden');
        document.getElementById('loadingMsg').classList.remove('hidden');

        fetch(`{{ route('docente.notas.alumnos') }}?grupo_id=${grupoId}&materia_id=${materiaId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('loadingMsg').classList.add('hidden');

            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }

            document.getElementById('hidden_materia_id').value = materiaId;
            document.getElementById('hidden_grupo_id').value   = grupoId;

            // Construir encabezado
            const grupoNombre   = document.getElementById('grupo_id').options[document.getElementById('grupo_id').selectedIndex].text;
            const materiaNombre = document.getElementById('materia_id').options[document.getElementById('materia_id').selectedIndex].text;
            document.getElementById('tablaTitle').textContent = `${materiaNombre} — ${grupoNombre}`;

            let thead = '<th class="px-4 py-3 text-left font-medium">CI</th><th class="px-4 py-3 text-left font-medium">Alumno</th>';
            data.examenes.forEach(e => {
                thead += `<th class="px-4 py-3 text-center font-medium capitalize">${e.tipo}<br><span class="text-xs font-normal opacity-75">/${e.puntaje_maximo}</span></th>`;
            });
            thead += '<th class="px-4 py-3 text-center font-medium">Total</th><th class="px-4 py-3 text-center font-medium">Estado</th>';
            document.getElementById('theadRow').innerHTML = thead;

            // Construir cuerpo
            let tbody = '';
            data.alumnos.forEach(alumno => {
                tbody += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 text-gray-500 text-xs">${alumno.ci}</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-100 font-medium">${alumno.name}</td>`;

                let total = 0;
                data.examenes.forEach(examen => {
                    const notaExistente = data.notas[alumno.id]
                        ? data.notas[alumno.id].find(n => n.examen_id == examen.id)
                        : null;
                    const valorActual = notaExistente ? notaExistente.puntaje : '';
                    if (valorActual !== '') total += parseFloat(valorActual);

                    tbody += `<td class="px-4 py-3 text-center">
                        <input type="number" name="notas[${alumno.id}][${examen.id}]"
                            value="${valorActual}"
                            min="0" max="${examen.puntaje_maximo}" step="0.5"
                            class="w-16 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-center text-sm dark:bg-gray-700 dark:text-gray-100"
                            oninput="recalcularFila(this)"
                            data-alumno="${alumno.id}" data-max="${examen.puntaje_maximo}">
                    </td>`;
                });

                const aprobado = total >= 60;
                tbody += `<td class="px-4 py-3 text-center font-bold" id="total_${alumno.id}">${total > 0 ? total.toFixed(1) : '—'}</td>
                    <td class="px-4 py-3 text-center" id="estado_${alumno.id}">
                        ${total > 0 ? `<span class="px-2 py-1 rounded text-xs font-semibold ${aprobado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${aprobado ? 'APROBADO' : 'REPROBADO'}</span>` : '—'}
                    </td>
                </tr>`;
            });

            document.getElementById('tablaBody').innerHTML = tbody;
            document.getElementById('totalesFooter').classList.remove('hidden');
            document.getElementById('tablaNotas').classList.remove('hidden');
        })
        .catch(() => {
            document.getElementById('loadingMsg').classList.add('hidden');
            alert('Error al cargar los alumnos. Intenta de nuevo.');
        });
    }

    function recalcularFila(input) {
        const alumnoId = input.dataset.alumno;
        const fila = input.closest('tr');
        let total = 0;
        fila.querySelectorAll('input[type="number"]').forEach(inp => {
            const v = parseFloat(inp.value);
            if (!isNaN(v)) total += v;
        });
        const aprobado = total >= 60;
        const celdaTotal  = document.getElementById('total_' + alumnoId);
        const celdaEstado = document.getElementById('estado_' + alumnoId);
        if (celdaTotal)  celdaTotal.textContent = total.toFixed(1);
        if (celdaEstado) celdaEstado.innerHTML = `<span class="px-2 py-1 rounded text-xs font-semibold ${aprobado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${aprobado ? 'APROBADO' : 'REPROBADO'}</span>`;
    }
    </script>
</x-app-layout>
