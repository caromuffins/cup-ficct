<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function docente()
    {
        return $this->hasOne(Docente::class);
    }

    public function postulante()
    {
        return $this->hasOne(Postulante::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDocente()
    {
        return $this->role === 'docente';
    }

    public function isPostulante()
    {
        return $this->role === 'postulante';
    }
}