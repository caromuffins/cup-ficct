<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoMateria extends Model
{
    protected $fillable = [
        'postulante_id', 'materia_id', 'gestion_id',
        'total_parcial1', 'total_parcial2', 'total_final',
        'total', 'aprobado'
    ];

    protected $casts = ['aprobado' => 'boolean'];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function calcular()
    {
        $this->total = $this->total_parcial1 + $this->total_parcial2 + $this->total_final;
        $this->aprobado = $this->total >= 60;
        $this->save();
    }
}