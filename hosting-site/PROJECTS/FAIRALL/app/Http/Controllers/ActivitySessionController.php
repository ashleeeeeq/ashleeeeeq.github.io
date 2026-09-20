<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivitySession;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivitySessionController extends Controller
{
    private const WEEKDAY_LABELS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function index(Activity $activity): RedirectResponse
    {
        return redirect()->route('activities.show', $activity);
    }

    public function create(Activity $activity): View
    {
        return view('activities.sessions.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'activity' => $activity,
            'weekdayLabels' => self::WEEKDAY_LABELS,
        ]);
    }

    public function store(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_date' => ['required', 'date'],
            'schedule_time' => ['required', 'date_format:H:i'],
            'repeat_until_date' => ['nullable', 'date'],
            'repeat_until_time' => ['nullable', 'date_format:H:i'],
            'repeat_days' => ['required_with:repeat_until_date', 'array', 'min:1'],
            'repeat_days.*' => ['integer', 'between:0,6'],
        ]);

        // Merge date and time into datetime
        $scheduleDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['schedule_date'] . ' ' . $validated['schedule_time']
        );

        $repeatUntil = null;
        if (!empty($validated['repeat_until_date'])) {
            $repeatUntilTime = $validated['repeat_until_time'] ?? $validated['schedule_time'];
            $repeatUntil = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validated['repeat_until_date'] . ' ' . $repeatUntilTime
            );
        }

        $repeatDays = collect($validated['repeat_days'] ?? [])
            ->map(fn ($day) => (int) $day)
            ->unique()
            ->sort()
            ->values()
            ->all();

        DB::transaction(function () use ($activity, $scheduleDateTime, $repeatUntil, $repeatDays) {
            ActivitySession::create([
                'activity_id' => $activity->id,
                'schedule' => $scheduleDateTime->copy(),
                'qr_token' => Str::random(40),
                'created_by' => Auth::user()?->staff?->id,
                'updated_by' => Auth::user()?->staff?->id,
            ]);

            if (is_null($repeatUntil) || empty($repeatDays)) {
                return;
            }

            $current = $scheduleDateTime->copy()->addDay();

            while ($current->lessThanOrEqualTo($repeatUntil)) {
                if (in_array($current->dayOfWeek, $repeatDays, true)) {
                    ActivitySession::create([
                        'activity_id' => $activity->id,
                        'schedule' => $current->copy(),
                        'qr_token' => Str::random(40),
                        'created_by' => Auth::user()?->staff?->id,
                        'updated_by' => Auth::user()?->staff?->id,
                    ]);
                }

                $current->addDay();
            }
        });

        return redirect()->route('activities.show', $activity)->with('status', 'Activity session(s) added.');
    }

    public function edit(Activity $activity, ActivitySession $session): View
    {
        abort_unless($session->activity_id === $activity->id, 404);

        return view('activities.sessions.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'activity' => $activity,
            'session' => $session,
        ]);
    }

    public function update(Request $request, Activity $activity, ActivitySession $session): RedirectResponse
    {
        abort_unless($session->activity_id === $activity->id, 404);

        $validated = $request->validate([
            'schedule' => ['required', 'date'],
        ]);

        $session->update([
            'schedule' => $validated['schedule'],
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        return redirect()->route('activities.show', $activity)->with('status', 'Activity session updated.');
    }

    public function destroy(Activity $activity, ActivitySession $session): RedirectResponse
    {
        abort_unless($session->activity_id === $activity->id, 404);

        $session->delete();

        return redirect()->route('activities.show', $activity)->with('status', 'Activity session deleted.');
    }

    /**
     * Regenerate QR token for an activity session.
     */
    public function regenerateQr(Request $request, Activity $activity, ActivitySession $session): RedirectResponse
    {
        abort_unless($session->activity_id === $activity->id, 404);

        Gate::authorize('work-on-activities');

        $session->update([
            'qr_token' => Str::random(40),
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        return redirect()->route('activities.show', $activity)->with('status', 'Session QR regenerated.');
    }

    /**
     * Printable QR for session.
     */
    public function showQr(Activity $activity, ActivitySession $session)
    {
        abort_unless($session->activity_id === $activity->id, 404);

        return view('qr.printable', [
            'token' => $session->qr_token,
            'activityName' => $activity->name
            ]);
    }
}