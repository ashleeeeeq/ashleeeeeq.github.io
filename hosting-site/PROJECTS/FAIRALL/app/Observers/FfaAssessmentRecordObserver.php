<?php

namespace App\Observers;

use App\Models\FfaAssessmentRecord;

class FfaAssessmentRecordObserver
{
    public function created(FfaAssessmentRecord $record): void
    {
        $this->notifyBeneficiary($record, 'added');
    }

    public function updated(FfaAssessmentRecord $record): void
    {
        $this->notifyBeneficiary($record, 'updated');
    }

    protected function notifyBeneficiary(FfaAssessmentRecord $record, string $action): void
    {
        if (!$record->beneficiary || !$record->beneficiary->user) return;

        $user = $record->beneficiary->user;
        $category = $record->assessmentCategory?->assessment_name ?? 'FFA';
        $user->notify(new \App\Notifications\BeneficiaryRecordUpdatedNotification([
            'type' => 'ffa_assessment_updated',
            'record_id' => $record->id,
            'message' => sprintf('Your %s assessment record has been %s. Check the Youth Center tab for more information.', $category, $action),
            'action_url' => url('/beneficiaries/' . $record->beneficiary_id . '/profile'),
        ]));
    }
}
