<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('gestiones')->insert([
            ['anio' => 2024, 'periodo' => 'primero', 'fecha_inicio' => '2024-01-15', 'fecha_fin' => '2024-02-15', 'activa' => false, 'cupo_por_carrera' => 80, 'monto_inscripcion' => 150.00, 'created_at' => now(), 'updated_at' => now()],
            ['anio' => 2024, 'periodo' => 'segundo', 'fecha_inicio' => '2024-07-15', 'fecha_fin' => '2024-08-15', 'activa' => false, 'cupo_por_carrera' => 80, 'monto_inscripcion' => 150.00, 'created_at' => now(), 'updated_at' => now()],
            ['anio' => 2025, 'periodo' => 'primero', 'fecha_inicio' => '2025-01-15', 'fecha_fin' => '2025-02-15', 'activa' => false, 'cupo_por_carrera' => 80, 'monto_inscripcion' => 200.00, 'created_at' => now(), 'updated_at' => now()],
            ['anio' => 2026, 'periodo' => 'primero', 'fecha_inicio' => '2026-01-15', 'fecha_fin' => '2026-02-15', 'activa' => true, 'cupo_por_carrera' => 80, 'monto_inscripcion' => 200.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}