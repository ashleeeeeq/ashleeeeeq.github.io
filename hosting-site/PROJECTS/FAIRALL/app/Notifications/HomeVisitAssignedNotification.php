<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HomeVisitAssignedNotification extends Notification
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
            'type' => 'home_visit_assigned',
            'message' => $this->payload['message'] ?? 'You were assigned to a home visit',
            'home_visit_id' => $this->payload['home_visit_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
