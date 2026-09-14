<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivitySession;
use App\Models\ActivityParticipant;
use App\Models\Attendance;
use App\Models\Competition;
use App\Models\CompetitionResult;
use App\Models\Event;
use App\Models\HomeVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Get all activities, events, and competitions that the beneficiary is involved in
     * Active = ongoing (is_active = 1 for activities, end date in future for events/competitions)
     * Archived = completed (is_active = 0 or end date in past)
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary not found'
                ], 404);
            }
            
            $activeActivities = [];
            $archivedActivities = [];
            $now = Carbon::now();
            
            // --- 1. Activities (from activity_participants) ---
            $participations = ActivityParticipant::where('beneficiary_id', $beneficiary->id)
                ->whereNull('exited_at')
                ->with(['activity.program'])
                ->get();
            
            foreach ($participations as $participation) {
                $activity = $participation->activity;
                if (!$activity) continue;
                
                $activityData = [
                    'id' => $activity->id,
                    'title' => $activity->name,
                    'category' => 'Activities',
                    'program' => $activity->program ? $activity->program->program_name : 'N/A',
                    'is_registered' => true,
                    'is_active_flag' => $activity->is_active ?? false,
                ];
                
                if ($activity->is_active) {
                    $activeActivities[] = $activityData;
                } else {
                    $archivedActivities[] = $activityData;
                }
            }
            
            // --- 2. Events (from attendances) ---
            $eventIds = Attendance::where('beneficiary_id', $beneficiary->id)
                ->whereNotNull('event_id')
                ->pluck('event_id')
                ->unique();
            
            if ($eventIds->isNotEmpty()) {
                $events = Event::whereIn('id', $eventIds)
                    ->with('program')
                    ->get();
                
                foreach ($events as $event) {
                    $isActive = $event->end && Carbon::parse($event->end)->gte($now);
                    
                    $eventData = [
                        'id' => $event->id,
                        'title' => $event->name,
                        'category' => 'Events',
                        'program' => $event->program ? $event->program->program_name : 'N/A',
                        'is_registered' => true,
                        'is_active_flag' => $isActive,
                        'start' => $event->start ? $event->start->timezone('Asia/Manila')->format('M d, Y h:i A') : null,
                        'end' => $event->end ? $event->end->timezone('Asia/Manila')->format('M d, Y h:i A') : null,
                        'location' => $event->location,
                    ];
                    
                    if ($isActive) {
                        $activeActivities[] = $eventData;
                    } else {
                        $archivedActivities[] = $eventData;
                    }
                }
            }
            
            // --- 3. Competitions (from competition_results) ---
            $competitionResults = CompetitionResult::where('beneficiary_id', $beneficiary->id)
                ->get()
                ->keyBy('competition_id');
            
            if ($competitionResults->isNotEmpty()) {
                $competitions = Competition::whereIn('id', $competitionResults->keys())
                    ->with('program')
                    ->get();
                
                foreach ($competitions as $competition) {
                    $isActive = $competition->end && Carbon::parse($competition->end)->gte($now);
                    $result = $competitionResults->get($competition->id);
                    
                    $competitionData = [
                        'id' => $competition->id,
                        'title' => $competition->name,
                        'category' => 'Competitions',
                        'program' => $competition->program ? $competition->program->program_name : 'N/A',
                        'is_registered' => true,
                        'is_active_flag' => $isActive,
                        'type' => $competition->type,
                        'scale' => $competition->scale,
                        'organizer' => $competition->organizer,
                        'venue' => $competition->venue,
                        'start' => $competition->start ? $competition->start->timezone('Asia/Manila')->format('M d, Y') : null,
                        'end' => $competition->end ? $competition->end->timezone('Asia/Manila')->format('M d, Y') : null,
                        'placement' => $result ? $result->placement : null,
                        'date_given' => $result && $result->date_given ? $result->date_given->timezone('Asia/Manila')->format('M d, Y') : null,
                    ];
                    
                    if ($isActive) {
                        $activeActivities[] = $competitionData;
                    } else {
                        $archivedActivities[] = $competitionData;
                    }
                }
            }
            
            // --- 4. Home Visits (always shown, no active/archive split) ---
            $homeVisits = HomeVisit::where('beneficiary_id', $beneficiary->id)
                ->orderBy('schedule', 'desc')
                ->get();
            
            foreach ($homeVisits as $visit) {
                $activeActivities[] = [
                    'id' => $visit->id,
                    'title' => $visit->purpose ?? 'Home Visit',
                    'category' => 'Home Visits',
                    'program' => 'N/A',
                    'is_registered' => true,
                    'is_active_flag' => true,
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'active' => $activeActivities,
                    'archived' => $archivedActivities,
                    'total_active' => count($activeActivities),
                    'total_archived' => count($archivedActivities),
                ],
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Activity index error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load activities: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get activity details with attendance history
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary not found'
                ], 404);
            }
            
            // Check if beneficiary is registered
            $participation = ActivityParticipant::where('activity_id', $id)
                ->where('beneficiary_id', $beneficiary->id)
                ->whereNull('exited_at')
                ->first();
            
            if (!$participation) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not registered for this activity'
                ], 403);
            }
            
            $activity = Activity::with(['activityType', 'program', 'activitySessions'])->find($id);
            
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity not found'
                ], 404);
            }
            
            $sessions = $activity->activitySessions->sortByDesc('schedule');
            $now = Carbon::now();
            $joinedAt = $participation->joined_at;
            $upcomingSessions = [];
            $pastSessions = [];
            $attendanceSummary = [
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'excused' => 0,
                'skip' => 0,
            ];
            
            foreach ($sessions as $session) {
                $sessionDate = Carbon::parse($session->schedule);
                
                // Skip sessions before the beneficiary enrolled
                if ($joinedAt && $sessionDate->lt(Carbon::parse($joinedAt))) {
                    continue;
                }
                
                $isUpcoming = $sessionDate->gte($now);
                
                // Get attendance
                $attendance = Attendance::where('beneficiary_id', $beneficiary->id)
                    ->where('activity_session_id', $session->id)
                    ->first();
                
                $status = 'skip';
                if ($attendance) {
                    $status = $attendance->attendance_status;
                    $attendanceSummary[$status]++;
                } else {
                    $attendanceSummary['skip']++;
                }
                
                $sessionData = [
                    'id' => $session->id,
                    'date' => $sessionDate->timezone('Asia/Manila')->format('M d, Y'),
                    'time' => $sessionDate->timezone('Asia/Manila')->format('h:i A'),
                    'status' => strtoupper($status),
                    'is_upcoming' => $isUpcoming,
                ];
                
                if ($isUpcoming) {
                    $upcomingSessions[] = $sessionData;
                } else {
                    $pastSessions[] = $sessionData;
                }
            }
            
            // Status is based on is_active flag
            // active = is_active = 1, archived = is_active = 0
            $status = $activity->is_active ? 'active' : 'archived';
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $activity->id,
                    'title' => $activity->name,
                    'description' => $activity->description,
                    'category' => $activity->activityType ? $activity->activityType->name : 'General',
                    'program' => $activity->program ? $activity->program->program_name : 'N/A',
                    'is_registered' => true,
                    'status' => $status,
                    'is_active' => $activity->is_active,
                    'total_sessions' => $sessions->count(),
                    'upcoming_sessions_count' => count($upcomingSessions),
                    'past_sessions_count' => count($pastSessions),
                    'attendance_summary' => $attendanceSummary,
                    'upcoming_sessions' => $upcomingSessions,
                    'past_sessions' => $pastSessions,
                ],
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Activity show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load activity details: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get only active activities (is_active = 1)
     */
    public function getActiveActivities(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary not found'
                ], 404);
            }
            
            // Get only active activities (is_active = 1)
            $participations = ActivityParticipant::where('beneficiary_id', $beneficiary->id)
                ->whereNull('exited_at')
                ->whereHas('activity', function ($query) {
                    $query->where('is_active', true);
                })
                ->with(['activity.activityType', 'activity.program'])
                ->get();
            
            $activeActivities = [];
            
            foreach ($participations as $participation) {
                $activity = $participation->activity;
                if ($activity) {
                    $activeActivities[] = [
                        'id' => $activity->id,
                        'title' => $activity->name,
                        'description' => $activity->description,
                        'category' => $activity->activityType ? $activity->activityType->name : 'General',
                        'program' => $activity->program ? $activity->program->program_name : 'N/A',
                        'is_registered' => true,
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $activeActivities,
                'total' => count($activeActivities),
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Get active activities error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load active activities: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get only archived activities (is_active = 0)
     */
    public function getArchivedActivities(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary not found'
                ], 404);
            }
            
            // Get only archived activities (is_active = 0)
            $participations = ActivityParticipant::where('beneficiary_id', $beneficiary->id)
                ->whereNull('exited_at')
                ->whereHas('activity', function ($query) {
                    $query->where('is_active', false);
                })
                ->with(['activity.activityType', 'activity.program'])
                ->get();
            
            $archivedActivities = [];
            
            foreach ($participations as $participation) {
                $activity = $participation->activity;
                if ($activity) {
                    $archivedActivities[] = [
                        'id' => $activity->id,
                        'title' => $activity->name,
                        'description' => $activity->description,
                        'category' => $activity->activityType ? $activity->activityType->name : 'General',
                        'program' => $activity->program ? $activity->program->program_name : 'N/A',
                        'is_registered' => true,
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $archivedActivities,
                'total' => count($archivedActivities),
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Get archived activities error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load archived activities: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Register for an activity (only if active)
     */
    public function register(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user || $user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary not found'
                ], 404);
            }
            
            $activity = Activity::with('activitySessions')->find($id);
            
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity not found'
                ], 404);
            }
            
            // Check if activity is active (is_active = 1)
            if (!$activity->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot register for archived activity'
                ], 400);
            }
            
            // Check if already registered
            $existing = ActivityParticipant::where('activity_id', $id)
                ->where('beneficiary_id', $beneficiary->id)
                ->whereNull('exited_at')
                ->exists();
            
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already registered for this activity'
                ], 400);
            }
            
            // Create registration
            ActivityParticipant::create([
                'activity_id' => $id,
                'beneficiary_id' => $beneficiary->id,
                'joined_at' => Carbon::now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully registered for the activity'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Activity register error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to register: ' . $e->getMessage()
            ], 500);
        }
    }
}