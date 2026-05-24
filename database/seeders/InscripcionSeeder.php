<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        $rangos = [1 => [1,30], 2 => [31,60], 3 => [61,90], 4 => [91,120]];
        $montos = [1 => 150, 2 => 150, 3 => 200, 4 => 200];

        foreach ($rangos as $gestionId => [$inicio, $fin]) {
            $postulantes = DB::table('postulantes')->whereBetween('id', [$inicio, $fin])->get();

            foreach ($postulantes as $postulante) {
                DB::table('inscripciones')->insert([
                    'postulante_id'      => $postulante->id,
                    'gestion_id'         => $gestionId,
                    'carrera_primera_id' => rand(1, 4),
                    'carrera_segunda_id' => rand(1, 4),
                    'estado'             => 'pagada',
                    'fecha_inscripcion'  => now(),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                $inscripcionId = DB::getPdo()->lastInsertId();

                DB::table('pagos')->insert([
                    'inscripcion_id' => $inscripcionId,
                    'monto'          => $montos[$gestionId],
                    'moneda'         => 'USD',
                    'metodo'         => 'paypal',
                    'estado'         => 'completado',
                    'transaccion_id' => 'TXN-' . strtoupper(uniqid()),
                    'fecha_pago'     => now(),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }
}