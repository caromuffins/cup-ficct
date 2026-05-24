<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $fillable = ['nombre', 'codigo', 'descripcion', 'cupo_maximo', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function inscripcionesPrimera()
    {
        return $this->hasMany(Inscripcion::class, 'carrera_primera_id');
    }

    public function inscripcionesSegunda()
    {
        return $this->hasMany(Inscripcion::class, 'carrera_segunda_id');
    }

    public function admisiones()
    {
        return $this->hasMany(Admision::class, 'carrera_asignada_id');
    }
}