<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'skill_name',
        'skill_category',
        'proficiency_level',
        'certification',
        'certification_expiry',
        'acquired_date',
        'notes',
    ];

    protected $casts = [
        'certification_expiry' => 'date',
        'acquired_date' => 'date',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
