<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChemicalType extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'form',
        'dosage_per_m3',
        'dosage_per_hectare',
        'manufacturer_instructions',
        'toxicity_level',
        'target_pest',
        'restricted',
    ];
}
