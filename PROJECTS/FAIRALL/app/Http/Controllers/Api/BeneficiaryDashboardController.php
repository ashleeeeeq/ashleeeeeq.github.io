<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\AcademicRecord;
use App\Models\Attendance;
use App\Models\ActivitySession;
use App\Models\Competition;
use App\Models\Event;
use App\Models\HomeVisit;
use App\Models\FFAAssessment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BeneficiaryDashboardController extends Controller
{
    /**
     * Get beneficiary dashboard metrics
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only beneficiaries can access this endpoint.'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary profile not found.'
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

            // Get filter parameters
            $year = $request->query('year');
            
            // Calculate metrics based on category
            if ($category === 'Education') {
                $metrics = $this->getEducationMetrics($beneficiary, $year);
            } else {
                $metrics = $this->getSportsMetrics($beneficiary, $year);
            }
            
            // Get upcoming activities
            $upcomingActivities = $this->getUpcomingEvents($beneficiary);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'beneficiary' => [
                        'id' => $beneficiary->id,
                        'full_name' => $beneficiary->full_name,
                        'grade_level' => $beneficiary->grade_level,
                    ],
                    'program_type' => $programType,
                    'selected_program' => $category,
                    'metrics' => $metrics,
                    'upcoming_activities' => $upcomingActivities,
                ],
            ], 200);
            
        } catch (\Exception $e) {
            DB::error('BeneficiaryDashboardController error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get Education metrics (EQ Attendance, Tutorial Attendance, GWA)
     */
    private function getEducationMetrics($beneficiary, $year = null): array
    {
        $metrics = [
            'eq_attendance' => 0,
            'tutorial_attendance' => 0,
            'gwa' => 'N/A',
            'socio_emotional' => 'N/A',
        ];
        
        // If year is not specified, use current year
        if (!$year) {
            $year = Carbon::now()->year;
        }
        
        // Calculate EQ Attendance (from activity sessions)
        $eqAttendance = $this->calculateAttendanceRate($beneficiary->id, $year);
        $metrics['eq_attendance'] = $eqAttendance;
        
        // Calculate Tutorial Attendance (academic-related sessions)
        $tutorialAttendance = $this->calculateTutorialAttendance($beneficiary->id, $year);
        $metrics['tutorial_attendance'] = $tutorialAttendance;
        
        // Get GWA from academic records
        $latestAcademicRecord = AcademicRecord::where('beneficiary_id', $beneficiary->id)
            ->whereYear('created_at', $year)
            ->latest('created_at')
            ->first();
        
        if ($latestAcademicRecord && $latestAcademicRecord->gwa) {
            $metrics['gwa'] = number_format($latestAcademicRecord->gwa, 2);
        }
        
        // Get Socio-Emotional assessment (from FFA assessments)
        $latestFFA = FFAAssessment::where('beneficiary_id', $beneficiary->id)
            ->whereYear('created_at', $year)
            ->latest('created_at')
            ->first();
        
        if ($latestFFA && $latestFFA->score) {
            $metrics['socio_emotional'] = $this->getSocioEmotionalLevel($latestFFA->score);
        }
        
        return $metrics;
    }
    
    /**
     * Get Sports metrics (Training Attendance & Unique Visits)
     */
    private function getSportsMetrics($beneficiary, $year = null): array
    {
        if (!$year) {
            $year = Carbon::now()->year;
        }

        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate = Carbon::create($year, 12, 31)->endOfDay();

        $beneficiaryId = (int) $beneficiary->id;

        // Training Attendance rate
        $trainingAttendance = $this->calculateAttendanceRateByType($beneficiaryId, 'Training Session', $startDate, $endDate);

        // Unique Visits (distinct training sessions attended)
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

    /**
     * Calculate attendance rate by activity type
     */
    private function calculateAttendanceRateByType(int $beneficiaryId, string $activityType, Carbon $startDate, Carbon $endDate): float
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
    
    /**
     * Calculate attendance rate for a beneficiary
     */
    private function calculateAttendanceRate($beneficiaryId, $year): float
    {
        $sessions = ActivitySession::whereYear('schedule', $year)
            ->whereHas('activity', function ($query) {
                $query->where('is_active', true);
            })
            ->get();
        
        $totalSessions = $sessions->count();
        
        if ($totalSessions === 0) {
            return 0;
        }
        
        $attendedSessions = Attendance::where('beneficiary_id', $beneficiaryId)
            ->whereYear('created_at', $year)
            ->whereIn('attendance_status', ['present', 'late'])
            ->count();
        
        return round(($attendedSessions / $totalSessions) * 100, 2);
    }
    
    /**
     * Calculate tutorial attendance rate
     */
    private function calculateTutorialAttendance($beneficiaryId, $year): float
    {
        // Get tutorial/education related sessions
        $tutorialSessions = ActivitySession::whereYear('schedule', $year)
            ->whereHas('activity', function ($query) {
                $query->whereHas('program', function ($q) {
                    $q->where('program_name', 'like', '%Education%');
                })->orWhere('name', 'like', '%tutorial%');
            })
            ->get();
        
        $totalSessions = $tutorialSessions->count();
        
        if ($totalSessions === 0) {
            return 0;
        }
        
        $attendedSessions = Attendance::where('beneficiary_id', $beneficiaryId)
            ->whereYear('created_at', $year)
            ->whereIn('attendance_status', ['present', 'late'])
            ->whereIn('activity_session_id', $tutorialSessions->pluck('id'))
            ->count();
        
        return round(($attendedSessions / $totalSessions) * 100, 2);
    }
    
    /**
     * Calculate sports attendance rate
     */
    private function calculateSportsAttendance($beneficiaryId, $year): float
    {
        $sportsSessions = ActivitySession::whereYear('schedule', $year)
            ->whereHas('activity', function ($query) {
                $query->whereHas('sportType')->orWhereHas('program', function ($q) {
                    $q->where('program_name', 'like', '%Sports%');
                });
            })
            ->get();
        
        $totalSessions = $sportsSessions->count();
        
        if ($totalSessions === 0) {
            return 0;
        }
        
        $attendedSessions = Attendance::where('beneficiary_id', $beneficiaryId)
            ->whereYear('created_at', $year)
            ->whereIn('attendance_status', ['present', 'late'])
            ->whereIn('activity_session_id', $sportsSessions->pluck('id'))
            ->count();
        
        return round(($attendedSessions / $totalSessions) * 100, 2);
    }
    
    /**
     * Get sports performance metrics
     */
    private function getSportsPerformance($beneficiaryId, $year)
    {
        // Get FFA assessments for sports category
        $sportsAssessment = FFAAssessment::where('beneficiary_id', $beneficiaryId)
            ->whereYear('created_at', $year)
            ->where('category', 'Sports')
            ->latest('created_at')
            ->first();
        
        if ($sportsAssessment) {
            return [
                'score' => number_format($sportsAssessment->score, 2),
                'level' => $this->getSocioEmotionalLevel($sportsAssessment->score),
            ];
        }
        
        return null;
    }
    
    /**
     * Convert score to socio-emotional level
     */
    private function getSocioEmotionalLevel($score): string
    {
        if ($score >= 90) {
            return 'Excellent';
        } elseif ($score >= 75) {
            return 'Good';
        } elseif ($score >= 60) {
            return 'Satisfactory';
        } elseif ($score >= 40) {
            return 'Needs Improvement';
        } else {
            return 'Poor';
        }
    }
    
    /**
     * Get upcoming activities for the beneficiary (limit 5)
     */
    private function getUpcomingEvents($beneficiary)
    {
        $today = Carbon::today();
        $items = [];
        $programIds = $beneficiary->programs()->pluck('programs.id');

        // 1. Future activity sessions (beneficiary is active participant)
        $sessions = ActivitySession::whereDate('schedule', '>=', $today)
            ->whereHas('activity', function ($q) use ($beneficiary) {
                $q->whereIn('id', function ($sub) use ($beneficiary) {
                    $sub->select('activity_id')
                        ->from('activity_participants')
                        ->where('beneficiary_id', $beneficiary->id)
                        ->whereNull('exited_at');
                });
            })
            ->with('activity')
            ->orderBy('schedule', 'asc')
            ->get()
            ->map(function ($session) {
                return [
                    'title' => $session->activity ? $session->activity->name : 'Activity',
                    'date' => $session->schedule ? $session->schedule->format('M d, Y') : null,
                    'time' => $session->schedule ? $session->schedule->format('g:i A') : null,
                ];
            });
        $items = array_merge($items, $sessions->toArray());

        // 2. Future events (beneficiary has attendance record)
        $eventIds = \App\Models\Attendance::where('beneficiary_id', $beneficiary->id)
            ->whereNotNull('event_id')
            ->pluck('event_id')
            ->unique();
        $events = Event::whereIn('id', $eventIds)
            ->whereDate('start', '>=', $today)
            ->orderBy('start', 'asc')
            ->get()
            ->map(function ($event) {
                return [
                    'title' => $event->name,
                    'date' => $event->start ? $event->start->format('M d, Y') : null,
                    'time' => $event->start ? $event->start->format('g:i A') : null,
                ];
            });
        $items = array_merge($items, $events->toArray());

        // 3. Future competitions (in beneficiary's program)
        if ($programIds->isNotEmpty()) {
            $competitions = Competition::whereIn('program_id', $programIds)
                ->whereDate('start', '>=', $today)
                ->orderBy('start', 'asc')
                ->get()
                ->map(function ($comp) {
                    return [
                        'title' => $comp->name,
                        'date' => $comp->start ? $comp->start->format('M d, Y') : null,
                        'time' => $comp->start ? $comp->start->format('g:i A') : null,
                    ];
                });
            $items = array_merge($items, $competitions->toArray());
        }

        // 4. Future home visits
        $homeVisits = HomeVisit::where('beneficiary_id', $beneficiary->id)
            ->whereDate('schedule', '>=', $today)
            ->orderBy('schedule', 'asc')
            ->get()
            ->map(function ($visit) {
                $type = $visit->visit_type ? ' (' . $visit->visit_type . ')' : '';
                return [
                    'title' => 'Home Visit' . $type,
                    'date' => $visit->schedule ? $visit->schedule->format('M d, Y') : null,
                    'time' => $visit->schedule ? $visit->schedule->format('g:i A') : null,
                ];
            });
        $items = array_merge($items, $homeVisits->toArray());

        usort($items, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));

        return array_slice($items, 0, 5);
    }
    
    /**
     * Get dashboard summary (quick stats)
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary profile not found.'
                ], 404);
            }
            
            $currentYear = Carbon::now()->year;
            
            $totalActivities = Attendance::where('beneficiary_id', $beneficiary->id)
                ->whereYear('created_at', $currentYear)
                ->distinct('activity_session_id')
                ->count('activity_session_id');
            
            $attendanceRate = $this->calculateAttendanceRate($beneficiary->id, $currentYear);
            
            $latestGWA = AcademicRecord::where('beneficiary_id', $beneficiary->id)
                ->latest('created_at')
                ->value('gwa');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_activities' => $totalActivities,
                    'attendance_rate' => round($attendanceRate, 2),
                    'current_gwa' => $latestGWA ? number_format($latestGWA, 2) : 'N/A',
                ],
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard summary: ' . $e->getMessage()
            ], 500);
        }
    }
}