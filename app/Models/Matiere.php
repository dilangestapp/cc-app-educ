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

    /**
     * Une matière peut appartenir à plusieurs classes (pivot classe_matiere)
     */
    public function classes()
    {
        return $this->belongsToMany(
            \App\Models\Classe::class,
            'classe_matiere',
            'matiere_id',
            'classe_id'
        )->withTimestamps();
    }
}
