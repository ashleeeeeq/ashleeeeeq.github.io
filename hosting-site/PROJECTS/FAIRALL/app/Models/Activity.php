<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'program_id',
        'sport_type_id',
        'activity_type_id',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // relationships

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    public function sportType(): BelongsTo
    {
        return $this->belongsTo(SportType::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function beneficiaries(): BelongsToMany
    {
        return $this->belongsToMany(Beneficiary::class, 'activity_participants')
            ->using(ActivityParticipant::class)
            ->withPivot(['id', 'staff_id', 'joined_at', 'exited_at', 'created_by', 'updated_by'])
            ->withTimestamps()
            ->wherePivotNotNull('beneficiary_id')
            ->wherePivotNull('exited_at');
    }

    public function staffParticipants(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'activity_participants', 'activity_id', 'staff_id')
            ->using(ActivityParticipant::class)
            ->withPivot(['id', 'beneficiary_id', 'joined_at', 'exited_at', 'created_by', 'updated_by'])
            ->withTimestamps()
            ->wherePivotNotNull('staff_id')
            ->wherePivotNull('exited_at');
    }

    public function participantRecords(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class);
    }

    public function activitySessions(): HasMany
    {
        return $this->hasMany(ActivitySession::class);
    }
}
