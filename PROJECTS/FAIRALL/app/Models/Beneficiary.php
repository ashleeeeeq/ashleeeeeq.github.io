<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasFormattedContact;
use App\Models\Concerns\HasPhoneNumber;
use App\Models\EducationEnrollment;

class Beneficiary extends Model
{
    use HasFactory, HasFormattedContact, HasPhoneNumber, SoftDeletes;

    public $timestamps = true;

    protected $table = 'beneficiaries';

    public const SEX_OPTIONS = [
        'male',
        'female',
    ];

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'birth_date',
        'sex',
        'contact_number',
        'dial_code',
        'form_given',
        'with_disability',
        'created_by',
        'updated_by',
    ];

    // relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Beneficiary Management Module

    public function guardians(): HasMany
    {
        return $this->hasMany(BeneficiaryGuardian::class);
    }

    public function intakeSheet(): HasOne
    {
        return $this->hasOne(EducationIntakeSheet::class);
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'beneficiary_program_memberships')
            ->using(BeneficiaryProgramMembership::class)
            ->withPivot(['id', 'enrolled_at', 'exited_at', 'created_by'])
            ->withTimestamps();
    }

    /**
     * Active programs (where pivot.exited_at IS NULL)
     */
    public function activePrograms(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'beneficiary_program_memberships')
            ->using(BeneficiaryProgramMembership::class)
            ->withPivot(['id', 'enrolled_at', 'exited_at', 'created_by'])
            ->withTimestamps()
            ->wherePivot('exited_at', null); // means beneficiary is still enrolled.
    }

    public function academicRecords(): HasMany
    {
        return $this->HasMany(AcademicRecord::class);
    }

    public function educationEnrollments(): HasMany
    {
        return $this->hasMany(EducationEnrollment::class);
    }

    public function ffaAssessmentRecords(): HasMany
    {
        return $this->HasMany(FfaAssessmentRecord::class);
    }

    public function injuryRecords(): HasMany
    {
        return $this->HasMany(InjuryRecord::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(BeneficiaryStatus::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    // Activity Management and Attendance Tracking Module

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_participants')
            ->using(ActivityParticipant::class)
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function homeVisits(): HasMany
    {
        return $this->hasMany(HomeVisit::class);
    }

    public function competitionResults(): HasMany
    {
        return $this->hasMany(CompetitionResult::class);
    }

    protected $casts = [
        'birth_date' => 'date:Y-m-d',
        'form_given' => 'boolean',
        'with_disability' => 'boolean',
    ];

    protected $appends = ['full_address', 'school_name', 'academic_year'];

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function getDisplayNameAttribute(): string
    {
        return \Illuminate\Support\Str::of($this->full_name)->squish()->title()->value();
    }

    public function getGradeLevelAttribute(): ?string
    {
        $latestEnrollment = $this->relationLoaded('educationEnrollments')
            ? $this->educationEnrollments->sortByDesc(fn(EducationEnrollment $enrollment) => [$enrollment->academic_year_end_date, $enrollment->academic_year_start_date])->first()
            : $this->educationEnrollments()->orderByDesc('academic_year_end_date')->orderByDesc('academic_year_start_date')->first();

        return $latestEnrollment?->grade_level ?? $this->attributes['grade_level'] ?? null;
    }

    public function getSchoolNameAttribute(): ?string
    {
        $latestEnrollment = $this->relationLoaded('educationEnrollments')
            ? $this->educationEnrollments->sortByDesc(fn(EducationEnrollment $enrollment) => [$enrollment->academic_year_end_date, $enrollment->academic_year_start_date])->first()
            : $this->educationEnrollments()->orderByDesc('academic_year_end_date')->orderByDesc('academic_year_start_date')->first();

        return $latestEnrollment?->school_name ?? null;
    }

    public function getFullAddressAttribute(): ?string
    {
        $address = $this->address;
        if (!$address) return null;
        return $address->full_address;
    }

    public function getAcademicYearAttribute(): ?string
    {
        $latestEnrollment = $this->relationLoaded('educationEnrollments')
            ? $this->educationEnrollments->sortByDesc(fn(EducationEnrollment $enrollment) => [$enrollment->academic_year_end_date, $enrollment->academic_year_start_date])->first()
            : $this->educationEnrollments()->orderByDesc('academic_year_end_date')->orderByDesc('academic_year_start_date')->first();

        return $latestEnrollment?->academic_year_start_date && $latestEnrollment?->academic_year_end_date
            ? $latestEnrollment->academic_year_start_date->format('Y') . '-' . $latestEnrollment->academic_year_end_date->format('Y')
            : null;
    }
}
