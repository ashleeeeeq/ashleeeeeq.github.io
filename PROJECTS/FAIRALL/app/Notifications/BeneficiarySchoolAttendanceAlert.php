<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BeneficiarySchoolAttendanceAlert extends Notification
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
            'type' => 'beneficiary_school_attendance',
            'message' => $this->payload['message'] ?? 'Beneficiary school attendance below threshold',
            'beneficiary_id' => $this->payload['beneficiary_id'] ?? null,
            'academic_record_id' => $this->payload['academic_record_id'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }
}
