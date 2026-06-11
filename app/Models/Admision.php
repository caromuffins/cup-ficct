<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admision extends Model
{
    protected $fillable = [
        'postulante_id', 'gestion_id', 'carrera_asignada_id',
        'promedio_general', 'admitido', 'opcion_asignada', 'fecha_publicacion'
    ];

    protected $casts = [
        'admitido' => 'boolean',
        'fecha_publicacion' => 'datetime',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function carreraAsignada()
    {
        return $this->belongsTo(Carrera::class, 'carrera_asignada_id');
    }
}