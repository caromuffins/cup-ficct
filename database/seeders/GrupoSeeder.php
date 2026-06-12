<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $turnos = ['maniana', 'tarde'];

        // 250 postulantes por gestión → rangos de IDs secuenciales
        $rangos = [
            1 => [1,    250],
            2 => [251,  500],
            3 => [501,  750],
            4 => [751, 1000],
        ];

        // ceil(250/70) = 4 grupos por gestión → 16 grupos en total
        foreach ($rangos as $gestionId => [$inicio, $fin]) {
            $postulantes = DB::table('postulantes')->whereBetween('id', [$inicio, $fin])->get();
            $totalGrupos = (int) ceil($postulantes->count() / 70);

            $docentes = DB::table('docentes')->pluck('id')->values();

            for ($g = 1; $g <= $totalGrupos; $g++) {
                DB::table('grupos')->insert([
                    'gestion_id'  => $gestionId,
                    'nombre'      => "Grupo $g",
                    'turno'       => $turnos[($g - 1) % 2],
                    'cupo_maximo' => 70,
                    'cupo_actual' => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                $grupoId = DB::getPdo()->lastInsertId();
                $chunk   = $postulantes->slice(($g - 1) * 70, 70);

                foreach ($chunk as $postulante) {
                    DB::table('asignacion_grupos')->insert([
                        'postulante_id'    => $postulante->id,
                        'grupo_id'         => $grupoId,
                        'gestion_id'       => $gestionId,
                        'fecha_asignacion' => now(),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                    DB::table('grupos')->where('id', $grupoId)->increment('cupo_actual');
                }

                // Asignar docentes a materias (rotando si hay menos de 4)
                $materiaIds = [1, 2, 3, 4];
                foreach ($materiaIds as $idx => $materiaId) {
                    $docenteId = $docentes[$idx % $docentes->count()];
                    DB::table('asignacion_docentes')->insert([
                        'docente_id'  => $docenteId,
                        'grupo_id'    => $grupoId,
                        'materia_id'  => $materiaId,
                        'gestion_id'  => $gestionId,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }
    }
}
