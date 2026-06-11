<?php

namespace App\Http\Controllers\Postulante;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ResultadosController extends Controller
{
    public function notas()
    {
        $postulante = DB::table('postulantes')->where('user_id', auth()->id())->first();
        $gestion    = DB::table('gestiones')->where('activa', true)->first();

        if (!$postulante || !$gestion) {
            return view('postulante.notas', [
                'postulante' => $postulante,
                'gestion'    => $gestion,
                'resultados' => collect(),
            ]);
        }

        $materias = DB::table('materias')->orderBy('nombre')->get();

        $resultados = $materias->map(function ($materia) use ($postulante, $gestion) {
            $examenes = DB::table('examenes')
                ->where('materia_id', $materia->id)
                ->where('gestion_id', $gestion->id)
                ->orderByRaw("CASE tipo WHEN 'parcial1' THEN 1 WHEN 'parcial2' THEN 2 WHEN 'final' THEN 3 ELSE 4 END")
                ->get();

            if ($examenes->isEmpty()) return null;

            $notas = DB::table('notas')
                ->whereIn('examen_id', $examenes->pluck('id'))
                ->where('postulante_id', $postulante->id)
                ->get()
                ->keyBy('examen_id');

            $resultado = DB::table('resultado_materias')
                ->where('postulante_id', $postulante->id)
                ->where('materia_id', $materia->id)
                ->first();

            return (object) [
                'materia'   => $materia,
                'examenes'  => $examenes,
                'notas'     => $notas,
                'resultado' => $resultado,
            ];
        })->filter()->values();

        return view('postulante.notas', compact('postulante', 'gestion', 'resultados'));
    }

    public function admision()
    {
        $postulante = DB::table('postulantes')->where('user_id', auth()->id())->first();
        $gestion    = DB::table('gestiones')->where('activa', true)->first();

        $admision  = null;
        $publicada = $gestion && $gestion->publicada;

        if ($postulante) {
            $admision = DB::table('admisiones')
                ->leftJoin('carreras', 'admisiones.carrera_asignada_id', '=', 'carreras.id')
                ->select(
                    'admisiones.*',
                    'carreras.nombre as carrera_nombre',
                    'carreras.codigo as carrera_codigo'
                )
                ->where('admisiones.postulante_id', $postulante->id)
                ->first();
        }

        // Materias aprobadas para mostrar resumen
        $materiasAprobadas = $postulante
            ? DB::table('resultado_materias')
                ->join('materias', 'resultado_materias.materia_id', '=', 'materias.id')
                ->select('materias.nombre', 'resultado_materias.total', 'resultado_materias.aprobado')
                ->where('resultado_materias.postulante_id', $postulante->id)
                ->orderBy('materias.nombre')
                ->get()
            : collect();

        return view('postulante.admision', compact('postulante', 'gestion', 'admision', 'publicada', 'materiasAprobadas'));
    }
}
