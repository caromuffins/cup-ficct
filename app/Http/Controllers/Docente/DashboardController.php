<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function getDocente()
    {
        return DB::table('docentes')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->select('docentes.*', 'users.name', 'users.email')
            ->where('users.id', auth()->id())
            ->first();
    }

    private function getGestion()
    {
        return DB::table('gestiones')->where('activa', true)->first();
    }

    public function index()
    {
        $docente = $this->getDocente();
        $gestion = $this->getGestion();

        $stats        = ['grupos' => 0, 'alumnos' => 0, 'materias' => 0];
        $asignaciones = collect();

        if ($docente && $gestion) {
            $gruposIds = DB::table('asignacion_docentes')
                ->where('docente_id', $docente->id)
                ->where('gestion_id', $gestion->id)
                ->distinct()
                ->pluck('grupo_id');

            $stats['grupos']   = $gruposIds->count();
            $stats['materias'] = DB::table('asignacion_docentes')
                ->where('docente_id', $docente->id)
                ->where('gestion_id', $gestion->id)
                ->distinct()
                ->count('materia_id');
            $stats['alumnos'] = $gruposIds->isEmpty() ? 0
                : DB::table('asignacion_grupos')->whereIn('grupo_id', $gruposIds)->count();

            $asignaciones = DB::table('asignacion_docentes')
                ->join('grupos', 'asignacion_docentes.grupo_id', '=', 'grupos.id')
                ->join('materias', 'asignacion_docentes.materia_id', '=', 'materias.id')
                ->select('grupos.nombre as grupo_nombre', 'grupos.turno', 'materias.nombre as materia_nombre')
                ->where('asignacion_docentes.docente_id', $docente->id)
                ->where('asignacion_docentes.gestion_id', $gestion->id)
                ->orderBy('grupos.nombre')
                ->get();
        }

        return view('docente.dashboard', compact('docente', 'gestion', 'stats', 'asignaciones'));
    }

    public function grupos()
    {
        $docente = $this->getDocente();
        $gestion = $this->getGestion();

        if (!$docente || !$gestion) {
            return view('docente.grupos', compact('docente', 'gestion') + ['grupos' => collect()]);
        }

        $asignaciones = DB::table('asignacion_docentes')
            ->join('grupos', 'asignacion_docentes.grupo_id', '=', 'grupos.id')
            ->join('materias', 'asignacion_docentes.materia_id', '=', 'materias.id')
            ->select(
                'asignacion_docentes.grupo_id',
                'asignacion_docentes.materia_id',
                'grupos.nombre as grupo_nombre',
                'grupos.turno',
                'materias.nombre as materia_nombre'
            )
            ->where('asignacion_docentes.docente_id', $docente->id)
            ->where('asignacion_docentes.gestion_id', $gestion->id)
            ->orderBy('grupos.nombre')
            ->get();

        $grupos = $asignaciones->map(function ($a) {
            $a->horario = DB::table('horarios')
                ->join('aulas', 'horarios.aula_id', '=', 'aulas.id')
                ->select('horarios.dia', 'horarios.hora_inicio', 'horarios.hora_fin', 'aulas.nombre as aula_nombre', 'aulas.edificio as aula_codigo')
                ->where('horarios.grupo_id', $a->grupo_id)
                ->where('horarios.materia_id', $a->materia_id)
                ->first();

            $a->alumnos_count = DB::table('asignacion_grupos')->where('grupo_id', $a->grupo_id)->count();
            return $a;
        });

        return view('docente.grupos', compact('docente', 'gestion', 'grupos'));
    }

    public function horario()
    {
        $docente = $this->getDocente();
        $gestion = $this->getGestion();

        $horario = collect();

        if ($docente) {
            $horario = DB::table('horarios')
                ->join('materias', 'horarios.materia_id', '=', 'materias.id')
                ->join('grupos', 'horarios.grupo_id', '=', 'grupos.id')
                ->join('aulas', 'horarios.aula_id', '=', 'aulas.id')
                ->select(
                    'horarios.dia',
                    'horarios.hora_inicio',
                    'horarios.hora_fin',
                    'materias.nombre as materia_nombre',
                    'grupos.nombre as grupo_nombre',
                    'grupos.turno',
                    'aulas.nombre as aula_nombre',
                    'aulas.edificio as aula_codigo'
                )
                ->where('horarios.docente_id', $docente->id)
                ->orderByRaw("CASE horarios.dia
                    WHEN 'lunes'     THEN 1
                    WHEN 'martes'    THEN 2
                    WHEN 'miercoles' THEN 3
                    WHEN 'jueves'    THEN 4
                    WHEN 'viernes'   THEN 5
                    WHEN 'sabado'    THEN 6
                    ELSE 7 END")
                ->orderBy('horarios.hora_inicio')
                ->get();
        }

        return view('docente.horario', compact('docente', 'gestion', 'horario'));
    }

    public function notas()
    {
        $docente = $this->getDocente();
        $gestion = $this->getGestion();

        if (!$docente || !$gestion) {
            return view('docente.notas', compact('docente', 'gestion') + [
                'grupos' => collect(), 'materias' => collect(), 'asignaciones' => collect(),
            ]);
        }

        $asignaciones = DB::table('asignacion_docentes')
            ->join('grupos', 'asignacion_docentes.grupo_id', '=', 'grupos.id')
            ->join('materias', 'asignacion_docentes.materia_id', '=', 'materias.id')
            ->select(
                'asignacion_docentes.grupo_id',
                'asignacion_docentes.materia_id',
                'grupos.nombre as grupo_nombre',
                'grupos.turno',
                'materias.nombre as materia_nombre'
            )
            ->where('asignacion_docentes.docente_id', $docente->id)
            ->where('asignacion_docentes.gestion_id', $gestion->id)
            ->get();

        $grupos = $asignaciones->unique('grupo_id')->map(fn($a) => (object)[
            'id'    => $a->grupo_id,
            'nombre' => $a->grupo_nombre,
            'turno'  => $a->turno,
        ])->values();

        $materias = $asignaciones->unique('materia_id')->map(fn($a) => (object)[
            'id'    => $a->materia_id,
            'nombre' => $a->materia_nombre,
        ])->values();

        return view('docente.notas', compact('docente', 'gestion', 'grupos', 'materias', 'asignaciones'));
    }

    public function getAlumnos(Request $request)
    {
        $docente    = $this->getDocente();
        $gestion    = $this->getGestion();
        $grupo_id   = $request->grupo_id;
        $materia_id = $request->materia_id;

        $autorizado = DB::table('asignacion_docentes')
            ->where('docente_id', $docente->id)
            ->where('grupo_id', $grupo_id)
            ->where('materia_id', $materia_id)
            ->where('gestion_id', $gestion->id)
            ->exists();

        if (!$autorizado) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $alumnos = DB::table('asignacion_grupos')
            ->join('postulantes', 'asignacion_grupos.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->select('postulantes.id', 'users.name', 'postulantes.ci')
            ->where('asignacion_grupos.grupo_id', $grupo_id)
            ->orderBy('users.name')
            ->get();

        $examenes = DB::table('examenes')
            ->where('materia_id', $materia_id)
            ->where('gestion_id', $gestion->id)
            ->get();

        if ($examenes->isEmpty()) {
            foreach (['parcial1' => 30, 'parcial2' => 30, 'final' => 40] as $tipo => $max) {
                DB::table('examenes')->insert([
                    'materia_id'     => $materia_id,
                    'gestion_id'     => $gestion->id,
                    'tipo'           => $tipo,
                    'puntaje_maximo' => $max,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
            $examenes = DB::table('examenes')
                ->where('materia_id', $materia_id)
                ->where('gestion_id', $gestion->id)
                ->get();
        }

        $notas = DB::table('notas')
            ->whereIn('postulante_id', $alumnos->pluck('id'))
            ->whereIn('examen_id', $examenes->pluck('id'))
            ->get()
            ->groupBy('postulante_id');

        return response()->json(compact('alumnos', 'examenes', 'notas'));
    }

    public function storeNotas(Request $request)
    {
        $docente    = $this->getDocente();
        $gestion    = $this->getGestion();
        $materia_id = $request->materia_id;
        $grupo_id   = $request->grupo_id;

        $autorizado = DB::table('asignacion_docentes')
            ->where('docente_id', $docente->id)
            ->where('grupo_id', $grupo_id)
            ->where('materia_id', $materia_id)
            ->where('gestion_id', $gestion->id)
            ->exists();

        if (!$autorizado) abort(403, 'No autorizado');

        foreach (($request->notas ?? []) as $postulante_id => $examenes) {
            foreach ($examenes as $examen_id => $puntaje) {
                if ($puntaje === null || $puntaje === '') continue;

                $max     = DB::table('examenes')->where('id', $examen_id)->value('puntaje_maximo');
                $puntaje = min(max((float) $puntaje, 0), $max);

                $existe = DB::table('notas')
                    ->where('postulante_id', $postulante_id)
                    ->where('examen_id', $examen_id)
                    ->exists();

                if ($existe) {
                    DB::table('notas')
                        ->where('postulante_id', $postulante_id)
                        ->where('examen_id', $examen_id)
                        ->update(['puntaje' => $puntaje, 'updated_at' => now()]);
                } else {
                    DB::table('notas')->insert([
                        'postulante_id' => $postulante_id,
                        'examen_id'     => $examen_id,
                        'puntaje'       => $puntaje,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
            $this->calcularResultado($postulante_id, $materia_id, $gestion->id, $gestion);
        }

        return back()->with('success', 'Notas guardadas correctamente.');
    }

    private function calcularResultado(int $postulante_id, int $materia_id, int $gestion_id, object $gestion): void
    {
        $examenes = DB::table('examenes')
            ->where('materia_id', $materia_id)
            ->where('gestion_id', $gestion_id)
            ->pluck('id');

        $notas = DB::table('notas')
            ->whereIn('examen_id', $examenes)
            ->where('postulante_id', $postulante_id)
            ->pluck('puntaje');

        if ($notas->isEmpty()) return;

        $total    = $notas->sum();
        $aprobado = $total >= 60;

        $existe = DB::table('resultado_materias')
            ->where('postulante_id', $postulante_id)
            ->where('materia_id', $materia_id)
            ->exists();

        if ($existe) {
            DB::table('resultado_materias')
                ->where('postulante_id', $postulante_id)
                ->where('materia_id', $materia_id)
                ->update(['total' => $total, 'aprobado' => $aprobado, 'updated_at' => now()]);
        } else {
            DB::table('resultado_materias')->insert([
                'postulante_id' => $postulante_id,
                'materia_id'    => $materia_id,
                'total'         => $total,
                'aprobado'      => $aprobado,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    /**
     * Muestra la vista del registro de asistencia.
     */
    public function asistencia()
    {
        $docente = $this->getDocente();
        $gestion = $this->getGestion();

        if (!$docente || !$gestion) {
            return view('docente.asistencia', compact('docente', 'gestion') + [
                'grupos' => collect(), 'materias' => collect(), 'asignaciones' => collect(),
            ]);
        }

        $asignaciones = DB::table('asignacion_docentes')
            ->join('grupos', 'asignacion_docentes.grupo_id', '=', 'grupos.id')
            ->join('materias', 'asignacion_docentes.materia_id', '=', 'materias.id')
            ->select(
                'asignacion_docentes.grupo_id',
                'asignacion_docentes.materia_id',
                'grupos.nombre as grupo_nombre',
                'grupos.turno',
                'materias.nombre as materia_nombre'
            )
            ->where('asignacion_docentes.docente_id', $docente->id)
            ->where('asignacion_docentes.gestion_id', $gestion->id)
            ->get();

        $grupos = $asignaciones->unique('grupo_id')->map(fn($a) => (object)[
            'id'    => $a->grupo_id,
            'nombre' => $a->grupo_nombre,
            'turno'  => $a->turno,
        ])->values();

        $materias = $asignaciones->unique('materia_id')->map(fn($a) => (object)[
            'id'    => $a->materia_id,
            'nombre' => $a->materia_nombre,
        ])->values();

        return view('docente.asistencia', compact('docente', 'gestion', 'grupos', 'materias', 'asignaciones'));
    }

    /**
     * Obtiene la lista de alumnos de un grupo y su asistencia para una fecha determinada (AJAX).
     */
    public function getAsistenciaAlumnos(Request $request)
    {
        $docente    = $this->getDocente();
        $gestion    = $this->getGestion();
        $grupo_id   = $request->grupo_id;
        $materia_id = $request->materia_id;
        $fecha      = $request->fecha ?? now()->format('Y-m-d');

        $autorizado = DB::table('asignacion_docentes')
            ->where('docente_id', $docente->id)
            ->where('grupo_id', $grupo_id)
            ->where('materia_id', $materia_id)
            ->where('gestion_id', $gestion->id)
            ->exists();

        if (!$autorizado) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $alumnos = DB::table('asignacion_grupos')
            ->join('postulantes', 'asignacion_grupos.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->select('postulantes.id', 'users.name', 'postulantes.ci')
            ->where('asignacion_grupos.grupo_id', $grupo_id)
            ->orderBy('users.name')
            ->get();

        // Buscar si ya existe la asistencia para esta fecha
        $asistencia = DB::table('asistencias')
            ->where('grupo_id', $grupo_id)
            ->where('materia_id', $materia_id)
            ->where('fecha', $fecha)
            ->first();

        $asistenciasMarcadas = collect();
        if ($asistencia) {
            $asistenciasMarcadas = DB::table('asistencia_postulante')
                ->where('asistencia_id', $asistencia->id)
                ->get()
                ->keyBy('postulante_id');
        }

        return response()->json(compact('alumnos', 'asistencia', 'asistenciasMarcadas'));
    }

    /**
     * Guarda la asistencia del grupo y materia seleccionados para una fecha.
     */
    public function storeAsistencia(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'grupo_id'   => 'required|exists:grupos,id',
            'fecha'      => 'required|date|before_or_equal:today',
            'asistencia' => 'required|array',
        ]);

        $docente    = $this->getDocente();
        $gestion    = $this->getGestion();
        $materia_id = $request->materia_id;
        $grupo_id   = $request->grupo_id;
        $fecha      = $request->fecha;

        $autorizado = DB::table('asignacion_docentes')
            ->where('docente_id', $docente->id)
            ->where('grupo_id', $grupo_id)
            ->where('materia_id', $materia_id)
            ->where('gestion_id', $gestion->id)
            ->exists();

        if (!$autorizado) abort(403, 'No autorizado');

        DB::beginTransaction();
        try {
            // Obtener o crear cabecera de asistencia
            $asistenciaId = DB::table('asistencias')
                ->where('grupo_id', $grupo_id)
                ->where('materia_id', $materia_id)
                ->where('fecha', $fecha)
                ->value('id');

            if ($asistenciaId) {
                DB::table('asistencias')->where('id', $asistenciaId)->update([
                    'updated_at' => now(),
                ]);
            } else {
                $asistenciaId = DB::table('asistencias')->insertGetId([
                    'docente_id' => $docente->id,
                    'grupo_id'   => $grupo_id,
                    'materia_id' => $materia_id,
                    'fecha'      => $fecha,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Guardar detalles
            foreach ($request->asistencia as $postulante_id => $estado) {
                if (!in_array($estado, ['presente', 'falta', 'licencia'])) continue;

                $existeDetalle = DB::table('asistencia_postulante')
                    ->where('asistencia_id', $asistenciaId)
                    ->where('postulante_id', $postulante_id)
                    ->exists();

                if ($existeDetalle) {
                    DB::table('asistencia_postulante')
                        ->where('asistencia_id', $asistenciaId)
                        ->where('postulante_id', $postulante_id)
                        ->update([
                            'estado'     => $estado,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('asistencia_postulante')->insert([
                        'asistencia_id' => $asistenciaId,
                        'postulante_id' => $postulante_id,
                        'estado'        => $estado,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Asistencia guardada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al guardar la asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la sábana o matriz de historial de asistencias de un grupo y materia.
     */
    public function asistenciaHistorial(Request $request)
    {
        $docente    = $this->getDocente();
        $gestion    = $this->getGestion();
        $grupo_id   = $request->grupo_id;
        $materia_id = $request->materia_id;

        if (!$docente || !$gestion || !$grupo_id || !$materia_id) {
            return redirect()->route('docente.asistencia');
        }

        $grupo = DB::table('grupos')->find($grupo_id);
        $materia = DB::table('materias')->find($materia_id);

        $alumnos = DB::table('asignacion_grupos')
            ->join('postulantes', 'asignacion_grupos.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->select('postulantes.id', 'users.name', 'postulantes.ci')
            ->where('asignacion_grupos.grupo_id', $grupo_id)
            ->orderBy('users.name')
            ->get();

        $asistencias = DB::table('asistencias')
            ->where('grupo_id', $grupo_id)
            ->where('materia_id', $materia_id)
            ->orderBy('fecha', 'asc')
            ->get();

        // Armar matriz de asistencia: alumnos x fechas
        $matriz = [];
        foreach ($alumnos as $alumno) {
            $registro = [
                'ci'     => $alumno->ci,
                'nombre' => $alumno->name,
                'fechas' => [],
                'totales' => ['presente' => 0, 'falta' => 0, 'licencia' => 0]
            ];

            foreach ($asistencias as $asist) {
                $estado = DB::table('asistencia_postulante')
                    ->where('asistencia_id', $asist->id)
                    ->where('postulante_id', $alumno->id)
                    ->value('estado');

                $registro['fechas'][$asist->id] = $estado ?? '—';
                if ($estado) {
                    $registro['totales'][$estado]++;
                }
            }
            $matriz[] = (object) $registro;
        }

        return view('docente.asistencia_historial', compact('docente', 'gestion', 'grupo', 'materia', 'asistencias', 'matriz'));
    }
}
