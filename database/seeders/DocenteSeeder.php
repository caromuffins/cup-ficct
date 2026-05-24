<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocenteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('docentes')->insert([
            ['user_id' => 2, 'ci' => '1234567', 'telefono' => '70011001', 'especialidad' => 'Matematicas', 'max_grupos' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 3, 'ci' => '2345678', 'telefono' => '70022002', 'especialidad' => 'Fisica', 'max_grupos' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 4, 'ci' => '3456789', 'telefono' => '70033003', 'especialidad' => 'Computacion', 'max_grupos' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 5, 'ci' => '4567890', 'telefono' => '70044004', 'especialidad' => 'Ingles', 'max_grupos' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 6, 'ci' => '5678901', 'telefono' => '70055005', 'especialidad' => 'Matematicas', 'max_grupos' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 7, 'ci' => '6789012', 'telefono' => '70066006', 'especialidad' => 'Fisica', 'max_grupos' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 8, 'ci' => '7890123', 'telefono' => '70077007', 'especialidad' => 'Computacion', 'max_grupos' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 9, 'ci' => '8901234', 'telefono' => '70088008', 'especialidad' => 'Ingles', 'max_grupos' => 4, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}