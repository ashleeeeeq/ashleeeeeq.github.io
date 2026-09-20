<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GuestDonationReceived extends Notification
{
    use Queueable;

    public function __construct(protected Donation $donation)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $donation = $this->donation;

        return (new MailMessage)
            ->subject('Guest donation received — action required')
            ->greeting('Hello,')
            ->line('A donation was received that is not linked to a donor profile.')
            ->line('Amount: ' . number_format($donation->amount, 2) . ' ' . ($donation->currency ?? config('services.paypal.currency', 'USD')))
            ->line('Gateway: ' . ($donation->gateway ?? 'unknown'))
            ->line('Please review and optionally link this donation to a donor profile in the reconciliation UI.')
            ->action('Open Reconciliation', route('donors.donations.all', ['unmatched' => 1]))
            ->salutation('Thanks, FAIRALL');
    }
}