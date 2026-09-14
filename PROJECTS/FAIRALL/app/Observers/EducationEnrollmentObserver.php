<?php

namespace App\Observers;

use App\Models\EducationEnrollment;

class EducationEnrollmentObserver
{
    public function created(EducationEnrollment $record): void
    {
        $this->notifyBeneficiary($record, 'added');
    }

    public function updated(EducationEnrollment $record): void
    {
        $this->notifyBeneficiary($record, 'updated');
    }

    protected function notifyBeneficiary(EducationEnrollment $record, string $action): void
    {
        if (!$record->beneficiary || !$record->beneficiary->user) return;

        $user = $record->beneficiary->user;
        $school = $record->school_name ?? 'School';
        $user->notify(new \App\Notifications\BeneficiaryRecordUpdatedNotification([
            'type' => 'enrollment_updated',
            'record_id' => $record->id,
            'message' => sprintf('Your enrollment at %s has been %s. Check the enrollment tab for more information.', $school, $action),
            'action_url' => url('/beneficiaries/' . $record->beneficiary_id . '/profile'),
        ]));
    }
}
