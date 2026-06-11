<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = ['gestion_id', 'nombre', 'turno', 'cupo_maximo', 'cupo_actual'];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionGrupo::class);
    }

    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class, 'asignacion_grupos');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function asignacionDocentes()
    {
        return $this->hasMany(AsignacionDocente::class);
    }

    public function estaLleno()
    {
        return $this->cupo_actual >= $this->cupo_maximo;
    }

    public function getPorcentajeAprobados()
    {
        $total = $this->postulantes()->count();
        if ($total === 0) return 0;
        $aprobados = $this->postulantes()
            ->whereHas('admision', fn($q) => $q->where('admitido', true))
            ->count();
        return round(($aprobados / $total) * 100, 2);
    }
}
