<?php
namespace App\Observers;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\AttendanceChangedNotification;
use App\Services\MonitoringService;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        $this->notifyRelevantUsers($attendance);

        if ($attendance->beneficiary_id) {
            (new MonitoringService())->evaluateAttendanceForBeneficiary($attendance->beneficiary);
        }
    }

    public function updated(Attendance $attendance): void
    {
        if (!$attendance->wasChanged('attendance_status')) {
            return;
        }

        $this->notifyRelevantUsers($attendance);

        if ($attendance->beneficiary_id) {
            (new MonitoringService())->evaluateAttendanceForBeneficiary($attendance->beneficiary);
        }
    }

    protected function notifyRelevantUsers(Attendance $attendance): void
    {
        $context = $attendance->activitySession?->activity?->name ?? $attendance->event?->name ?? 'attendance';
        $actionUrl = $this->getActionUrl($attendance);
        $beneficiaryName = $attendance->beneficiary?->getDisplayNameAttribute() ?? ('#' . $attendance->beneficiary_id);

        $staffUser = $attendance->staff?->user;

        $notified = collect();

        if ($staffUser) {
            $staffUser->notify(new AttendanceChangedNotification([
                'attendance_id' => $attendance->id,
                'message' => sprintf('Your attendance record was recorded/updated for %s', $context),
                'action_url' => $actionUrl,
            ]));
            $notified->push($staffUser->id);
        }
    }

    protected function getActionUrl(Attendance $attendance): ?string
    {
        if ($attendance->activitySession?->activity_id) {
            return route('activities.show', $attendance->activitySession->activity_id);
        }
        if ($attendance->event_id) {
            return route('events.show', $attendance->event_id);
        }
        return null;
    }
}
