<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivitySession extends Model
{
    protected $fillable = [
        'activity_id',
        'schedule',
        'qr_token',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'schedule' => 'datetime',
    ];

    // relationships

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
