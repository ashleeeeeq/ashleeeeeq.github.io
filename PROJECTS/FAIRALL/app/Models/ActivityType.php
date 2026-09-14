<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityType extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'program_id'];

    // relationships

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    // scopes

    public function scopeForProgram($query, ?int $programId)
    {
        if (is_null($programId)) {
            return $query;
        }

        return $query->where(function ($q) use ($programId) {
            $q->where('program_id', $programId)
              ->orWhereNull('program_id');
        });
    }
}
