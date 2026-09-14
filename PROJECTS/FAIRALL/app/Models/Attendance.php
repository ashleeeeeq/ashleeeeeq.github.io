<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'staff_id',
        'beneficiary_id',
        'activity_session_id',
        'event_id',
        'attendance_status',
        'remarks',
        'attendance_method',
        'created_by',
        'updated_by',
    ];

    // relationships

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function activitySession(): BelongsTo
    {
        return $this->belongsTo(ActivitySession::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
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
