<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ManualDonationAddedNotification extends Notification
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

        return [
            'type' => 'manual_donation_added',
            'message' => 'A manual transaction of ' . number_format((float) $donation->amount, 2) . ' ' . ($donation->currency ?? config('services.paypal.currency', 'USD')) . ' was added to your account.',
            'donation_id' => $donation->id,
            'subscription_id' => $donation->subscription_id,
            'action_url' => URL::route('donor.portal.donations.index'),
            'meta' => [
                'gateway' => $donation->gateway,
                'status' => $donation->status,
                'reference_number' => $donation->reference_number,
            ],
        ];
    }
}