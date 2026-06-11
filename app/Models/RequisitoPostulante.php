<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoPostulante extends Model
{
    protected $fillable = [
        'postulante_id', 'requisito_id', 'inscripcion_id',
        'archivo_path', 'estado', 'fecha_entrega'
    ];

    protected $casts = ['fecha_entrega' => 'datetime'];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function requisito()
    {
        return $this->belongsTo(Requisito::class);
    }

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }
}