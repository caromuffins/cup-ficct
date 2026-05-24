<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $fillable = [
        'postulante_id', 'gestion_id', 'carrera_primera_id',
        'carrera_segunda_id', 'estado', 'fecha_inscripcion'
    ];

    protected $casts = ['fecha_inscripcion' => 'datetime'];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function carreraPrimera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_primera_id');
    }

    public function carreraSegunda()
    {
        return $this->belongsTo(Carrera::class, 'carrera_segunda_id');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }

    public function requisitos()
    {
        return $this->hasMany(RequisitoPostulante::class);
    }
}