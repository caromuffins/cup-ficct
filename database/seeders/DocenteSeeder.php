<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocenteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('docentes')->insert([
            [
                'user_id' => 2, 'ci' => '1234567', 'telefono' => '70011001',
                'especialidad' => 'Matematicas', 'titulo_profesional' => 'Licenciado en Matemáticas',
                'tiene_maestria' => true, 'area_maestria' => 'Matemática Aplicada',
                'tiene_diplomado' => false, 'area_diplomado' => null,
                'estado_contratacion' => 'contratado', 'max_grupos' => 4, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => 3, 'ci' => '2345678', 'telefono' => '70022002',
                'especialidad' => 'Fisica', 'titulo_profesional' => 'Licenciado en Física',
                'tiene_maestria' => true, 'area_maestria' => 'Física Experimental',
                'tiene_diplomado' => false, 'area_diplomado' => null,
                'estado_contratacion' => 'contratado', 'max_grupos' => 4, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => 4, 'ci' => '3456789', 'telefono' => '70033003',
                'especialidad' => 'Computacion', 'titulo_profesional' => 'Ingeniero en Sistemas',
                'tiene_maestria' => false, 'area_maestria' => null,
                'tiene_diplomado' => true, 'area_diplomado' => 'Educación Superior en Tecnología',
                'estado_contratacion' => 'contratado', 'max_grupos' => 4, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => 5, 'ci' => '4567890', 'telefono' => '70044004',
                'especialidad' => 'Ingles', 'titulo_profesional' => 'Licenciado en Lingüística',
                'tiene_maestria' => false, 'area_maestria' => null,
                'tiene_diplomado' => true, 'area_diplomado' => 'Educación Superior en Idiomas',
                'estado_contratacion' => 'contratado', 'max_grupos' => 4, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => 6, 'ci' => '5678901', 'telefono' => '70055005',
                'especialidad' => 'Matematicas', 'titulo_profesional' => 'Licenciado en Matemáticas',
                'tiene_maestria' => true, 'area_maestria' => 'Álgebra y Análisis',
                'tiene_diplomado' => false, 'area_diplomado' => null,
                'estado_contratacion' => 'contratado', 'max_grupos' => 4, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => 7, 'ci' => '6789012', 'telefono' => '70066006',
                'especialidad' => 'Fisica', 'titulo_profesional' => 'Licenciado en Física',
                'tiene_maestria' => false, 'area_maestria' => null,
                'tiene_diplomado' => true, 'area_diplomado' => 'Educación Superior en Ciencias',
                'estado_contratacion' => 'contratado', 'max_grupos' => 4, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => 8, 'ci' => '7890123', 'telefono' => '70077007',
                'especialidad' => 'Computacion', 'titulo_profesional' => 'Ingeniero Informático',
                'tiene_maestria' => true, 'area_maestria' => 'Ciencias de la Computación',
                'tiene_diplomado' => false, 'area_diplomado' => null,
                'estado_contratacion' => 'contratado', 'max_grupos' => 4, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => 9, 'ci' => '8901234', 'telefono' => '70088008',
                'especialidad' => 'Ingles', 'titulo_profesional' => 'Licenciado en Idiomas',
                'tiene_maestria' => true, 'area_maestria' => 'Lingüística Aplicada',
                'tiene_diplomado' => false, 'area_diplomado' => null,
                'estado_contratacion' => 'contratado', 'max_grupos' => 4, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
