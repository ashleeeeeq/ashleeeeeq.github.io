<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EducationEnrollment;

class AcademicRecord extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    // relationships

    public function beneficiary() : BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function educationEnrollment(): BelongsTo
    {
        return $this->belongsTo(EducationEnrollment::class);
    }

    public function subjectGrades(): HasMany
    {
        return $this->hasMany(SubjectGrade::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'sourceable', 'source_module', 'source_record_id');
    }

    public function getSchoolNameAttribute(): ?string
    {
        return $this->attributes['school_name'] ?? $this->educationEnrollment?->school_name;
    }

    public function getAcademicYearAttribute(): ?string
    {
        return $this->attributes['academic_year'] ?? ($this->educationEnrollment?->academic_year_start_date && $this->educationEnrollment?->academic_year_end_date
            ? $this->educationEnrollment->academic_year_start_date->format('Y') . '-' . $this->educationEnrollment->academic_year_end_date->format('Y')
            : null);
    }

    public function getGradeLevelAttribute(): ?string
    {
        return $this->attributes['grade_level'] ?? $this->educationEnrollment?->grade_level;
    }

    public function getSchoolAttendanceAttribute(): ?float
    {
        return $this->attributes['school_attendance'] ?? 0;
    }

    protected $fillable = [
        'beneficiary_id',
        'education_enrollment_id',
        'term',
        'gwa',
        'school_attendance',
        'created_by',
        'updated_by',
    ];
    
    protected $casts = [
        'gwa' => 'decimal:2',
        'school_attendance' => 'decimal:2', 
    ];

    protected static function booted(): void
    {
        static::deleting(function (AcademicRecord $record) {
            if ($record->isForceDeleting()) {
                $record->subjectGrades()->withTrashed()->each(fn (\App\Models\SubjectGrade $grade) => $grade->forceDelete());
            } else {
                $record->subjectGrades()->each(fn (\App\Models\SubjectGrade $grade) => $grade->delete());
            }
        });

        static::restoring(function (AcademicRecord $record) {
            $record->subjectGrades()->withTrashed()->each(fn (\App\Models\SubjectGrade $grade) => $grade->restore());
        });
    }
}