<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeneficiaryStatusType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status_name',
    ];

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'beneficiary_status_type_program');
    }

    public function beneficiaryStatuses(): HasMany
    {
        return $this->hasMany(BeneficiaryStatus::class);
    }
}
