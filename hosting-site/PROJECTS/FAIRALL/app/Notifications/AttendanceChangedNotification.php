<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceChangedNotification extends Notification
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
            'type' => 'attendance_changed',
            'message' => $this->payload['message'] ?? 'Your attendance record was updated',
            'attendance_id' => $this->payload['attendance_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
