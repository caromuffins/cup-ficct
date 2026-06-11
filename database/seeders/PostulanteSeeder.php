<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PostulanteSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Diego','Lucia','Miguel','Sofia','Andres','Valeria','Carlos','Maria',
            'Juan','Ana','Pedro','Rosa','Luis','Elena','Jorge','Carmen',
            'Roberto','Patricia','Fernando','Gabriela','Ricardo','Natalia',
            'Sergio','Monica','Eduardo','Alejandra','Hector','Daniela',
            'Oscar','Claudia','Raul','Paola','Victor','Isabel','Cesar','Jimena',
            'Gustavo','Verónica','Alfredo','Beatriz','Marcos','Lorena',
            'Emilio','Sandra','Adrian','Fabiola','Nicolas','Mariana',
        ];
        $apellidos = [
            'Mamani','Quispe','Flores','Vaca','Suarez','Lopez','Torrez','Mendoza',
            'Arce','Pinto','Chavez','Vargas','Morales','Perez','Gutierrez','Rojas',
            'Soria','Camacho','Aguilar','Herrera','Salinas','Miranda','Ibañez',
            'Orellana','Balcazar','Montero','Sandoval','Veizaga','Terceros','Zabala',
            'Cuellar','Nogales','Velarde','Claros','Barrios','Alcazar','Durán',
        ];
        $colegios = [
            'Colegio Nacional Florida','Unidad Educativa El Cristo',
            'Colegio Aleman','Unidad Educativa Domingo Savio',
            'Colegio San Ignacio','Unidad Educativa 24 de Septiembre',
            'Colegio Don Bosco','Instituto Tecnológico Bolivia',
            'Unidad Educativa Franz Tamayo','Colegio Santa Ana',
        ];
        $ciudades  = ['Santa Cruz','Cochabamba','La Paz','Sucre','Tarija','Trinidad','Oruro'];
        $turnos    = ['maniana', 'tarde'];

        $gestionSufijos = ['2024A', '2024B', '2025A', '2026A'];
        $counter = 9;

        foreach ($gestionSufijos as $sufijo) {
            for ($i = 1; $i <= 125; $i++) {
                $counter++;
                $nombre   = $nombres[array_rand($nombres)];
                $apellido = $apellidos[array_rand($apellidos)];
                $apellido2 = rand(0, 1) ? ' ' . $apellidos[array_rand($apellidos)] : '';
                $ci       = 10000000 + $counter;

                DB::table('users')->insert([
                    'name'       => "$nombre $apellido$apellido2",
                    'email'      => "postulante{$counter}@gmail.com",
                    'password'   => Hash::make('postulante1234'),
                    'role'       => 'postulante',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $userId = DB::getPdo()->lastInsertId();

                DB::table('postulantes')->insert([
                    'user_id'          => $userId,
                    'ci'               => (string)$ci,
                    'fecha_nacimiento' => now()->subYears(rand(17, 22))->format('Y-m-d'),
                    'telefono'         => '7' . rand(1000000, 9999999),
                    'colegio'          => $colegios[array_rand($colegios)],
                    'ciudad'           => $ciudades[array_rand($ciudades)],
                    'estado'           => 'inscrito',
                    'turno_preferido'  => $turnos[array_rand($turnos)],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }
}
