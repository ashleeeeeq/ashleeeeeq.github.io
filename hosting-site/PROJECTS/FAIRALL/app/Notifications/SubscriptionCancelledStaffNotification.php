<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SubscriptionCancelledStaffNotification extends Notification
{
    use Queueable;

    public function __construct(protected Subscription $subscription)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $donor = $this->subscription->donor;
        $donorName = $donor?->user?->display_name ?? $donor?->email ?? 'Unknown donor';

        return [
            'type' => 'subscription_cancelled',
            'subscription_id' => $this->subscription->id,
            'message' => $donorName . ' cancelled their ' . $this->subscription->displayPlanName() . ' subscription (' . number_format((float) $this->subscription->amount, 2) . ' ' . ($this->subscription->currency ?? config('services.paypal.currency', 'USD')) . ').',
            'action_url' => $this->subscription->donor_id ? URL::route('donors.show', $this->subscription->donor_id) : null,
            'meta' => [
                'plan_name' => $this->subscription->displayPlanName(),
                'amount' => $this->subscription->amount,
                'donor_id' => $this->subscription->donor_id,
            ],
        ];
    }
}
