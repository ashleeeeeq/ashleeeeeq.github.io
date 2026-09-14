<?php
namespace App\Observers;

use App\Models\Beneficiary;
use App\Models\Event;
use App\Models\User;
use App\Notifications\NewEventNotification;

class EventObserver
{
    public function created(Event $event): void
    {
        $this->notifyAdminAndEd($event);
        $this->notifyBeneficiaries($event);
    }

    protected function notifyAdminAndEd(Event $event): void
    {
        $adminEdUsers = User::getAdminAndEdUsers();
        foreach ($adminEdUsers as $adminUser) {
            $adminUser->notify(new NewEventNotification([
                'event_id' => $event->id,
                'message' => sprintf('New event created: %s', $event->name),
                'action_url' => route('events.show', $event->id),
            ]));
        }
    }

    protected function notifyBeneficiaries(Event $event): void
    {
        if ($event->program) {
            $beneficiaries = $event->program->beneficiaries()
                ->whereNull('exited_at')
                ->with('user')
                ->get();
        } else {
            $beneficiaries = Beneficiary::whereHas('user')
                ->with('user')
                ->get();
        }

        foreach ($beneficiaries as $beneficiary) {
            if (!$beneficiary->user) continue;
            $beneficiary->user->notify(new NewEventNotification([
                'event_id' => $event->id,
                'message' => sprintf('Fairplay has a new event: %s. Check the events tab for more information.', $event->name),
                'action_url' => url('/events/' . $event->id),
            ]));
        }
    }
}
