<?php

namespace App\Models;

use App\Models\Concerns\HasFormattedContact;
use App\Models\Concerns\HasPhoneNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'organization_name', 'first_name', 'middle_name', 'last_name', 'contact_number', 'dial_code', 'donor_type', 'created_by', 'updated_by'])]
class Donor extends Model
{
    use HasFormattedContact, HasPhoneNumber, SoftDeletes;
    // relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    

    public function deliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function getFullNameAttribute(): string
    {
        if ($this->donor_type === "individual") {
            return trim(implode(' ', array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
            ])));
        } else {
            return trim(implode(' ', array_filter([
                $this->organization_name
            ])));
        }
    }

    public function getDisplayNameAttribute(): string
    {
        return \Illuminate\Support\Str::of($this->full_name)->squish()->title()->value();
    }

}