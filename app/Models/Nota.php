<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = ['postulante_id', 'examen_id', 'grupo_id', 'puntaje', 'fecha_registro'];

    protected $casts = ['fecha_registro' => 'datetime'];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }
}
