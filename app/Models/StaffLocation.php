<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'field_id',
        'farm_id',
        'latitude',
        'longitude',
        'location_type',
        'checkin_type',
        'qr_code',
        'checked_in_at',
        'checked_out_at',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return !is_null($this->checked_in_at) && is_null($this->checked_out_at);
    }
}
