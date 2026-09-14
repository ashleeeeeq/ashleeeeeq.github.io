<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InjuryRecord extends Model
{
    public function beneficiary() : BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'sourceable', 'source_module', 'source_record_id');
    }

    protected $fillable = [
        'beneficiary_id',
        'injury_type',
        'severity',
        'body_part',
        'status',
        'recovery_start_date',
        'recovery_end_date',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'recovery_start_date' => 'date',
        'recovery_end_date' => 'date',
    ];
}
