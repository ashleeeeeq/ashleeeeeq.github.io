<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewEventNotification extends Notification
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
            'type' => 'new_event',
            'message' => $this->payload['message'] ?? 'Fairplay has a new event available. Check the events tab for more information.',
            'event_id' => $this->payload['event_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
