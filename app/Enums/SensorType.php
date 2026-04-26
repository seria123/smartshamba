<?php

namespace App\Enums;

/**
 * Sensor types available in the SmartShamba system
 */
enum SensorType: string
{
    case SOIL_MOISTURE = 'soil_moisture';
    case SOIL_PH = 'soil_ph';
    case TEMPERATURE = 'temperature';
    case HUMIDITY = 'humidity';
    case LIGHT_INTENSITY = 'light_intensity';
    case RAIN_DETECTION = 'rain_detection';

    /**
     * Get all sensor types as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display name for the sensor type
     */
    public function displayName(): string
    {
        return match ($this) {
            self::SOIL_MOISTURE => 'Soil Moisture',
            self::SOIL_PH => 'Soil pH',
            self::TEMPERATURE => 'Temperature',
            self::HUMIDITY => 'Humidity',
            self::LIGHT_INTENSITY => 'Light Intensity',
            self::RAIN_DETECTION => 'Rain Detection',
        };
    }

    /**
     * Get unit for the sensor type
     */
    public function unit(): string
    {
        return match ($this) {
            self::SOIL_MOISTURE => '%',
            self::SOIL_PH => 'pH',
            self::TEMPERATURE => '°C',
            self::HUMIDITY => '%',
            self::LIGHT_INTENSITY => 'lux',
            self::RAIN_DETECTION => 'boolean',
        };
    }

    /**
     * Get min value for the sensor type
     */
    public function minValue(): float
    {
        return match ($this) {
            self::SOIL_MOISTURE => 0,
            self::SOIL_PH => 0,
            self::TEMPERATURE => -40,
            self::HUMIDITY => 0,
            self::LIGHT_INTENSITY => 0,
            self::RAIN_DETECTION => 0,
        };
    }

    /**
     * Get max value for the sensor type
     */
    public function maxValue(): float
    {
        return match ($this) {
            self::SOIL_MOISTURE => 100,
            self::SOIL_PH => 14,
            self::TEMPERATURE => 80,
            self::HUMIDITY => 100,
            self::LIGHT_INTENSITY => 100000,
            self::RAIN_DETECTION => 1,
        };
    }
}
