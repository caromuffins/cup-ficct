<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    protected $fillable = ['materia_id', 'gestion_id', 'nombre', 'tipo', 'puntaje_maximo', 'fecha'];

    protected $casts = ['fecha' => 'date'];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
}
