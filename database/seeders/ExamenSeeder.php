<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamenSeeder extends Seeder
{
    public function run(): void
    {
        $gestiones = [1, 2, 3, 4];
        $materias  = [1, 2, 3, 4];
        $tipos = [
            ['nombre' => 'Primer Parcial',  'tipo' => 'parcial1', 'puntaje_maximo' => 30],
            ['nombre' => 'Segundo Parcial', 'tipo' => 'parcial2', 'puntaje_maximo' => 30],
            ['nombre' => 'Examen Final',    'tipo' => 'final',    'puntaje_maximo' => 40],
        ];

        foreach ($gestiones as $gestionId) {
            foreach ($materias as $materiaId) {
                foreach ($tipos as $tipo) {
                    DB::table('examenes')->insert([
                        'materia_id'     => $materiaId,
                        'gestion_id'     => $gestionId,
                        'nombre'         => $tipo['nombre'],
                        'tipo'           => $tipo['tipo'],
                        'puntaje_maximo' => $tipo['puntaje_maximo'],
                        'fecha'          => now()->format('Y-m-d'),
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }
        }
    }
}