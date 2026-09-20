<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SubscriptionCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(protected Subscription $subscription)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $sub = $this->subscription;

        return (new MailMessage)
            ->subject('Subscription cancelled — ' . $sub->displayPlanName())
            ->greeting('Hello,')
            ->line('Your subscription has been cancelled as requested.')
            ->line('Plan: ' . $sub->displayPlanName())
            ->line('Amount: ' . number_format((float) $sub->amount, 2) . ' ' . ($sub->currency ?? config('services.paypal.currency', 'USD')))
            ->line('If you have any questions, please contact our support team.')
            ->action('View Subscriptions', route('donor.portal.subscriptions.index'))
            ->salutation('Thanks, FAIRALL');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'subscription_cancelled',
            'subscription_id' => $this->subscription->id,
            'message' => 'Your ' . $this->subscription->displayPlanName() . ' subscription has been cancelled.',
            'action_url' => URL::route('donor.portal.subscriptions.index'),
            'meta' => [
                'plan_name' => $this->subscription->displayPlanName(),
                'amount' => $this->subscription->amount,
                'currency' => $this->subscription->currency,
            ],
        ];
    }
}
