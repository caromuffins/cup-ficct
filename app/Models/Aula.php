<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $fillable = ['nombre', 'edificio', 'capacidad', 'disponible'];

    protected $casts = ['disponible' => 'boolean'];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
