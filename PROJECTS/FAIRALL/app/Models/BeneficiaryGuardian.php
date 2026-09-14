<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Concerns\HasFormattedContact;
use App\Models\Concerns\HasPhoneNumber;

class BeneficiaryGuardian extends Model
{
    use HasFactory, HasFormattedContact, HasPhoneNumber;

    protected $fillable = [
        'beneficiary_id',
        'guardian_type',
        'first_name',
        'middle_name',
        'last_name',
        'civil_status',
        'birth_date',
        'place_of_birth',
        'sex',
        'contact_number',
        'dial_code',
        'highest_education',
        'job',
        'estimated_salary',
        'deceased',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'deceased' => 'boolean',
    ];

    // relationships

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'guardian_id');
    }
}
