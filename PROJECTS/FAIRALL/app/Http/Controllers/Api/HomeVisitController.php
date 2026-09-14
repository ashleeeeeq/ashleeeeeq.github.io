<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeVisit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class HomeVisitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401);
            }
            
            if ($user->user_type !== 'beneficiary') {
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
            
            $homeVisits = HomeVisit::with('assignedStaff', 'creator')
                ->where('beneficiary_id', $beneficiary->id)
                ->orderBy('schedule', 'desc')
                ->get();
            
            // Set Manila timezone
            $manilaTimezone = new \DateTimeZone('Asia/Manila');
            
            // Format the data 
            $formattedVisits = $homeVisits->map(function ($visit) use ($manilaTimezone) {
                // Parse the schedule date
                $schedule = $visit->schedule;
                $formattedDate = null;
                $formattedTime = null;
                
                if ($schedule) {
                    try {
                        // Set to Manila timezone
                        $dateTime = new \DateTime($schedule, $manilaTimezone);
                        
                        $formattedDate = $dateTime->format('m/d/Y'); // Format date as m/d/Y
                        $formattedTime = $dateTime->format('g:i A'); // Format time as 12-hour format with AM/PM
                    } catch (\Exception $e) {
                        $formattedDate = $schedule;
                        $formattedTime = $schedule;
                    }
                }
                
                // Format for display
                return [
                    'id' => $visit->id,
                    'beneficiary_id' => $visit->beneficiary_id,
                    'visit_type' => ucfirst($visit->visit_type), 
                    'purpose' => $visit->purpose,
                    'notes' => $visit->notes,
                    'schedule' => $schedule, 
                    'formatted_date' => $formattedDate, 
                    'formatted_time' => $formattedTime, 
                    'assigned_staff_id' => $visit->assigned_staff_id,
                    'assigned_staff_name' => $visit->assignedStaff
                        ? trim("{$visit->assignedStaff->first_name} {$visit->assignedStaff->last_name}")
                        : ($visit->creator
                            ? trim("{$visit->creator->first_name} {$visit->creator->last_name}")
                            : 'Staff'),
                    'created_by' => $visit->created_by,
                    'updated_by' => $visit->updated_by,
                    'created_at' => $visit->created_at,
                    'updated_at' => $visit->updated_at,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedVisits,
                'count' => $formattedVisits->count()
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('HomeVisit index error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home visits: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Request $request, $id): JsonResponse
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
            
            $homeVisit = HomeVisit::where('beneficiary_id', $beneficiary->id)
                ->where('id', $id)
                ->first();
            
            if (!$homeVisit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Home visit not found'
                ], 404);
            }
            
            // Set Manila timezone
            $manilaTimezone = new \DateTimeZone('Asia/Manila');
            
            // Parse and format the schedule date
            $schedule = $homeVisit->schedule;
            $formattedDate = null;
            $formattedTime = null;
            
            if ($schedule) {
                try {
                    $dateTime = new \DateTime($schedule, $manilaTimezone);
                    $formattedDate = $dateTime->format('m/d/Y');
                    $formattedTime = $dateTime->format('g:i A');
                } catch (\Exception $e) {
                    $formattedDate = $schedule;
                    $formattedTime = $schedule;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $homeVisit->id,
                    'beneficiary_id' => $homeVisit->beneficiary_id,
                    'visit_type' => ucfirst($homeVisit->visit_type),
                    'purpose' => $homeVisit->purpose,
                    'notes' => $homeVisit->notes,
                    'schedule' => $schedule,
                    'formatted_date' => $formattedDate,
                    'formatted_time' => $formattedTime,
                    'assigned_staff_id' => $homeVisit->assigned_staff_id,
                    'created_by' => $homeVisit->created_by,
                    'updated_by' => $homeVisit->updated_by,
                    'created_at' => $homeVisit->created_at,
                    'updated_at' => $homeVisit->updated_at,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('HomeVisit show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home visit: ' . $e->getMessage()
            ], 500);
        }
    }
}