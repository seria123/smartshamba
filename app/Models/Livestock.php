<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livestock extends Model
{
    use HasFactory;

    protected $table = 'livestock';

    protected $fillable = [
        'user_id',
        'livestock_type_id',
        'farm_id',
        'tag_number',
        'name',
        'date_acquired',
        'weight',
        'birth_date',
        'gender',
        'parent_id',
        'status',
        'purchase_price',
        'sale_price',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'date_acquired' => 'date',
        'birth_date' => 'date',
    ];

    const STATUS_HEALTHY = 'healthy';

    const STATUS_SICK = 'sick';

    const STATUS_SOLD = 'sold';

    const STATUS_DEAD = 'dead';

    const GENDER_MALE = 'male';

    const GENDER_FEMALE = 'female';

    public function type(): BelongsTo
    {
        return $this->belongsTo(LivestockType::class, 'livestock_type_id');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Livestock::class, 'parent_id');
    }

    public function offspring(): HasMany
    {
        return $this->hasMany(Livestock::class, 'parent_id');
    }

    public function diseases(): HasMany
    {
        return $this->hasMany(LivestockDisease::class);
    }

    public function activeDiseases(): HasMany
    {
        return $this->hasMany(LivestockDisease::class)->where('status', 'active');
    }

    public function scopeHealthy($query)
    {
        return $query->where('status', self::STATUS_HEALTHY);
    }

    public function scopeSick($query)
    {
        return $query->where('status', self::STATUS_SICK);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_HEALTHY, self::STATUS_SICK]);
    }

    public function markAsSold(?float $salePrice = null): void
    {
        $this->status = self::STATUS_SOLD;
        if ($salePrice) {
            $this->sale_price = $salePrice;
        }
        $this->save();
    }

    public function markAsDead(): void
    {
        $this->status = self::STATUS_DEAD;
        $this->save();
    }

    public function markAsHealthy(): void
    {
        $this->status = self::STATUS_HEALTHY;
        $this->save();
    }

    public function markAsSick(): void
    {
        $this->status = self::STATUS_SICK;
        $this->save();
    }
}
