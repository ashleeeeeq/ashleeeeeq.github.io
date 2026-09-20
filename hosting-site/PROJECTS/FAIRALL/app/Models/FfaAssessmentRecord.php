<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FfaAssessmentRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'beneficiary_id',
        'assessment_category_id',
        'name',
        'score',
        'max_score',
        'remarks',
        'date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'date' => 'date',
    ];

    public $timestamps = true;

    // relationships

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function assessmentCategory(): BelongsTo
    {
        return $this->belongsTo(AssessmentCategory::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'sourceable', 'source_module', 'source_record_id');
    }
}
