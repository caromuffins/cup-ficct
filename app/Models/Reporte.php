<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $fillable = [
        'gestion_id', 'tipo', 'formato',
        'generado_por', 'ruta_archivo', 'fecha_generacion'
    ];

    protected $casts = ['fecha_generacion' => 'datetime'];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function generadoPor()
    {
        return $this->belongsTo(User::class, 'generado_por');
    }
}