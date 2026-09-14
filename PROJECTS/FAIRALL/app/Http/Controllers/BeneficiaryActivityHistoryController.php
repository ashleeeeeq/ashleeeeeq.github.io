<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Beneficiary;
use App\Models\CompetitionResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BeneficiaryActivityHistoryController extends Controller
{
    /**
     * Display activity history for a beneficiary with optional filtering.
     */
    public function index(Request $request, Beneficiary $beneficiary): View
    {
        $beneficiary->load(['user', 'programs', 'activePrograms']);

        $filterType = $request->query('type', 'all');
        $page = $request->query('page', 1);
        $perPage = 10;

        // Collect all activity records with timestamps
        $allRecords = [];

        // Get activities
        if (in_array($filterType, ['all', 'activity'])) {
            $activities = $beneficiary->activities()
                ->with('activityType')
                ->get()
                ->map(function ($activity) {
                    return [
                        'type' => 'activity',
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'timestamp' => $activity->created_at,
                        'details' => $activity->activityType?->name ?? 'Activity',
                        'url' => route('activities.show', $activity),
                    ];
                });
            $allRecords = array_merge($allRecords, $activities->toArray());
        }

        // Get events (from attendances)
        if (in_array($filterType, ['all', 'event'])) {
            $events = Attendance::query()
                ->where('beneficiary_id', $beneficiary->id)
                ->whereHas('event')
                ->with('event.eventType')
                ->get()
                ->map(function ($attendance) {
                    return [
                        'type' => 'event',
                        'id' => $attendance->event_id,
                        'name' => $attendance->event->name,
                        'timestamp' => $attendance->event->start,
                        'details' => $attendance->event->eventType?->name ?? 'Event',
                        'url' => route('events.show', $attendance->event),
                    ];
                });
            $allRecords = array_merge($allRecords, $events->toArray());
        }

        // Get competition results
        if (in_array($filterType, ['all', 'competition'])) {
            $competitionResults = CompetitionResult::query()
                ->where('beneficiary_id', $beneficiary->id)
                ->with('competition')
                ->get()
                ->map(function ($result) {
                    return [
                        'type' => 'competition',
                        'id' => $result->id,
                        'name' => $result->competition?->name ?? 'Competition',
                        'timestamp' => $result->created_at,
                        'details' => 'Placement: ' . $result->placement,
                        'url' => route('competitions.show', $result->competition),
                    ];
                });
            $allRecords = array_merge($allRecords, $competitionResults->toArray());
        }

        // Get home visits
        if (in_array($filterType, ['all', 'home_visit'])) {
            $homeVisits = $beneficiary->homeVisits()
                ->get()
                ->map(function ($visit) {
                    return [
                        'type' => 'home_visit',
                        'id' => $visit->id,
                        'name' => 'Home Visit',
                        'timestamp' => $visit->schedule ?? $visit->created_at,
                        'details' => ucfirst(str_replace('-', ' ', $visit->visit_type)),
                        'url' => route('home-visits.show', $visit),
                    ];
                });
            $allRecords = array_merge($allRecords, $homeVisits->toArray());
        }

        // Sort by timestamp descending (newest first)
        usort($allRecords, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        // Paginate
        $totalRecords = count($allRecords);
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($page - 1) * $perPage;
        $paginatedRecords = array_slice($allRecords, $offset, $perPage);

        return view('beneficiaries.activity_history.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'records' => $paginatedRecords,
            'filterType' => $filterType,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'perPage' => $perPage,
        ]);
    }
}
