<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisito extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'obligatorio', 'tipo_archivo', 'activo'];

    protected $casts = [
        'obligatorio' => 'boolean',
        'activo' => 'boolean',
    ];

    public function requisitoPostulantes()
    {
        return $this->hasMany(RequisitoPostulante::class);
    }
}