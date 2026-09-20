<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SportType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    // relationships

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
