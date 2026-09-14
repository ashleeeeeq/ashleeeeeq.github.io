<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'program_id',
        'type',
        'scale',
        'organizer',
        'description',
        'venue',
        'start',
        'end',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // relationships

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function competitionResults(): HasMany
    {
        return $this->hasMany(CompetitionResult::class);
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
