<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case Generating = 'generating';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Generating => 'Generating',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
