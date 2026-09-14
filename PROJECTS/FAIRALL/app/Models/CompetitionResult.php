<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionResult extends Model
{
    protected $casts = [
        'date_given' => 'date',
    ];

    protected $fillable = [
        'competition_id',
        'beneficiary_id',
        'placement',
        'date_given',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    // relationships

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }
}
