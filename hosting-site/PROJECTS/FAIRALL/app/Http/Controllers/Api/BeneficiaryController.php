<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\AcademicRecord;
use App\Models\ActivityParticipant;
use App\Models\ActivitySession;
use App\Models\EducationEnrollment;
use App\Models\Competition;
use App\Models\CompetitionResult;
use App\Models\Event;
use App\Models\HomeVisit;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficiaryController extends Controller
{
    /**
     * Get beneficiary dashboard data
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $beneficiary = $user->beneficiary;

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary not found',
            ], 404);
        }

        $beneficiary->load('activePrograms');
        $activePrograms = $beneficiary->activePrograms;
        $programCount = $activePrograms->count();

        $programType = match (true) {
            $programCount === 0 => 'none',
            $programCount >= 2 => 'mixed',
            default => strtolower(trim((string) $activePrograms->first()->program_name)),
        };

        $category = $request->query('category', 'Education');

        if ($programType === 'education') {
            $category = 'Education';
        } elseif ($programType === 'sports') {
            $category = 'Sports';
        }

        if ($category === 'Education') {
            $metrics = $this->getEducationMetrics($beneficiary->id, $request);
        } else {
            $metrics = $this->getSportsMetrics($beneficiary->id, $request);
        }

        $recentItems = [];

        // 1. Activity attendance (present/late)
        $activityAttendances = Attendance::with(['activitySession.activity.activityType'])
            ->where('attendances.beneficiary_id', $beneficiary->id)
            ->whereNotNull('attendances.activity_session_id')
            ->whereIn('attendances.attendance_status', ['present', 'late'])
            ->join('activity_sessions', 'attendances.activity_session_id', '=', 'activity_sessions.id')
            ->orderBy('activity_sessions.schedule', 'desc')
            ->select('attendances.*')
            ->limit(10)
            ->get();

        foreach ($activityAttendances as $a) {
            $recentItems[] = [
                'title' => $a->activitySession?->activity?->name ?? 'Activity',
                'date' => $a->activitySession?->schedule?->timezone('Asia/Manila')->format('F j, Y'),
                'category' => 'Activities',
                '_sort' => $a->activitySession?->schedule,
            ];
        }

        // 2. Event attendance
        $eventAttendances = Attendance::with('event')
            ->where('attendances.beneficiary_id', $beneficiary->id)
            ->whereNotNull('attendances.event_id')
            ->whereIn('attendances.attendance_status', ['present', 'late'])
            ->join('events', 'attendances.event_id', '=', 'events.id')
            ->orderBy('events.start', 'desc')
            ->select('attendances.*')
            ->limit(10)
            ->get();

        foreach ($eventAttendances as $a) {
            $recentItems[] = [
                'title' => $a->event?->name ?? 'Event',
                'date' => $a->event?->start?->timezone('Asia/Manila')->format('F j, Y'),
                'category' => 'Events',
                '_sort' => $a->event?->start,
            ];
        }

        // 3. Competition results
        $competitionResults = CompetitionResult::with('competition')
            ->where('beneficiary_id', $beneficiary->id)
            ->orderBy('date_given', 'desc')
            ->limit(10)
            ->get();

        foreach ($competitionResults as $r) {
            $recentItems[] = [
                'title' => $r->competition?->name ?? 'Competition',
                'date' => $r->date_given?->timezone('Asia/Manila')->format('F j, Y'),
                'category' => 'Competitions',
                '_sort' => $r->date_given,
            ];
        }

        // 4. Home visits
        $homeVisits = HomeVisit::where('beneficiary_id', $beneficiary->id)
            ->orderBy('schedule', 'desc')
            ->limit(10)
            ->get();

        foreach ($homeVisits as $v) {
            $recentItems[] = [
                'title' => $v->purpose ?? 'Home Visit',
                'date' => $v->schedule?->timezone('Asia/Manila')->format('F j, Y'),
                'category' => 'Home Visits',
                '_sort' => $v->schedule,
            ];
        }

        // Sort by date descending (latest first) using Carbon dates, take top 7
        usort($recentItems, fn ($a, $b) => $b['_sort'] <=> $a['_sort']);
        $recentActivities = collect(array_slice($recentItems, 0, 7))
            ->map(fn ($item) => ['title' => $item['title'], 'date' => $item['date'], 'category' => $item['category']])
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => [
                'program_type' => $programType,
                'selected_program' => $category,
                'metrics' => $metrics,
                'recent_activities' => $recentActivities,
            ],
        ], 200);
    }

    /**
     * Get dashboard summary (quick stats)
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $beneficiary = $user->beneficiary;

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary not found',
            ], 404);
        }

        $currentYear = Carbon::now()->year;

        $totalActivities = Attendance::where('beneficiary_id', $beneficiary->id)
            ->whereYear('created_at', $currentYear)
            ->distinct('activity_session_id')
            ->count('activity_session_id');

        $totalSessions = ActivitySession::whereYear('schedule', $currentYear)
            ->whereHas('activity', fn ($q) => $q->where('is_active', true))
            ->count();

        $attendanceRate = $totalSessions > 0
            ? round((Attendance::where('beneficiary_id', $beneficiary->id)
                ->whereYear('created_at', $currentYear)
                ->whereIn('attendance_status', ['present', 'late'])
                ->count() / $totalSessions) * 100, 2)
            : 0;

        $latestGwa = AcademicRecord::where('beneficiary_id', $beneficiary->id)
            ->latest('created_at')
            ->value('gwa');

        return response()->json([
            'success' => true,
            'data' => [
                'total_activities' => $totalActivities,
                'attendance_rate' => $attendanceRate,
                'current_gwa' => $latestGwa ? (string) round($latestGwa, 2) : 'N/A',
            ],
        ], 200);
    }

    private function getEducationMetrics(int $beneficiaryId, Request $request): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        // Attendance per type 
        $eqAttendance = $this->getAttendanceRateByType($beneficiaryId, 'EQ Session', $startDate, $endDate);
        $tutorialAttendance = $this->getAttendanceRateByType($beneficiaryId, 'Tutorial Session', $startDate, $endDate);

        // GWA
        $hasFilter = $request->filled('year') || ($request->filled('date_from') && $request->filled('date_to'));

        $enrollments = EducationEnrollment::where('beneficiary_id', $beneficiaryId);
        if ($hasFilter) {
            $enrollments->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('academic_year_start_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('academic_year_end_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('academic_year_start_date', '<=', $startDate->toDateString())
                         ->where('academic_year_end_date', '>=', $endDate->toDateString());
                  });
            });
        }
        $enrollmentIds = $enrollments->pluck('id');

        if ($enrollmentIds->isEmpty() && !$hasFilter) {
            $enrollmentIds = EducationEnrollment::where('beneficiary_id', $beneficiaryId)
                ->latest('academic_year_start_date')
                ->limit(1)
                ->pluck('id');
        }

        $yearlyGwa = 'N/A';
        if ($enrollmentIds->isNotEmpty()) {
            $gwaValues = AcademicRecord::where('beneficiary_id', $beneficiaryId)
                ->whereIn('education_enrollment_id', $enrollmentIds)
                ->pluck('gwa');

            if ($gwaValues->isNotEmpty()) {
                $yearlyGwa = (string) round($gwaValues->avg(), 2);
            }
        }

        // Socio-Emotional
        $socioPct = $this->getSocioEmotionalPercentage($beneficiaryId, $startDate, $endDate);

        return [
            'eq_attendance' => $eqAttendance,
            'tutorial_attendance' => $tutorialAttendance,
            'gwa' => $yearlyGwa,
            'socio_emotional' => $socioPct !== null ? $this->getSocioEmotionalLevel($socioPct) : 'N/A',
        ];
    }

    private function resolveDateRange(Request $request): array
    {
        if ($request->filled('date_from') && $request->filled('date_to')) {
            return [
                Carbon::parse($request->query('date_from'))->startOfDay(),
                Carbon::parse($request->query('date_to'))->endOfDay(),
            ];
        }

        $year = (int) ($request->query('year', Carbon::now()->year));

        return [
            Carbon::create($year, 1, 1)->startOfDay(),
            Carbon::create($year, 12, 31)->endOfDay(),
        ];
    }

    private function getAttendanceRateByType(int $beneficiaryId, string $activityType, Carbon $startDate, Carbon $endDate): float
    {
        $result = DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->where('attendances.beneficiary_id', $beneficiaryId)
            ->where('activity_types.name', $activityType)
            ->whereBetween(DB::raw('DATE(activity_sessions.schedule)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN attendances.attendance_status = ? THEN 1 ELSE 0 END) as present', ['present'])
            ->first();

        if (!$result || (int) $result->total === 0) {
            return 0;
        }

        return round(((int) $result->present / (int) $result->total) * 100, 2);
    }

    private function getSocioEmotionalPercentage(int $beneficiaryId, Carbon $startDate, Carbon $endDate): ?float
    {
        $result = DB::table('ffa_assessment_records')
            ->join('assessment_categories', 'assessment_categories.id', '=', 'ffa_assessment_records.assessment_category_id')
            ->where('ffa_assessment_records.beneficiary_id', $beneficiaryId)
            ->where('assessment_categories.assessment_name', 'Socio-Emotional')
            ->whereBetween(DB::raw('DATE(ffa_assessment_records.date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('AVG(ffa_assessment_records.score / NULLIF(ffa_assessment_records.max_score, 0) * 100) as avg_pct')
            ->first();

        if (!$result || $result->avg_pct === null) {
            return null;
        }

        return round((float) $result->avg_pct, 2);
    }

    private function getSportsMetrics(int $beneficiaryId, Request $request): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $trainingAttendance = $this->getAttendanceRateByType($beneficiaryId, 'Training Session', $startDate, $endDate);

        $uniqueVisits = (int) DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->where('attendances.beneficiary_id', $beneficiaryId)
            ->where('activity_types.name', 'Training Session')
            ->where('attendances.attendance_status', 'present')
            ->whereBetween(DB::raw('DATE(activity_sessions.schedule)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->distinct('activity_sessions.id')
            ->count('activity_sessions.id');

        return [
            'training_attendance' => $trainingAttendance,
            'unique_visits' => $uniqueVisits,
        ];
    }

    private function applyDateFilter($query, Request $request, string $column): void
    {
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate($column, '>=', $request->query('date_from'))
                  ->whereDate($column, '<=', $request->query('date_to'));
        } elseif ($year = $request->query('year')) {
            $query->whereYear($column, $year);
        }
    }

    private function getSocioEmotionalLevel(float $score): string
    {
        return match (true) {
            $score >= 90 => 'Excellent',
            $score >= 75 => 'Good',
            $score >= 60 => 'Satisfactory',
            $score >= 40 => 'Needs Improvement',
            default => 'Poor',
        };
    }
    
    /**
     * Get beneficiary activities (filtered by registration)
     */
    public function activities(Request $request): JsonResponse
    {
        $user = $request->user();
        $beneficiary = $user->beneficiary;
        
        $activityIds = ActivityParticipant::where('beneficiary_id', $beneficiary->id)
            ->whereNull('exited_at')
            ->pluck('activity_id');
        
        $activities = Activity::with('activitySessions')
            ->whereIn('id', $activityIds)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'activities' => $activities,
        ], 200);
    }
    
    /**
     * Get beneficiary attendance history
     */
    public function attendance(Request $request): JsonResponse
    {
        $user = $request->user();
        $beneficiary = $user->beneficiary;
        
        $attendance = Attendance::where('beneficiary_id', $beneficiary->id)
            ->with(['activitySession.activity'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Attendance $a) => [
                'id' => $a->id,
                'activity_session_id' => $a->activity_session_id,
                'status' => $a->attendance_status,
                'activity_name' => $a->activitySession?->activity?->name ?? 'Unknown',
                'schedule' => $a->activitySession?->schedule?->format('M d, Y h:i A'),
                'created_at' => $a->created_at?->diffForHumans(),
            ]);
        
        return response()->json([
            'success' => true,
            'attendance' => $attendance,
        ], 200);
    }
    
    /**
     * Get notifications for the authenticated user.
     */
    public function notifications(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = $user->notifications();
        
        if ($request->query('filter') === 'unread') {
            $query->whereNull('read_at');
        }
        
        $notifications = $query->latest()->get()->map(fn ($n) => [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->data['title'] ?? match ($n->type) {
                'beneficiary_attendance' => 'ATTENDANCE ALERT',
                'beneficiary_school_attendance' => 'SCHOOL ATTENDANCE ALERT',
                'beneficiary_gwa' => 'GWA ALERT',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                'academic_record_updated' => 'ACADEMIC RECORD UPDATED',
                'ffa_assessment_updated' => 'ASSESSMENT UPDATED',
                'injury_record_updated' => 'INJURY RECORD UPDATED',
                'enrollment_updated' => 'ENROLLMENT UPDATED',
                'competition_result_updated' => 'COMPETITION RESULT UPDATED',
                'home_visit_updated' => 'HOME VISIT UPDATED',
                default => 'NOTIFICATION',
            },
            'message' => $n->data['message'] ?? '',
            'is_read' => $n->read_at !== null,
            'created_at' => $n->created_at?->diffForHumans(),
        ]);
        
        $unreadCount = $user->unreadNotifications()->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ],
        ]);
    }
    
    /**
     * Get unread notification count only (lightweight).
     */
    public function unreadNotificationCount(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => request()->user()->unreadNotifications()->count(),
            ],
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markNotificationRead(string $id): JsonResponse
    {
        $user = request()->user();
        $notification = $user->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead(): JsonResponse
    {
        request()->user()->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Delete a notification.
     */
    public function deleteNotification(string $id): JsonResponse
    {
        $user = request()->user();
        $notification = $user->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }
        
        $notification->delete();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Get academic records
     */
    public function academicRecords(Request $request): JsonResponse
    {
        $user = $request->user();
        $beneficiary = $user->beneficiary;
        
        $records = $beneficiary->academicRecords()
            ->with('subjects')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'academic_records' => $records,
        ], 200);
    }
    
    /**
     * Get beneficiary status history
     */
    public function statusHistory(Beneficiary $beneficiary): JsonResponse
    {
        $beneficiary->load(['statuses.statusType']);

        $statuses = $beneficiary->statuses
            ->sortByDesc(fn($record) => [$record->start_date, $record->id])
            ->values()
            ->map(fn($s) => [
                'id' => $s->id,
                'beneficiary_id' => $s->beneficiary_id,
                'beneficiary_status_type_id' => $s->beneficiary_status_type_id,
                'start_date' => $s->start_date?->format('Y-m-d'),
                'end_date' => $s->end_date?->format('Y-m-d'),
                'status_name' => $s->statusType?->status_name,
            ]);

        return response()->json([
            'success' => true,
            'data' => $statuses,
        ]);
    }

    /**
     * Log attendance via QR code
     */
    public function logAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => ['required', 'string'],
            'activity_id' => ['required', 'exists:activities,id'],
        ]);
        
        $user = $request->user();
        $beneficiary = $user->beneficiary;
        
        // Check if already attended
        $existing = Attendance::where('beneficiary_id', $beneficiary->id)
            ->where('activity_id', $validated['activity_id'])
            ->first();
        
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already logged for this activity',
            ], 400);
        }
        
        $attendance = Attendance::create([
            'beneficiary_id' => $beneficiary->id,
            'activity_id' => $validated['activity_id'],
            'qr_code' => $validated['qr_code'],
            'logged_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Attendance logged successfully',
            'attendance' => $attendance,
        ], 200);
    }
}