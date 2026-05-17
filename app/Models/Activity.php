<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    protected $fillable = [
        'crop_stage_id',
        'crop_cycle_id',
        'field_id',
        'activity_name',
        'activity_type',
        'description',
        'cost',
        'activity_date',
        'staff_id',
        'quantity',
        'notes',
        'status',
        'supervisor_id',
        'labor_type',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'quantity' => 'decimal:2',
        'activity_date' => 'date',
        'status' => 'string',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const TYPE_WEEDING = 'weeding';
    const TYPE_FERTILIZER = 'fertilizer_application';
    const TYPE_SPRAYING = 'spraying';
    const TYPE_IRRIGATION = 'irrigation';
    const TYPE_PRUNING = 'pruning_training';
    const TYPE_SCOUTING = 'scouting_inspection';
    const TYPE_THINNING = 'thinning_gapping';
    const TYPE_SOIL_NUTRITION = 'soil_crop_nutrition';
    const TYPE_HARVESTING = 'harvesting';

    public static function getActivityTypes(): array
    {
        return [
            self::TYPE_WEEDING => 'Weeding',
            self::TYPE_FERTILIZER => 'Fertilizer Application',
            self::TYPE_SPRAYING => 'Spraying',
            self::TYPE_IRRIGATION => 'Irrigation',
            self::TYPE_PRUNING => 'Pruning/Training',
            self::TYPE_SCOUTING => 'Scouting/Inspection',
            self::TYPE_THINNING => 'Thinning/Gapping',
            self::TYPE_SOIL_NUTRITION => 'Soil/Crop Nutrition',
            self::TYPE_HARVESTING => 'Harvesting',
        ];
    }

    /**
     * Get the crop stage that owns the activity.
     */
    public function cropStage(): BelongsTo
    {
        return $this->belongsTo(CropStage::class);
    }

    /**
     * Get the crop cycle that owns the activity.
     */
    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    /**
     * Get the field where this activity occurred.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the primary staff member assigned to this activity.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the supervisor who approved/rejected this activity.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'supervisor_id');
    }

    /**
     * Get the inputs for the activity.
     */
    public function inputs(): HasMany
    {
        return $this->hasMany(Input::class);
    }

    /**
     * Get the staff members assigned to this activity.
     */
    public function assignedStaff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'activity_staff')
            ->withTimestamps();
    }

    /**
     * Get the equipment used for this activity.
     */
    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'activity_equipment')
            ->withPivot('notes')
            ->withTimestamps();
    }

    /**
     * Get the images for the activity.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ActivityImage::class);
    }
}
