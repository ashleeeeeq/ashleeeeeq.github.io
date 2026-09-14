<?php

namespace App\Observers;

use App\Models\AcademicRecord;
use App\Services\MonitoringService;

class AcademicRecordObserver
{
    public function saved(AcademicRecord $record): void
    {
        $beneficiary = $record->beneficiary ?? $record->educationEnrollment?->beneficiary;
        if (!$beneficiary) {
            return;
        }

        $service = new MonitoringService();
        $shouldNotify = false;

        if ($record->wasRecentlyCreated || $record->wasChanged('gwa')) {
            $service->evaluateGwaForBeneficiary($beneficiary);
            $shouldNotify = true;
        }

        if ($record->wasRecentlyCreated || $record->wasChanged('school_attendance')) {
            $service->evaluateSchoolAttendanceForBeneficiary($beneficiary, $record);
            $shouldNotify = true;
        }

        if ($shouldNotify) {
            $this->notifyBeneficiary($record);
        }
    }

    protected function notifyBeneficiary(AcademicRecord $record): void
    {
        if (!$record->beneficiary || !$record->beneficiary->user) return;

        $user = $record->beneficiary->user;
        $school = $record->school_name ?? $record->educationEnrollment?->school_name ?? 'School';
        $existing = $record->getOriginal() !== null;

        $user->notify(new \App\Notifications\BeneficiaryRecordUpdatedNotification([
            'type' => 'academic_record_updated',
            'record_id' => $record->id,
            'message' => sprintf(
                'Your academic record at %s has been %s. Check the enrollments tab for more information.',
                $school,
                $existing ? 'updated' : 'added'
            ),
            'action_url' => url('/beneficiaries/' . $record->beneficiary_id . '/profile'),
        ]));
    }
}
