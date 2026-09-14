<?php

namespace App\Observers;

use App\Models\CompetitionResult;

class CompetitionResultObserver
{
    public function created(CompetitionResult $record): void
    {
        $this->notifyBeneficiary($record, 'added');
    }

    public function updated(CompetitionResult $record): void
    {
        $this->notifyBeneficiary($record, 'updated');
    }

    protected function notifyBeneficiary(CompetitionResult $record, string $action): void
    {
        if (!$record->beneficiary || !$record->beneficiary->user) return;

        $user = $record->beneficiary->user;
        $competitionName = $record->competition?->name ?? 'Competition';
        $user->notify(new \App\Notifications\BeneficiaryRecordUpdatedNotification([
            'type' => 'competition_result_updated',
            'record_id' => $record->id,
            'message' => sprintf('The competition result for %s has been %s. Check the competitions tab for more information.', $competitionName, $action),
            'action_url' => url('/beneficiaries/' . $record->beneficiary_id . '/profile'),
        ]));
    }
}
