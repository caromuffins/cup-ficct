<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $fillable = [
        'grupo_id', 'materia_id', 'docente_id',
        'aula_id', 'dia', 'hora_inicio', 'hora_fin'
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }
}
