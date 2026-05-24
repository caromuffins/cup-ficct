<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materias')->insert([
            ['nombre' => 'Matematicas', 'codigo' => 'MAT', 'descripcion' => 'Matematicas basicas y avanzadas', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Fisica', 'codigo' => 'FIS', 'descripcion' => 'Fisica general', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Computacion', 'codigo' => 'COM', 'descripcion' => 'Introduccion a la computacion', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingles', 'codigo' => 'ING', 'descripcion' => 'Ingles basico', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}