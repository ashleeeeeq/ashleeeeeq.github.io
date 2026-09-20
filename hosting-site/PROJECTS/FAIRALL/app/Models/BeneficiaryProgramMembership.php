<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BeneficiaryProgramMembership extends Pivot
{
    public $timestamps = true;

    protected $table = 'beneficiary_program_memberships';

    protected $fillable = [
        'beneficiary_id',
        'program_id',
        'enrolled_at',
        'exited_at',
        'created_by',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'exited_at' => 'datetime',
    ];

    // relationships

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
