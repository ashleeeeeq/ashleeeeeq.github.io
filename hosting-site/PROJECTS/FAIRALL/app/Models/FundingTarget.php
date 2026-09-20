<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingTarget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'year',
        'target_amount_cents',
        'currency',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'target_amount_cents' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function getTargetAmountAttribute(): string
    {
        return number_format($this->target_amount_cents / 100, 2, '.', '');
    }
}
