<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockDisease extends Model
{
    use HasFactory;

    protected $table = 'livestock_diseases';

    protected $fillable = [
        'livestock_id',
        'disease_id',
        'name',
        'species',
        'cause',
        'symptoms',
        'transmission',
        'prevention',
        'treatment',
        'status',
        'diagnosed_date',
        'treated_date',
        'treated_by',
        'mortality_rate',
        'severity',
    ];

    protected $casts = [
        'diagnosed_date' => 'date',
        'treated_date' => 'date',
    ];

    const STATUS_ACTIVE = 'active';

    const STATUS_TREATED = 'treated';

    const STATUS_CHRONIC = 'chronic';

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }

    public function treatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'treated_by');
    }

    public function markAsTreated(int $treatedByUserId): void
    {
        $this->status = self::STATUS_TREATED;
        $this->treated_date = now();
        $this->treated_by = $treatedByUserId;
        $this->save();

        $this->livestock->markAsHealthy();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeTreated($query)
    {
        return $query->where('status', self::STATUS_TREATED);
    }
}
