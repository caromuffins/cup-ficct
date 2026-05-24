<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carreras')->insert([
            ['nombre' => 'Ingeniería Informática', 'codigo' => 'INF', 'descripcion' => 'Carrera de Ingeniería Informática', 'cupo_maximo' => 80, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería de Sistemas', 'codigo' => 'SIS', 'descripcion' => 'Carrera de Ingeniería de Sistemas', 'cupo_maximo' => 80, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería en Redes y Telecomunicaciones', 'codigo' => 'RYT', 'descripcion' => 'Carrera de Ingeniería en Redes y Telecomunicaciones', 'cupo_maximo' => 80, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería Robótica', 'codigo' => 'ROB', 'descripcion' => 'Carrera de Ingeniería Robótica', 'cupo_maximo' => 80, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}