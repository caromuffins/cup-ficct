<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color: #1F4E79;">
                Registrar Asistencia de Clases
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
            @if(session('error'))
                <div class="bg-red-100 border border-red-200 text-red-800 p-4 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Selector de grupo, materia y fecha --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Seleccionar Grupo, Materia y Fecha</h3>

                @if($grupos->isEmpty())
                <p class="text-yellow-600 text-sm">No tienes grupos asignados en la gestión activa.</p>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Grupo</label>
                        <select id="grupo_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2" style="--tw-ring-color: #1F4E79;">
                            <option value="">Seleccionar grupo...</option>
                            @foreach($grupos as $g)
                                <option value="{{ $g->id }}" {{ request('grupo_id') == $g->id ? 'selected' : '' }}>
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
                                <option value="{{ $m->id }}" {{ request('materia_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Fecha</label>
                        <input type="date" id="fecha" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 mt-4">
                    <button onclick="cargarAlumnos()"
                        class="text-white font-semibold px-6 py-2 rounded-md transition-colors"
                        style="background-color: #1F4E79;"
                        onmouseover="this.style.backgroundColor='#163a5f'"
                        onmouseout="this.style.backgroundColor='#1F4E79'">
                        Cargar Alumnos
                    </button>
                    <a id="btnHistorial" href="#" class="hidden px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Ver Historial
                    </a>
                </div>
                @endif
            </div>

            {{-- Tabla de asistencia (cargada vía AJAX) --}}
            <div id="tablaAsistencia" class="hidden">
                <form method="POST" action="{{ route('docente.asistencia.store') }}">
                    @csrf
                    <input type="hidden" name="materia_id" id="hidden_materia_id">
                    <input type="hidden" name="grupo_id" id="hidden_grupo_id">
                    <input type="hidden" name="fecha" id="hidden_fecha">

                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <h3 id="tablaTitle" class="font-bold text-gray-800 dark:text-gray-100 text-base">Asistencia</h3>
                                <p id="asistenciaFechaSub" class="text-xs text-gray-400 mt-0.5"></p>
                            </div>
                            <button type="submit"
                                class="text-white font-semibold text-sm px-5 py-2 rounded-md transition-colors"
                                style="background-color: #1F4E79;"
                                onmouseover="this.style.backgroundColor='#163a5f'"
                                onmouseout="this.style.backgroundColor='#1F4E79'">
                                Guardar Asistencia
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead style="background-color: #1F4E79;" class="text-white">
                                    <tr>
                                        <th class="px-5 py-3 text-left font-semibold w-32">CI</th>
                                        <th class="px-5 py-3 text-left font-semibold">Postulante</th>
                                        <th class="px-5 py-3 text-center font-semibold w-80">Estado de Asistencia</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaBody" class="divide-y divide-gray-100 dark:divide-gray-700">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>

            <div id="loadingMsg" class="hidden text-center py-8 text-gray-400">
                Cargando alumnos y estado de asistencia...
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
            filtrarMaterias();
            const sel = document.getElementById('materia_id');
            if (sel) sel.value = materiaParam;
        }
        if (grupoParam && materiaParam) cargarAlumnos();
    });

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

        if (sel.options.length === 2) sel.selectedIndex = 1;
    }

    function cargarAlumnos() {
        const grupoId   = document.getElementById('grupo_id').value;
        const materiaId = document.getElementById('materia_id').value;
        const fechaVal  = document.getElementById('fecha').value;

        if (!grupoId || !materiaId || !fechaVal) {
            alert('Selecciona grupo, materia y fecha.');
            return;
        }

        document.getElementById('tablaAsistencia').classList.add('hidden');
        document.getElementById('loadingMsg').classList.remove('hidden');

        // Configurar enlace del historial
        const btnHistorial = document.getElementById('btnHistorial');
        btnHistorial.href = `{{ route('docente.asistencia.historial') }}?grupo_id=${grupoId}&materia_id=${materiaId}`;
        btnHistorial.classList.remove('hidden');

        fetch(`{{ route('docente.asistencia.alumnos') }}?grupo_id=${grupoId}&materia_id=${materiaId}&fecha=${fechaVal}`, {
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
            document.getElementById('hidden_fecha').value      = fechaVal;

            const grupoNombre   = document.getElementById('grupo_id').options[document.getElementById('grupo_id').selectedIndex].text;
            const materiaNombre = document.getElementById('materia_id').options[document.getElementById('materia_id').selectedIndex].text;
            document.getElementById('tablaTitle').textContent = `${materiaNombre} — ${grupoNombre}`;
            
            // Formatear fecha para el subtítulo
            const partesFecha = fechaVal.split('-');
            const fechaFormateada = `${partesFecha[2]}/${partesFecha[1]}/${partesFecha[0]}`;
            document.getElementById('asistenciaFechaSub').textContent = `Clase del día: ${fechaFormateada}`;

            let tbody = '';
            data.alumnos.forEach(alumno => {
                const marcada = data.asistenciasMarcadas[alumno.id];
                const estadoActual = marcada ? marcada.estado : 'presente'; // Por defecto presente

                tbody += `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-5 py-3 font-mono text-gray-500 text-xs">${alumno.ci}</td>
                    <td class="px-5 py-3 font-semibold text-gray-800 dark:text-gray-100">${alumno.name}</td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex justify-center items-center gap-6">
                            <label class="inline-flex items-center cursor-pointer select-none">
                                <input type="radio" name="asistencia[${alumno.id}]" value="presente" 
                                    ${estadoActual === 'presente' ? 'checked' : ''} 
                                    class="text-green-600 focus:ring-green-500 border-gray-300 dark:border-gray-700 focus:ring-2">
                                <span class="ml-2 text-sm font-semibold text-green-700 dark:text-green-400">Presente</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer select-none">
                                <input type="radio" name="asistencia[${alumno.id}]" value="falta" 
                                    ${estadoActual === 'falta' ? 'checked' : ''} 
                                    class="text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-700 focus:ring-2">
                                <span class="ml-2 text-sm font-semibold text-red-700 dark:text-red-400">Falta</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer select-none">
                                <input type="radio" name="asistencia[${alumno.id}]" value="licencia" 
                                    ${estadoActual === 'licencia' ? 'checked' : ''} 
                                    class="text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-700 focus:ring-2">
                                <span class="ml-2 text-sm font-semibold text-yellow-700 dark:text-yellow-400">Licencia</span>
                            </label>
                        </div>
                    </td>
                </tr>`;
            });

            document.getElementById('tablaBody').innerHTML = tbody;
            document.getElementById('tablaAsistencia').classList.remove('hidden');
        })
        .catch(() => {
            document.getElementById('loadingMsg').classList.add('hidden');
            alert('Error al cargar la lista de alumnos. Intenta de nuevo.');
        });
    }
    </script>
</x-app-layout>
