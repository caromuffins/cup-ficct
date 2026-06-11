<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = ['nombre', 'codigo', 'descripcion'];

    public function examenes()
    {
        return $this->hasMany(Examen::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function asignacionDocentes()
    {
        return $this->hasMany(AsignacionDocente::class);
    }

    public function resultados()
    {
        return $this->hasMany(ResultadoMateria::class);
    }
}
