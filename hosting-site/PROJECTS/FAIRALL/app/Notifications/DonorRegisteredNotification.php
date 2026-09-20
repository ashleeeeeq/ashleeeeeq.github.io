<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DonorRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(protected array $payload)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'donor_registered',
            'message' => $this->payload['message'] ?? 'A new donor has registered via sign up.',
            'donor_id' => $this->payload['donor_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
