<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        // 250 postulantes por gestión → rangos de IDs secuenciales
        $rangos = [
            1 => [1,    250],
            2 => [251,  500],
            3 => [501,  750],
            4 => [751, 1000],
        ];
        $montos = [1 => 150, 2 => 150, 3 => 200, 4 => 200];

        foreach ($rangos as $gestionId => [$inicio, $fin]) {
            $postulantes = DB::table('postulantes')->whereBetween('id', [$inicio, $fin])->get();

            foreach ($postulantes as $postulante) {
                $carrera1 = rand(1, 4);
                // segunda opción distinta a la primera
                do { $carrera2 = rand(1, 4); } while ($carrera2 === $carrera1);

                DB::table('inscripciones')->insert([
                    'postulante_id'      => $postulante->id,
                    'gestion_id'         => $gestionId,
                    'carrera_primera_id' => $carrera1,
                    'carrera_segunda_id' => $carrera2,
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
