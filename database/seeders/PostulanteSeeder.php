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
            'Gustavo','Veronica','Alfredo','Beatriz','Marcos','Lorena',
            'Emilio','Sandra','Adrian','Fabiola','Nicolas','Mariana',
            'Rodrigo','Camila','Felipe','Vanessa','Mauricio','Cristina',
            'Javier','Melissa','Alejandro','Stefania','Sebastian','Diana',
            'Christian','Pamela','Jonathan','Jessica','Daniel','Carla',
        ];
        $apellidos = [
            'Mamani','Quispe','Flores','Vaca','Suarez','Lopez','Torrez','Mendoza',
            'Arce','Pinto','Chavez','Vargas','Morales','Perez','Gutierrez','Rojas',
            'Soria','Camacho','Aguilar','Herrera','Salinas','Miranda','Ibañez',
            'Orellana','Balcazar','Montero','Sandoval','Veizaga','Terceros','Zabala',
            'Cuellar','Nogales','Velarde','Claros','Barrios','Alcazar','Duran',
            'Viscarra','Antelo','Hurtado','Rios','Molina','Cabrera','Paredes',
            'Quiroga','Fernandez','Medina','Castro','Ramos','Jimenez',
        ];
        $colegios = [
            'Colegio Nacional Florida','Unidad Educativa El Cristo',
            'Colegio Aleman','Unidad Educativa Domingo Savio',
            'Colegio San Ignacio','Unidad Educativa 24 de Septiembre',
            'Colegio Don Bosco','Instituto Tecnologico Bolivia',
            'Unidad Educativa Franz Tamayo','Colegio Santa Ana',
            'Colegio Marista','Unidad Educativa Santa Cruz de la Sierra',
            'Colegio Tecnico Humanistico','Instituto Nacional Bolivia',
            'Unidad Educativa Los Pinos','Colegio Metodista',
        ];
        $ciudades = ['Santa Cruz','Cochabamba','La Paz','Sucre','Tarija','Trinidad','Oruro','Potosi','Beni'];
        $calles   = [
            'Av. Cristo Redentor','Av. Banzer','Av. Busch','Calle Murillo',
            'Av. Las Americas','Calle Potosi','Av. Cañoto','Calle Independencia',
            'Av. Monseñor Rivero','Calle Ballivian','Av. Tres Pasos al Frente',
            'Calle Oruro','Av. Uruguay','Calle Junin','Av. Brasil',
            'Av. Pirai','Calle Libertad','Av. Virgen de Cotoca',
        ];
        $turnos = ['maniana', 'tarde'];
        $sexos  = ['M', 'F'];

        $gestionSufijos = ['2024A', '2024B', '2025A', '2026A'];
        $counter = 9;

        foreach ($gestionSufijos as $sufijo) {
            for ($i = 1; $i <= 250; $i++) {
                $counter++;
                $nombre    = $nombres[array_rand($nombres)];
                $apellido  = $apellidos[array_rand($apellidos)];
                $apellido2 = rand(0, 1) ? ' ' . $apellidos[array_rand($apellidos)] : '';
                $ci        = 10000000 + $counter;
                $sexo      = $sexos[array_rand($sexos)];
                $direccion = $calles[array_rand($calles)] . ' #' . rand(100, 9999);

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
                    'ci'               => (string) $ci,
                    'sexo'             => $sexo,
                    'fecha_nacimiento' => now()->subYears(rand(17, 22))->subDays(rand(0, 364))->format('Y-m-d'),
                    'telefono'         => '7' . rand(1000000, 9999999),
                    'ciudad'           => $ciudades[array_rand($ciudades)],
                    'direccion'        => $direccion,
                    'colegio'          => $colegios[array_rand($colegios)],
                    'estado'           => 'inscrito',
                    'turno_preferido'  => $turnos[array_rand($turnos)],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }
}
