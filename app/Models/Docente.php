<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $fillable = ['user_id', 'ci', 'telefono', 'especialidad', 'max_grupos', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionDocente::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'asignacion_docentes');
    }
}
