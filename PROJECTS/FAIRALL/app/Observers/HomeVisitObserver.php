<?php

namespace App\Observers;

use App\Models\HomeVisit;
use App\Models\User;
use App\Notifications\HomeVisitAssignedNotification;

class HomeVisitObserver
{
    public function created(HomeVisit $homeVisit): void
    {
        $this->maybeNotifyAssignedStaff($homeVisit);
        $this->notifyAdminAndEd($homeVisit);
        $this->notifyBeneficiary($homeVisit, 'added');
    }

    public function updated(HomeVisit $homeVisit): void
    {
        $this->maybeNotifyAssignedStaff($homeVisit);
        $this->notifyBeneficiary($homeVisit, 'updated');
    }

    protected function maybeNotifyAssignedStaff(HomeVisit $homeVisit): void
    {
        if ($homeVisit->created_by === $homeVisit->assignedStaff->user->id) {
            return;
        }

        if ($homeVisit->assigned_staff_id && $homeVisit->assignedStaff && $homeVisit->assignedStaff->user) {
            $user = $homeVisit->assignedStaff->user;
            $beneficiaryName = $homeVisit->beneficiary?->display_name ?? ('#' . $homeVisit->beneficiary_id);
            $user->notify(new HomeVisitAssignedNotification([
                'home_visit_id' => $homeVisit->id,
                'message' => sprintf('You were assigned to a home visit for %s', $beneficiaryName),
                'action_url' => route('home-visits.show', $homeVisit->id),
            ]));
        }
    }

    protected function notifyAdminAndEd(HomeVisit $homeVisit): void
    {
        if ($homeVisit->created_by === $homeVisit->assignedStaff->user->id) {
            return;
        }

        $beneficiaryName = $homeVisit->beneficiary?->display_name ?? ('#' . $homeVisit->beneficiary_id);
        $adminEdUsers = User::getAdminAndEdUsers();
        foreach ($adminEdUsers as $adminUser) {
            $adminUser->notify(new HomeVisitAssignedNotification([
                'home_visit_id' => $homeVisit->id,
                'message' => sprintf('A home visit was scheduled for %s', $beneficiaryName),
                'action_url' => route('home-visits.show', $homeVisit->id),
            ]));
        }
    }

    protected function notifyBeneficiary(HomeVisit $record, string $action): void
    {
        if (!$record->beneficiary || !$record->beneficiary->user) return;

        $user = $record->beneficiary->user;
        if ($action == "added") {
            $user->notify(new \App\Notifications\BeneficiaryRecordUpdatedNotification([
                'type' => 'home_visit_updated',
                'record_id' => $record->id,
                'message' => 'You are scheduled for a home visit. Check the home visits tab for more information.',
                'action_url' => url('/beneficiaries/' . $record->beneficiary_id . '/profile'),
            ]));
        } else {
            $user->notify(new \App\Notifications\BeneficiaryRecordUpdatedNotification([
                'type' => 'home_visit_updated',
                'record_id' => $record->id,
                'message' => 'A home visit record has been updated. Check the home visits tab for more information.',
                'action_url' => url('/beneficiaries/' . $record->beneficiary_id . '/profile'),
            ]));
        }
    }
}
