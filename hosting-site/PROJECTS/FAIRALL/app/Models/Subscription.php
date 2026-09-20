<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['donor_id','paypal_subscription_id','plan_id','amount','currency','status','next_billing_date','metadata','created_by'])]
class Subscription extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'next_billing_date' => 'date',
            'metadata' => 'array',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function displayPlanName(): string
    {
        $name = data_get($this->metadata ?? [], 'subscription_plan.name');

        if (is_string($name) && trim($name) !== '') {
            return trim($name);
        }

        foreach (config('services.paypal.subscription_plans', []) as $plan) {
            if (($plan['plan_id'] ?? null) === $this->plan_id) {
                return (string) ($plan['name'] ?? $this->plan_id ?? 'Plan');
            }
        }

        return (string) ($this->plan_id ?? 'Plan');
    }
}
