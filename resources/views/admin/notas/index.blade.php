<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Registro de Notas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Seleccionar Grupo y Materia</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Grupo</label>
                        <select id="grupo_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Seleccionar grupo...</option>
                            @foreach($grupos as $g)
                                <option value="{{ $g->id }}">{{ $g->nombre }} ({{ ucfirst($g->turno) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Materia</label>
                        <select id="materia_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Seleccionar materia...</option>
                            @foreach($materias as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button id="btnCargar" onclick="cargarAlumnos()"
                    class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Cargar Alumnos
                </button>
            </div>

            <!-- Tabla de notas -->
            <div id="tablaNotas" class="hidden">
                <form method="POST" action="{{ route('admin.notas.store') }}">
                    @csrf
                    <input type="hidden" name="materia_id" id="hidden_materia_id">
                    <input type="hidden" name="grupo_id" id="hidden_grupo_id">

                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-4 border-b flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100" id="tituloTabla">
                                Notas
                            </h3>
                            <div class="text-sm text-gray-500">
                                <span id="modoEval"></span>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-blue-900 text-white">
                                    <tr>
                                        <th class="px-4 py-3 text-left">CI</th>
                                        <th class="px-4 py-3 text-left">Nombre</th>
                                        <th class="px-4 py-3 text-center">Parcial 1 (30pts)</th>
                                        <th class="px-4 py-3 text-center">Parcial 2 (30pts)</th>
                                        <th class="px-4 py-3 text-center">Final (40pts)</th>
                                        <th class="px-4 py-3 text-center">Total</th>
                                        <th class="px-4 py-3 text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyNotas">
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t">
                            <button type="submit"
                                class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                                Guardar Notas
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
    let examenesData = [];

    async function cargarAlumnos() {
        const grupo_id   = document.getElementById('grupo_id').value;
        const materia_id = document.getElementById('materia_id').value;

        if (!grupo_id || !materia_id) {
            alert('Selecciona un grupo y una materia');
            return;
        }

        const response = await fetch(`{{ route('admin.notas.alumnos') }}?grupo_id=${grupo_id}&materia_id=${materia_id}`);
        const data = await response.json();

        examenesData = data.examenes;
        document.getElementById('hidden_materia_id').value = materia_id;
        document.getElementById('hidden_grupo_id').value = grupo_id;

        const materia = document.getElementById('materia_id').options[document.getElementById('materia_id').selectedIndex].text;
        const grupo   = document.getElementById('grupo_id').options[document.getElementById('grupo_id').selectedIndex].text;
        document.getElementById('tituloTabla').textContent = `Notas — ${materia} — ${grupo}`;

        const tbody = document.getElementById('tbodyNotas');
        tbody.innerHTML = '';

        data.alumnos.forEach((alumno, i) => {
            const notas = data.notas[alumno.id] || [];
            const notaMap = {};
            notas.forEach(n => notaMap[n.examen_id] = n.puntaje);

            const p1 = examenesData.find(e => e.tipo === 'parcial1');
            const p2 = examenesData.find(e => e.tipo === 'parcial2');
            const pf = examenesData.find(e => e.tipo === 'final');

            const p1val = notaMap[p1?.id] ?? '';
            const p2val = notaMap[p2?.id] ?? '';
            const pfval = notaMap[pf?.id] ?? '';

            const bg = i % 2 === 0 ? '' : 'bg-gray-50 dark:bg-gray-700';

            tbody.innerHTML += `
                <tr class="${bg}">
                    <td class="px-4 py-2 text-gray-600">${alumno.ci}</td>
                    <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100">${alumno.name}</td>
                    <td class="px-4 py-2 text-center">
                        <input type="number" name="notas[${alumno.id}][${p1?.id}]"
                            value="${p1val}" min="0" max="30" step="0.01"
                            onchange="calcularTotal(this, ${alumno.id})"
                            class="w-20 border rounded px-2 py-1 text-center dark:bg-gray-600 dark:text-gray-100"
                            id="p1_${alumno.id}">
                    </td>
                    <td class="px-4 py-2 text-center">
                        <input type="number" name="notas[${alumno.id}][${p2?.id}]"
                            value="${p2val}" min="0" max="30" step="0.01"
                            onchange="calcularTotal(this, ${alumno.id})"
                            class="w-20 border rounded px-2 py-1 text-center dark:bg-gray-600 dark:text-gray-100"
                            id="p2_${alumno.id}">
                    </td>
                    <td class="px-4 py-2 text-center">
                        <input type="number" name="notas[${alumno.id}][${pf?.id}]"
                            value="${pfval}" min="0" max="40" step="0.01"
                            onchange="calcularTotal(this, ${alumno.id})"
                            class="w-20 border rounded px-2 py-1 text-center dark:bg-gray-600 dark:text-gray-100"
                            id="pf_${alumno.id}">
                    </td>
                    <td class="px-4 py-2 text-center font-bold" id="total_${alumno.id}">
                        ${calcTotal(p1val, p2val, pfval)}
                    </td>
                    <td class="px-4 py-2 text-center" id="estado_${alumno.id}">
                        ${estadoBadge(calcTotal(p1val, p2val, pfval))}
                    </td>
                </tr>
            `;
        });

        document.getElementById('tablaNotas').classList.remove('hidden');
    }

    function calcTotal(p1, p2, pf) {
        const v1 = parseFloat(p1) || 0;
        const v2 = parseFloat(p2) || 0;
        const v3 = parseFloat(pf) || 0;
        return (v1 + v2 + v3).toFixed(2);
    }

    function estadoBadge(total) {
        const t = parseFloat(total);
        if (t >= 60) {
            return '<span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">APROBADO</span>';
        } else if (t > 0) {
            return '<span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">REPROBADO</span>';
        }
        return '<span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500">—</span>';
    }

    function calcularTotal(input, alumnoId) {
        const p1 = parseFloat(document.getElementById(`p1_${alumnoId}`)?.value) || 0;
        const p2 = parseFloat(document.getElementById(`p2_${alumnoId}`)?.value) || 0;
        const pf = parseFloat(document.getElementById(`pf_${alumnoId}`)?.value) || 0;
        const total = (p1 + p2 + pf).toFixed(2);
        document.getElementById(`total_${alumnoId}`).textContent = total;
        document.getElementById(`estado_${alumnoId}`).innerHTML = estadoBadge(total);
    }
    </script>
</x-app-layout>
