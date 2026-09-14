<?php

namespace App\Enums;

enum ReportType: string
{
    case OrganizationalOverview = 'organizational_overview';
    case Education = 'education';
    case Sports = 'sports';
    case Funding = 'funding';

    public function label(): string
    {
        return match ($this) {
            self::OrganizationalOverview => 'Organizational Overview',
            self::Education => 'Education',
            self::Sports => 'Sports',
            self::Funding => 'Donors & Grants',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
