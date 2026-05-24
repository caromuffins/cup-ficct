<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            ['name' => 'Administrador FICCT', 'email' => 'admin@ficct.uagrm.edu.bo', 'password' => Hash::make('admin1234'), 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Carlos Mamani Flores', 'email' => 'cmamani@ficct.uagrm.edu.bo', 'password' => Hash::make('docente1234'), 'role' => 'docente', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maria Rodriguez Vaca', 'email' => 'mrodriguez@ficct.uagrm.edu.bo', 'password' => Hash::make('docente1234'), 'role' => 'docente', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Juan Perez Suarez', 'email' => 'jperez@ficct.uagrm.edu.bo', 'password' => Hash::make('docente1234'), 'role' => 'docente', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ana Gutierrez Lopez', 'email' => 'agutierrez@ficct.uagrm.edu.bo', 'password' => Hash::make('docente1234'), 'role' => 'docente', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Luis Vargas Mendoza', 'email' => 'lvargas@ficct.uagrm.edu.bo', 'password' => Hash::make('docente1234'), 'role' => 'docente', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rosa Chavez Torrez', 'email' => 'rchavez@ficct.uagrm.edu.bo', 'password' => Hash::make('docente1234'), 'role' => 'docente', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pedro Quispe Arce', 'email' => 'pquispe@ficct.uagrm.edu.bo', 'password' => Hash::make('docente1234'), 'role' => 'docente', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Elena Morales Pinto', 'email' => 'emorales@ficct.uagrm.edu.bo', 'password' => Hash::make('docente1234'), 'role' => 'docente', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}