<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'pseudo',
        'type_compte',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function homeRouteName(): string
    {
        $type = strtolower((string) ($this->type_compte ?? 'eleve'));

        return match ($type) {
            'admin' => 'dashboard',
            'enseignant' => 'enseignant.dashboard',
            'parent' => 'parent.dashboard',
            default => 'eleve.dashboard',
        };
    }
}
