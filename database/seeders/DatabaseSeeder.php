<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CarreraSeeder::class,
            MateriaSeeder::class,
            GestionSeeder::class,
            AulaSeeder::class,
            UserSeeder::class,
            DocenteSeeder::class,
            PostulanteSeeder::class,
            InscripcionSeeder::class,
            GrupoSeeder::class,
            ExamenSeeder::class,
            NotaSeeder::class,
        ]);
    }
}