<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SubscriptionRenewalReminderNotification extends Notification
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
            ->subject('Upcoming subscription renewal — ' . $sub->displayPlanName())
            ->greeting('Hello,')
            ->line('Your subscription is scheduled for renewal soon.')
            ->line('Plan: ' . $sub->displayPlanName())
            ->line('Amount: ' . number_format((float) $sub->amount, 2) . ' ' . ($sub->currency ?? config('services.paypal.currency', 'USD')))
            ->line('Next billing date: ' . ($sub->next_billing_date?->format('F d, Y') ?? 'N/A'))
            ->action('Manage Subscription', route('donor.portal.subscriptions.index'))
            ->salutation('Thanks, FAIRALL');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'subscription_renewal_reminder',
            'subscription_id' => $this->subscription->id,
            'message' => 'Your ' . $this->subscription->displayPlanName() . ' subscription renews on ' . ($this->subscription->next_billing_date?->format('M d, Y') ?? 'N/A') . '.',
            'action_url' => URL::route('donor.portal.subscriptions.index'),
            'meta' => [
                'plan_name' => $this->subscription->displayPlanName(),
                'amount' => $this->subscription->amount,
                'next_billing_date' => $this->subscription->next_billing_date?->toDateString(),
            ],
        ];
    }
}
