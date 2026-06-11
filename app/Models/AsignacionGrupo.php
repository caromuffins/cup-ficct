<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionGrupo extends Model
{
    protected $fillable = ['postulante_id', 'grupo_id', 'gestion_id', 'fecha_asignacion'];

    protected $casts = ['fecha_asignacion' => 'datetime'];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }
}
