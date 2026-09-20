<?php

namespace App\Listeners;

use App\Services\PushNotificationService;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Log;

class SendPushNotification
{
    public function handle(NotificationSent $event): void
    {
        if ($event->channel !== 'database') {
            return;
        }

        $notifiable = $event->notifiable;

        if (!$notifiable instanceof \App\Models\User) {
            return;
        }

        $notification = $event->response;
        $data = $notification->data;

        $type = $data['type'] ?? 'system';
        $message = $data['message'] ?? '';

        if ($notifiable->user_type === 'donor' && in_array($type, [
            'donation_cleared',
            'donation_received',
            'manual_donation_added',
            'subscription_activated',
        ])) {
            return;
        }

        $title = $this->getTitleForType($type);

        try {
            app(PushNotificationService::class)->send($notifiable, $title, $message, ['type' => $type]);
        } catch (\Exception $e) {
            Log::error('Push notification failed', ['error' => $e->getMessage()]);
        }
    }

    private function getTitleForType(string $type): string
    {
        return match ($type) {
            'donation_cleared' => 'DONATION CLEARED',
            'donation_received' => 'DONATION RECEIVED',
            'manual_donation_added' => 'DONATION ADDED',
            'subscription_activated' => 'SUBSCRIPTION ACTIVE',
            'donor_registered' => 'NEW DONOR',
            'participant_added' => 'ACTIVITY ASSIGNED',
            'attendance_changed' => 'ATTENDANCE UPDATED',
            'home_visit_assigned' => 'HOME VISIT ASSIGNED',
            'beneficiary_attendance' => 'ATTENDANCE ALERT',
            'beneficiary_school_attendance' => 'SCHOOL ATTENDANCE ALERT',
            'beneficiary_gwa' => 'GWA ALERT',
            'activity_assigned' => 'ACTIVITY ASSIGNED',
            'new_event' => 'NEW EVENT',
            'academic_record_updated' => 'ACADEMIC RECORD UPDATED',
            'ffa_assessment_updated' => 'ASSESSMENT UPDATED',
            'injury_record_updated' => 'INJURY RECORD UPDATED',
            'enrollment_updated' => 'ENROLLMENT UPDATED',
            'competition_result_updated' => 'COMPETITION RESULT UPDATED',
            'home_visit_updated' => 'HOME VISIT UPDATED',
            default => 'NOTIFICATION',
        };
    }
}
