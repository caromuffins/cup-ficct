<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AulaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('aulas')->insert([
            ['nombre' => 'Aula 101', 'edificio' => 'Edificio A', 'capacidad' => 70, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Aula 102', 'edificio' => 'Edificio A', 'capacidad' => 70, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Aula 103', 'edificio' => 'Edificio A', 'capacidad' => 70, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Aula 201', 'edificio' => 'Edificio B', 'capacidad' => 70, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Aula 202', 'edificio' => 'Edificio B', 'capacidad' => 70, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Aula 203', 'edificio' => 'Edificio B', 'capacidad' => 70, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Aula 301', 'edificio' => 'Edificio C', 'capacidad' => 70, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Aula 302', 'edificio' => 'Edificio C', 'capacidad' => 70, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Lab. Computacion 1', 'edificio' => 'Edificio D', 'capacidad' => 40, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Lab. Computacion 2', 'edificio' => 'Edificio D', 'capacidad' => 40, 'disponible' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}