<?php

namespace App\Models;

use App\Enums\ReportPeriod;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'period_type',
        'period_config',
        'date_from',
        'date_to',
        'program_id',
        'status',
        'generation_phase',
        'narrative_cache',
        'boot_payload',
        'pdf_path',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'period_config' => 'array',
            'narrative_cache' => 'array',
            'boot_payload' => 'array',
            'date_from' => 'date:Y-m-d',
            'date_to' => 'date:Y-m-d',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function typeEnum(): ?ReportType
    {
        return ReportType::tryFrom($this->type);
    }

    public function periodEnum(): ?ReportPeriod
    {
        return ReportPeriod::tryFrom($this->period_type);
    }

    public function statusEnum(): ?ReportStatus
    {
        return ReportStatus::tryFrom($this->status);
    }

    public function isCompleted(): bool
    {
        return $this->status === ReportStatus::Completed->value;
    }

    public function isFailed(): bool
    {
        return $this->status === ReportStatus::Failed->value;
    }

    public function isGenerating(): bool
    {
        return in_array($this->status, [ReportStatus::Pending->value, ReportStatus::Generating->value], true);
    }

    public function periodLabel(): string
    {
        $config = $this->period_config;

        return match ($this->period_type) {
            'quarterly' => sprintf('Q%d %d', $config['quarter'] ?? 1, $config['year'] ?? date('Y')),
            'annual' => (string) ($config['year'] ?? date('Y')),
            'multi_year' => sprintf('%d–%d', $config['from_year'] ?? date('Y'), $config['to_year'] ?? date('Y')),
            default => (string) ($this->date_from?->format('Y-m-d') . ' to ' . $this->date_to?->format('Y-m-d')),
        };
    }
}
