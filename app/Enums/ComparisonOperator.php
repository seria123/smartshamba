<?php

namespace App\Enums;

/**
 * Comparison operators for automation rules
 */
enum ComparisonOperator: string
{
    case LESS_THAN = '<';
    case LESS_THAN_OR_EQUAL = '<=';
    case GREATER_THAN = '>';
    case GREATER_THAN_OR_EQUAL = '>=';
    case EQUALS = '==';
    case NOT_EQUALS = '!=';

    /**
     * Get all operators as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
