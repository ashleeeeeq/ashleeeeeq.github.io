<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventType extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    // relationships

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
