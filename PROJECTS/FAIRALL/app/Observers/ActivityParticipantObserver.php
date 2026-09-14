<?php
namespace App\Observers;

use App\Models\ActivityParticipant;
use App\Models\User;
use App\Notifications\ParticipantAddedNotification;
use App\Notifications\BeneficiaryRecordUpdatedNotification;

class ActivityParticipantObserver
{
    public function created(ActivityParticipant $participant): void
    {
        // notify the staff user if a staff participant was added
        if ($participant->staff_id && $participant->staff && $participant->staff->user) {
            $user = $participant->staff->user;
            $activityName = $participant->activity?->name ?? ('#' . $participant->activity_id);
            $user->notify(new ParticipantAddedNotification([
                'activity_id' => $participant->activity_id,
                'message' => sprintf('You were added to %s', $activityName),
                'action_url' => $participant->activity_id ? route('activities.show', $participant->activity_id) : null,
            ]));
        }

        // notify the beneficiary if a beneficiary participant was added
        if ($participant->beneficiary_id && $participant->beneficiary && $participant->beneficiary->user) {
            $user = $participant->beneficiary->user;
            $activityName = $participant->activity?->name ?? ('#' . $participant->activity_id);
            $user->notify(new BeneficiaryRecordUpdatedNotification([
                'type' => 'participant_added',
                'record_id' => $participant->id,
                'message' => sprintf('You were added to %s. Check the activity tab for more information.', $activityName),
                'action_url' => $participant->activity_id ? url('/activities/' . $participant->activity_id) : null,
            ]));
        }
    }
}
