<?php

namespace App\Enums;

enum ReportPeriod: string
{
    case Quarterly = 'quarterly';
    case Annual = 'annual';
    case MultiYear = 'multi_year';

    public function label(): string
    {
        return match ($this) {
            self::Quarterly => 'Quarterly',
            self::Annual => 'Annual',
            self::MultiYear => 'Multi-Year',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
