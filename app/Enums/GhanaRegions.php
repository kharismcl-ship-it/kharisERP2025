<?php

namespace App\Enums;

enum GhanaRegions: string
{
    case GREATER_ACCRA = 'Greater Accra';
    case ASHANTI = 'Ashanti';
    case CENTRAL = 'Central';
    case EASTERN = 'Eastern';
    case NORTHERN = 'Northern';
    case VOLTA = 'Volta';
    case WESTERN = 'Western';
    case UPPER_EAST = 'Upper East';
    case UPPER_WEST = 'Upper West';
    case BONO = 'Bono';
    case BONO_EAST = 'Bono East';
    case AHAFO = 'Ahafo';
    case SAVANNAH = 'Savannah';
    case NORTH_EAST = 'North East';
    case OTI = 'Oti';
    case WESTERN_NORTH = 'Western North';

    /**
     * Get all regions as an array
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the region code (enum name) for a given region name
     */
    public static function fromName(string $name): ?self
    {
        foreach (self::cases() as $region) {
            if ($region->value === $name) {
                return $region;
            }
        }

        return null;
    }

    /**
     * Get all countries as options array for Select fields
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'value')
        );
    }
}
