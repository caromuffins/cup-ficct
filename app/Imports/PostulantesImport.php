<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PostulantesImport
{
    public array $errores = [];
    public int $importados = 0;
    public int $omitidos = 0;

    public function fromFile(string $path): void
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            return;
        }

        // Normalizar cabeceras de la primera fila
        $headers = array_map(fn($h) => strtolower(trim((string) $h)), $rows[0]);
        unset($rows[0]);

        foreach ($rows as $rowIndex => $row) {
            $fila = $rowIndex + 1;
            $data = array_combine($headers, array_map(fn($v) => trim((string) $v), $row));

            $ci     = $data['ci'] ?? '';
            $nombre = $data['nombre'] ?? '';
            $email  = $data['email'] ?? '';

            if ($ci === '' || $nombre === '' || $email === '') {
                $this->errores[] = "Fila {$fila}: CI, nombre y email son obligatorios.";
                $this->omitidos++;
                continue;
            }

            if (DB::table('postulantes')->where('ci', $ci)->exists()) {
                $this->errores[] = "Fila {$fila}: CI '{$ci}' ya existe en el sistema.";
                $this->omitidos++;
                continue;
            }

            if (DB::table('users')->where('email', $email)->exists()) {
                $this->errores[] = "Fila {$fila}: Email '{$email}' ya registrado.";
                $this->omitidos++;
                continue;
            }

            DB::beginTransaction();
            try {
                $userId = DB::table('users')->insertGetId([
                    'name'              => $nombre,
                    'email'             => $email,
                    'password'          => Hash::make($ci),
                    'role'              => 'postulante',
                    'email_verified_at' => now(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                $sexo = strtoupper($data['sexo'] ?? '');
                $fechaNacimiento = $data['fecha_nacimiento'] ?? '';

                DB::table('postulantes')->insert([
                    'user_id'          => $userId,
                    'ci'               => $ci,
                    'telefono'         => $data['telefono'] ?? null,
                    'ciudad'           => $data['ciudad'] ?? null,
                    'colegio'          => $data['colegio'] ?? null,
                    'fecha_nacimiento' => ($fechaNacimiento !== '') ? $fechaNacimiento : null,
                    'sexo'             => in_array($sexo, ['M', 'F']) ? $sexo : null,
                    'estado'           => 'pendiente',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                DB::commit();
                $this->importados++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->errores[] = "Fila {$fila}: Error al guardar — {$e->getMessage()}";
                $this->omitidos++;
            }
        }
    }
}
