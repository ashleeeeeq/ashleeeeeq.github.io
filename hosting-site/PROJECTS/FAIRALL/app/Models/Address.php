<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'beneficiary_id',
        'guardian_id',
        'address_line',
        'city',
        'province',
        'zip',
        'country',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryGuardian::class);
    }

    public function toFormArray(): array
    {
        return $this->only([
            'address_line',
            'city',
            'province',
            'zip',
            'country',
        ]);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->address_line}, {$this->city}, {$this->province}, {$this->zip}, {$this->country}";
    }
}
