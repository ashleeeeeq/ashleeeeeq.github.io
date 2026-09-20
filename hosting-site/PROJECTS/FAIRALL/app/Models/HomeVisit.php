<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeVisit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'beneficiary_id',
        'visit_type',
        'purpose',
        'notes',
        'schedule',
        'assigned_staff_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'schedule' => 'datetime:Y-m-d\TH:i:s',
        'deleted_at' => 'datetime',
    ];

    // relationships

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
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
