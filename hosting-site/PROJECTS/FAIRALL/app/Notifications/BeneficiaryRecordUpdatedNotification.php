<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BeneficiaryRecordUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(protected array $payload) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->payload['type'],
            'message' => $this->payload['message'],
            'record_id' => $this->payload['record_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
