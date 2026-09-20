<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffInviteNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $temporaryPassword,
        protected string $verificationUrl,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $recipientName = $notifiable->display_name ?: $notifiable->email;

        return (new MailMessage)
            ->subject('Your FAIRALL staff account is ready')
            ->greeting('Hello ' . $recipientName . ',')
            ->line('A staff account has been created for you in FAIRALL.')
            ->line('Temporary password: ' . $this->temporaryPassword)
            ->line('Please verify your email address using the link below, then sign in with your temporary password.')
            ->action('Verify Email', $this->verificationUrl)
            ->line('For security, change your password after your first sign in.')
            ->salutation('Thanks, FAIRALL');
    }
}
