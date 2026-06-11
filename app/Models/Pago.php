<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'inscripcion_id', 'monto', 'moneda', 'metodo',
        'estado', 'transaccion_id', 'fecha_pago'
    ];

    protected $casts = ['fecha_pago' => 'datetime'];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }
}
