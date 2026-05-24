<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $turnos = ['maniana', 'tarde'];
        $rangos = [1 => [1,30], 2 => [31,60], 3 => [61,90], 4 => [91,120]];

        foreach ($rangos as $gestionId => [$inicio, $fin]) {
            $postulantes = DB::table('postulantes')->whereBetween('id', [$inicio, $fin])->get();
            $totalGrupos = ceil($postulantes->count() / 15);

            for ($g = 1; $g <= $totalGrupos; $g++) {
                DB::table('grupos')->insert([
                    'gestion_id'  => $gestionId,
                    'nombre'      => "Grupo $g",
                    'turno'       => $turnos[$g % 2],
                    'cupo_maximo' => 70,
                    'cupo_actual' => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                $grupoId = DB::getPdo()->lastInsertId();
                $chunk   = $postulantes->slice(($g - 1) * 15, 15);

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

                $materiaDocente = [1 => 1, 2 => 2, 3 => 3, 4 => 4];
                foreach ($materiaDocente as $materiaId => $docenteId) {
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