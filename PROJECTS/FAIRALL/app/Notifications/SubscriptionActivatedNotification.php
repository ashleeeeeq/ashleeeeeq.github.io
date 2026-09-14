<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SubscriptionActivatedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Donation $donation)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $donation = $this->donation;
        $subscription = $donation->subscription;

        return [
            'type' => 'subscription_activated',
            'message' => 'Your subscription is now active and the first payment of ' . number_format((float) $donation->amount, 2) . ' ' . ($donation->currency ?? config('services.paypal.currency', 'USD')) . ' was received.',
            'donation_id' => $donation->id,
            'subscription_id' => $donation->subscription_id,
            'action_url' => URL::route('donor.portal.subscriptions.index'),
            'meta' => [
                'plan_name' => $subscription?->displayPlanName(),
                'gateway' => $donation->gateway,
                'gateway_reference' => $donation->gateway_reference,
                'status' => $subscription?->status,
            ],
        ];
    }
}