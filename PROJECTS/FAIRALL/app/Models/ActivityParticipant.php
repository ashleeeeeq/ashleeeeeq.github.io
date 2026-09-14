<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ActivityParticipant extends Pivot
{
    public $incrementing = true;

    protected $primaryKey = 'id';

    protected $table = 'activity_participants';

    protected $fillable = [
        'activity_id',
        'beneficiary_id',
        'staff_id',
        'joined_at',
        'exited_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'exited_at' => 'datetime',
    ];

    // relationships

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
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
