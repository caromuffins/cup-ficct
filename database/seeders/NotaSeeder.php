<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotaSeeder extends Seeder
{
    public function run(): void
    {
        $gestiones = [1, 2, 3, 4];
        $materias  = [1, 2, 3, 4];

        foreach ($gestiones as $gestionId) {
            $asignaciones = DB::table('asignacion_grupos')->where('gestion_id', $gestionId)->get();

            foreach ($asignaciones as $asignacion) {
                $promedios = [];

                foreach ($materias as $materiaId) {
                    $examenes = DB::table('examenes')
                        ->where('gestion_id', $gestionId)
                        ->where('materia_id', $materiaId)
                        ->get();

                    $parcial1 = $parcial2 = $final = 0;

                    foreach ($examenes as $examen) {
                        $puntaje = match($examen->tipo) {
                            'parcial1' => rand(15, 30),
                            'parcial2' => rand(15, 30),
                            'final'    => rand(20, 40),
                            default    => 0,
                        };

                        DB::table('notas')->insert([
                            'postulante_id'  => $asignacion->postulante_id,
                            'examen_id'      => $examen->id,
                            'grupo_id'       => $asignacion->grupo_id,
                            'puntaje'        => $puntaje,
                            'fecha_registro' => now(),
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);

                        if ($examen->tipo === 'parcial1') $parcial1 = $puntaje;
                        if ($examen->tipo === 'parcial2') $parcial2 = $puntaje;
                        if ($examen->tipo === 'final')    $final    = $puntaje;
                    }

                    $total    = $parcial1 + $parcial2 + $final;
                    $aprobado = $total >= 60 ? 1 : 0;

                    DB::table('resultado_materias')->insert([
                        'postulante_id'  => $asignacion->postulante_id,
                        'materia_id'     => $materiaId,
                        'gestion_id'     => $gestionId,
                        'total_parcial1' => $parcial1,
                        'total_parcial2' => $parcial2,
                        'total_final'    => $final,
                        'total'          => $total,
                        'aprobado'       => $aprobado,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);

                    $promedios[] = $total;
                }

                $todasAprobadas = DB::table('resultado_materias')
                    ->where('postulante_id', $asignacion->postulante_id)
                    ->where('gestion_id', $gestionId)
                    ->where('aprobado', false)
                    ->count() === 0;

                $promedio = count($promedios) > 0 ? array_sum($promedios) / count($promedios) : 0;

                $inscripcion = DB::table('inscripciones')
                    ->where('postulante_id', $asignacion->postulante_id)
                    ->where('gestion_id', $gestionId)
                    ->first();

                DB::table('admisiones')->insert([
                    'postulante_id'       => $asignacion->postulante_id,
                    'gestion_id'          => $gestionId,
                    'carrera_asignada_id' => $inscripcion ? $inscripcion->carrera_primera_id : 1,
                    'promedio_general'    => round($promedio, 2),
                    'admitido'            => $todasAprobadas ? 1 : 0,
                    'opcion_asignada'     => $todasAprobadas ? 'primera' : null,
                    'fecha_publicacion'   => now(),
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }
}