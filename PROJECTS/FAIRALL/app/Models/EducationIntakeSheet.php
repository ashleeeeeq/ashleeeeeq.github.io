<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationIntakeSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_id',
        'age',
        'place_of_birth',
        'number_of_siblings',
        'older_sibling_age',
        'younger_sibling_age',
        'civil_status',
        'highest_education',
        'school_name',
        'grade_level',
        'other_scholarship',
        'scholarship_org_question',
        'hardworking_question',
        'dream_question',
        'scholarship_question',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'other_scholarship' => 'boolean',
    ];

    // relationships

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
