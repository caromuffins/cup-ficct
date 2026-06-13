<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    
    // La tabla de bitacora solo registra inserciones, no actualizaciones.
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'accion',
        'modulo',
        'descripcion',
        'modelo_tipo',
        'modelo_id',
        'ip',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Obtener el usuario que realizó la acción.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Función estática auxiliar para registrar logs de forma explícita en cualquier parte del sistema.
     *
     * @param string $accion Acción realizada (CREAR, MODIFICAR, ELIMINAR, etc.)
     * @param string $modulo Módulo afectado (Postulantes, Notas, Docentes, etc.)
     * @param string $descripcion Descripción detallada de la acción
     * @param string|null $modeloTipo Clase del modelo afectado (opcional)
     * @param int|null $modeloId ID del registro del modelo afectado (opcional)
     */
    public static function registrar(string $accion, string $modulo, string $descripcion, ?string $modeloTipo = null, ?int $modeloId = null): void
    {
        if (auth()->check()) {
            self::insert([
                'user_id'     => auth()->id(),
                'accion'      => substr($accion, 0, 50),
                'modulo'      => substr($modulo, 0, 60),
                'descripcion' => $descripcion,
                'modelo_tipo' => $modeloTipo ? substr($modeloTipo, 0, 80) : null,
                'modelo_id'   => $modeloId,
                'ip'          => substr(request()->ip(), 0, 45),
                'created_at'  => now(),
            ]);
        }
    }
}
