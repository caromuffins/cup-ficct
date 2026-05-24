<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $fillable = [
        'anio', 'periodo', 'fecha_inicio', 'fecha_fin',
        'activa', 'cupo_por_carrera', 'monto_inscripcion'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function examenes()
    {
        return $this->hasMany(Examen::class);
    }

    public function admisiones()
    {
        return $this->hasMany(Admision::class);
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }

    public function getNombreCompletoAttribute()
    {
        return "Gestión {$this->periodo} {$this->anio}";
    }
}
