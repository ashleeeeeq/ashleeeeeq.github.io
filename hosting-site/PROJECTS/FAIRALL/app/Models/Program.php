<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_name',
    ];

    public function beneficiaries(): BelongsToMany
    {
        return $this->belongsToMany(Beneficiary::class, 'beneficiary_program_memberships')
            ->using(BeneficiaryProgramMembership::class)
            ->withPivot(['id', 'enrolled_at', 'exited_at', 'created_by'])
            ->withTimestamps();
    }
}
