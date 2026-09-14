<?php

namespace App\Observers;

use App\Models\InjuryRecord;

class InjuryRecordObserver
{
    public function created(InjuryRecord $record): void
    {
        $this->notifyBeneficiary($record, 'added');
    }

    public function updated(InjuryRecord $record): void
    {
        $this->notifyBeneficiary($record, 'updated');
    }

    protected function notifyBeneficiary(InjuryRecord $record, string $action): void
    {
        if (!$record->beneficiary || !$record->beneficiary->user) return;

        $user = $record->beneficiary->user;
        $injuryType = $record->formatted_injury_type ?? $record->injury_type ?? 'Injury';
        $user->notify(new \App\Notifications\BeneficiaryRecordUpdatedNotification([
            'type' => 'injury_record_updated',
            'record_id' => $record->id,
            'message' => sprintf('Your injury record of type: %s has been %s. Check the injuries tab for more information.', $injuryType, $action),
            'action_url' => url('/beneficiaries/' . $record->beneficiary_id . '/profile'),
        ]));
    }
}
