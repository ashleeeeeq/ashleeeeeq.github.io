<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'beneficiary_id',
        'school_name',
        'academic_year_start_date',
        'academic_year_end_date',
        'education_level',
        'grade_level',
        'enrollment_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'academic_year_start_date' => 'date',
        'academic_year_end_date' => 'date',
    ];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function academicRecords(): HasMany
    {
        return $this->hasMany(AcademicRecord::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (EducationEnrollment $enrollment) {
            if ($enrollment->isForceDeleting()) {
                $enrollment->academicRecords()->withTrashed()->each(fn (AcademicRecord $record) => $record->forceDelete());
            } else {
                $enrollment->academicRecords()->each(fn (AcademicRecord $record) => $record->delete());
            }
        });

        static::restoring(function (EducationEnrollment $enrollment) {
            $enrollment->academicRecords()->withTrashed()->each(fn (AcademicRecord $record) => $record->restore());
        });
    }
}
