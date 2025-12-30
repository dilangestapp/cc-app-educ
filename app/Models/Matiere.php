<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;

    protected $table = 'matieres';

    protected $fillable = [
        'nom',
    ];

    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'classe_matiere', 'matiere_id', 'classe_id');
    }
}
