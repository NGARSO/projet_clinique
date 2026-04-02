<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'specialite',
        'email',
        'telephone',
        'matricule',
        'disponible',
    ];

    protected $casts = [
        'disponible' => 'boolean',
    ];

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'medecin_id');
    }
}
