<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BeneficiaryStatusEndingNotification extends Notification
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
            'type' => 'beneficiary_status_ending',
            'message' => $this->payload['message'] ?? 'A beneficiary status is nearing its end date',
            'beneficiary_id' => $this->payload['beneficiary_id'] ?? null,
            'status_id' => $this->payload['status_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
