<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Your FAIRALL Password')
            ->greeting('Hello ' . ($notifiable->display_name ?: $notifiable->email) . ',')
            ->line('We received a request to reset your FAIRALL account password.')
            ->action('Reset Password', $resetUrl)
            ->line('This reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is needed.')
            ->salutation('Thanks, FAIRALL Team');
    }
}
