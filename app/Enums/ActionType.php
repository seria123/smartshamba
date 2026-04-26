<?php

namespace App\Enums;

/**
 * Action types available for automation
 */
enum ActionType: string
{
    case IRRIGATION_ON = 'irrigation_on';
    case IRRIGATION_OFF = 'irrigation_off';
    case COOLING_ON = 'cooling_on';
    case COOLING_OFF = 'cooling_off';
    case SHADE_ON = 'shade_on';
    case SHADE_OFF = 'shade_off';
    case ALERT = 'alert';

    /**
     * Get all action types as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display name for the action type
     */
    public function displayName(): string
    {
        return match ($this) {
            self::IRRIGATION_ON => 'Turn Irrigation ON',
            self::IRRIGATION_OFF => 'Turn Irrigation OFF',
            self::COOLING_ON => 'Activate Cooling',
            self::COOLING_OFF => 'Deactivate Cooling',
            self::SHADE_ON => 'Activate Shade',
            self::SHADE_OFF => 'Deactivate Shade',
            self::ALERT => 'Send Alert',
        };
    }

    /**
     * Get icon for the action type
     */
    public function icon(): string
    {
        return match ($this) {
            self::IRRIGATION_ON => '💧',
            self::IRRIGATION_OFF => '🚫',
            self::COOLING_ON => '❄️',
            self::COOLING_OFF => '🔥',
            self::SHADE_ON => '☂️',
            self::SHADE_OFF => '☀️',
            self::ALERT => '⚠️',
        };
    }
}
