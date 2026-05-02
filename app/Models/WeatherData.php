<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherData extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'crop_cycle_id',
        'recorded_at',
        'temperature',
        'humidity',
        'precipitation',
        'wind_speed',
        'wind_direction',
        'pressure',
        'uv_index',
        'cloud_cover',
        'dew_point',
        'weather_condition',
        'feels_like',
        'visibility',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'precipitation' => 'decimal:2',
        'wind_speed' => 'decimal:2',
        'wind_direction' => 'decimal:2',
        'pressure' => 'decimal:2',
        'uv_index' => 'decimal:2',
        'cloud_cover' => 'decimal:2',
        'dew_point' => 'decimal:2',
        'feels_like' => 'decimal:2',
        'visibility' => 'decimal:2',
    ];

    const CONDITION_CLEAR = 'clear';
    const CONDITION_CLOUDY = 'cloudy';
    const CONDITION_PARTLY_CLOUDY = 'partly_cloudy';
    const CONDITION_RAINY = 'rainy';
    const CONDITION_STORMY = 'stormy';
    const CONDITION_SNOWY = 'snowy';
    const CONDITION_FOGGY = 'foggy';
    const CONDITION_WINDY = 'windy';
    const CONDITION_UNKNOWN = 'unknown';

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function getTemperatureFAttribute(): float
    {
        return ($this->temperature * 9/5) + 32;
    }

    public function getWindSpeedKphAttribute(): float
    {
        return $this->wind_speed * 3.6;
    }

    public function getWindDirectionNameAttribute(): string
    {
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = round($this->wind_direction / 22.5) % 16;
        return $directions[$index];
    }

    public function getUvIndexLevelAttribute(): string
    {
        if (!$this->uv_index) return 'Unknown';
        
        return match(true) {
            $this->uv_index <= 2 => 'Low',
            $this->uv_index <= 5 => 'Moderate',
            $this->uv_index <= 7 => 'High',
            $this->uv_index <= 10 => 'Very High',
            default => 'Extreme',
        };
    }

    public function isGoodForIrrigation(): bool
    {
        return !in_array($this->weather_condition, [
            self::CONDITION_RAINY,
            self::CONDITION_STORMY,
        ]) && $this->precipitation < 2;
    }

    public function isGoodForSpraying(): bool
    {
        return $this->wind_speed < 5 && 
               $this->humidity > 40 && 
               !in_array($this->weather_condition, [self::CONDITION_RAINY, self::CONDITION_STORMY]);
    }
}
