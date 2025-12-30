<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $table = 'classes';

    // Mets ici les champs réellement présents dans ta table classes
    protected $fillable = [
        'nom',
    ];

    /**
     * Une classe peut avoir plusieurs matières (pivot classe_matiere)
     */
    public function matieres()
    {
        return $this->belongsToMany(
            \App\Models\Matiere::class,
            'classe_matiere',
            'classe_id',
            'matiere_id'
        )->withTimestamps();
    }
}
