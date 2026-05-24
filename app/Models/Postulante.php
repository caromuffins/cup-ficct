<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    protected $fillable = [
        'user_id', 'ci', 'fecha_nacimiento',
        'telefono', 'colegio', 'ciudad', 'estado'
    ];

    protected $casts = ['fecha_nacimiento' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function requisitos()
    {
        return $this->hasMany(RequisitoPostulante::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    public function resultados()
    {
        return $this->hasMany(ResultadoMateria::class);
    }

    public function admision()
    {
        return $this->hasOne(Admision::class);
    }

    public function asignacionGrupo()
    {
        return $this->hasOne(AsignacionGrupo::class);
    }
}
