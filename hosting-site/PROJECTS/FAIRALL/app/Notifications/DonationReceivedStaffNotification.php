<?php
namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class DonationReceivedStaffNotification extends Notification
{
    use Queueable;

    public function __construct(protected Donation $donation)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $donation = $this->donation;
        $donorName = $donation->donor?->display_name ?? 'a guest';

        return [
            'type' => 'donation_received',
            'message' => "A donation of {$donation->amount} was received from {$donorName}.",
            'donation_id' => $donation->id,
            'action_url' => $donation->donor_id ? URL::route('donors.donations.show', [$donation->donor_id, $donation->id]) : URL::route('donors.donations.all'),
            'meta' => [
                'amount' => $donation->amount,
                'donor_name' => $donorName,
                'gateway' => $donation->gateway,
                'status' => $donation->status,
            ],
        ];
    }
}
