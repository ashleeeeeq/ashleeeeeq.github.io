<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardMetric extends Model
{
    protected $fillable = [
        'program_id',
        'metric',
        'dimension',
        'year',
        'month',
        'value',
        'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'computed_at' => 'datetime',
        ];
    }

    public $timestamps = false;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
