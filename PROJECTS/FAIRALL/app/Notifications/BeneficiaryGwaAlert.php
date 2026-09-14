<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BeneficiaryGwaAlert extends Notification
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
            'type' => 'beneficiary_gwa',
            'message' => $this->payload['message'] ?? 'Beneficiary GWA did not meet required increase',
            'beneficiary_id' => $this->payload['beneficiary_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
