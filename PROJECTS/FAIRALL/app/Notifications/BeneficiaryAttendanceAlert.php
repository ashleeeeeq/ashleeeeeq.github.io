<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BeneficiaryAttendanceAlert extends Notification
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
            'type' => 'beneficiary_attendance',
            'message' => $this->payload['message'] ?? 'Beneficiary attendance below threshold',
            'beneficiary_id' => $this->payload['beneficiary_id'] ?? null,
            'activity_id' => $this->payload['activity_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
