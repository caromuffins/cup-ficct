<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmisionController extends Controller
{
    public function index()
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $resultados = DB::table('postulantes')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->join('asignacion_grupos', 'postulantes.id', '=', 'asignacion_grupos.postulante_id')
            ->join('grupos', 'asignacion_grupos.grupo_id', '=', 'grupos.id')
            ->leftJoin('admisiones', 'postulantes.id', '=', 'admisiones.postulante_id')
            ->leftJoin('carreras', 'admisiones.carrera_asignada_id', '=', 'carreras.id')
            ->select(
                'postulantes.id',
                'users.name',
                'postulantes.ci',
                'grupos.nombre as grupo',
                'admisiones.promedio_general',
                'admisiones.admitido',
                'admisiones.opcion_asignada',
                'carreras.nombre as carrera_asignada'
            )
            ->where('asignacion_grupos.gestion_id', $gestion->id)
            ->orderByDesc('admisiones.promedio_general')
            ->get();

        $totalMaterias = DB::table('materias')->count();

        $resultados = $resultados->map(function($r) use ($totalMaterias) {
            $materiasAprobadas = DB::table('resultado_materias')
                ->where('postulante_id', $r->id)
                ->where('aprobado', true)
                ->count();
            $r->materias_aprobadas = $materiasAprobadas;
            $r->total_materias     = $totalMaterias;
            $r->apto               = $materiasAprobadas >= $totalMaterias;
            return $r;
        });

        $carreras = DB::table('carreras')->get();

        return view('admin.admision.index', compact('resultados', 'gestion', 'carreras'));
    }

    public function calcular()
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $postulantes   = DB::table('asignacion_grupos')->where('gestion_id', $gestion->id)->pluck('postulante_id');
        $materias      = DB::table('materias')->pluck('id');
        $totalMaterias = $materias->count();

        foreach ($postulantes as $postulante_id) {
            foreach ($materias as $materia_id) {
                $examenes = DB::table('examenes')
                    ->where('materia_id', $materia_id)
                    ->where('gestion_id', $gestion->id)
                    ->pluck('id');

                $notas = DB::table('notas')
                    ->whereIn('examen_id', $examenes)
                    ->where('postulante_id', $postulante_id)
                    ->pluck('puntaje');

                if ($notas->isEmpty()) continue;

                $total    = $gestion->modo_evaluacion === 'promedio' ? $notas->avg() : $notas->sum();
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
        }

        return redirect()->route('admin.admision.index')
            ->with('success', 'Resultados calculados correctamente.');
    }

    public function asignarCarreras()
    {
        $gestion       = DB::table('gestiones')->where('activa', true)->first();
        $totalMaterias = DB::table('materias')->count();

        $aptos = DB::table('postulantes')
            ->join('asignacion_grupos', 'postulantes.id', '=', 'asignacion_grupos.postulante_id')
            ->join('inscripciones', function($join) use ($gestion) {
                $join->on('postulantes.id', '=', 'inscripciones.postulante_id')
                     ->where('inscripciones.gestion_id', $gestion->id);
            })
            ->select('postulantes.id', 'inscripciones.carrera_primera_id', 'inscripciones.carrera_segunda_id')
            ->where('asignacion_grupos.gestion_id', $gestion->id)
            ->get()
            ->filter(function($p) use ($totalMaterias) {
                $aprobadas = DB::table('resultado_materias')
                    ->where('postulante_id', $p->id)
                    ->where('aprobado', true)
                    ->count();
                return $aprobadas >= $totalMaterias;
            })
            ->map(function($p) {
                $p->promedio = DB::table('resultado_materias')
                    ->where('postulante_id', $p->id)
                    ->avg('total');
                return $p;
            })
            ->sortByDesc('promedio');

        foreach ($aptos as $postulante) {
            $cupoUsado = DB::table('admisiones')
                ->where('carrera_asignada_id', $postulante->carrera_primera_id)
                ->where('admitido', true)
                ->count();

            $cupoMax = DB::table('carreras')
                ->where('id', $postulante->carrera_primera_id)
                ->value('cupo_maximo');

            if ($cupoUsado < $cupoMax) {
                $carrera_id = $postulante->carrera_primera_id;
                $opcion     = 'primera';
            } else {
                $cupoUsado2 = DB::table('admisiones')
                    ->where('carrera_asignada_id', $postulante->carrera_segunda_id)
                    ->where('admitido', true)
                    ->count();

                $cupoMax2 = DB::table('carreras')
                    ->where('id', $postulante->carrera_segunda_id)
                    ->value('cupo_maximo');

                if ($cupoUsado2 < $cupoMax2) {
                    $carrera_id = $postulante->carrera_segunda_id;
                    $opcion     = 'segunda';
                } else {
                    $carrera_id = null;
                    $opcion     = null;
                }
            }

            $existe = DB::table('admisiones')->where('postulante_id', $postulante->id)->exists();

            if ($existe) {
                DB::table('admisiones')
                    ->where('postulante_id', $postulante->id)
                    ->update([
                        'carrera_asignada_id' => $carrera_id,
                        'promedio_general'    => round($postulante->promedio, 2),
                        'admitido'            => $carrera_id !== null,
                        'opcion_asignada'     => $opcion,
                        'updated_at'          => now(),
                    ]);
            } else {
                DB::table('admisiones')->insert([
                    'postulante_id'       => $postulante->id,
                    'carrera_asignada_id' => $carrera_id,
                    'promedio_general'    => round($postulante->promedio, 2),
                    'admitido'            => $carrera_id !== null,
                    'opcion_asignada'     => $opcion,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }

        return redirect()->route('admin.admision.index')
            ->with('success', 'Carreras asignadas correctamente.');
    }

    public function publicar()
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $admitidos = DB::table('admisiones')->where('admitido', true)->count();

        if ($admitidos === 0) {
            return redirect()->route('admin.admision.index')
                ->with('error', 'Primero debes calcular resultados y asignar carreras.');
        }

        DB::table('gestiones')->where('id', $gestion->id)->update([
            'publicada'  => true,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.admision.index')
            ->with('success', 'Lista de admitidos publicada correctamente.');
    }

    public function listaPublica()
    {
        $gestion = DB::table('gestiones')
            ->where('activa', true)
            ->first();

        $admitidos = DB::table('admisiones')
            ->join('postulantes', 'admisiones.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->join('carreras', 'admisiones.carrera_asignada_id', '=', 'carreras.id')
            ->select(
                'users.name',
                'postulantes.ci',
                'carreras.nombre as carrera',
                'admisiones.opcion_asignada',
                'admisiones.promedio_general'
            )
            ->where('admisiones.admitido', true)
            ->orderByDesc('admisiones.promedio_general')
            ->get();

        return view('admision.lista-publica', compact('admitidos', 'gestion'));
    }
}
