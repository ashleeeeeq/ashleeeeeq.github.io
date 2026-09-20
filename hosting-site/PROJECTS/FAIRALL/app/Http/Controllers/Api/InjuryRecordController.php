<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InjuryRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InjuryRecordController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only beneficiaries can view injury records.'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary profile not found.'
                ], 404);
            }
            
            $injuries = InjuryRecord::where('beneficiary_id', $beneficiary->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $formattedInjuries = $injuries->map(function ($injury) {
                // Format dates
                $recoveryStartDate = null;
                $recoveryEndDate = null;
                $formattedStartDate = null;
                $formattedEndDate = null;
                
                if ($injury->recovery_start_date) {
                    try {
                        $startDate = Carbon::parse($injury->recovery_start_date);
                        $recoveryStartDate = $startDate->format('Y-m-d');
                        $formattedStartDate = $startDate->format('M d, Y');
                    } catch (\Exception $e) {
                        $recoveryStartDate = $injury->recovery_start_date;
                        $formattedStartDate = $injury->recovery_start_date;
                    }
                }
                
                if ($injury->recovery_end_date) {
                    try {
                        $endDate = Carbon::parse($injury->recovery_end_date);
                        $recoveryEndDate = $endDate->format('Y-m-d');
                        $formattedEndDate = $endDate->format('M d, Y');
                    } catch (\Exception $e) {
                        $recoveryEndDate = $injury->recovery_end_date;
                        $formattedEndDate = $injury->recovery_end_date;
                    }
                }
                
                // Format severity with proper capitalization
                $severity = $injury->severity ?? 'minor';
                $severityFormats = [
                    'minor' => 'Minor',
                    'moderate' => 'Moderate',
                    'major' => 'Major',
                    'severe' => 'Severe',
                ];
                $formattedSeverity = $severityFormats[strtolower($severity)] ?? ucfirst($severity);
                
                // Format status with proper capitalization
                $status = $injury->status ?? 'active';
                $statusFormats = [
                    'active' => 'Active',
                    'recovering' => 'Recovering',
                    'recovered' => 'Recovered',
                    'closed' => 'Closed',
                ];
                $formattedStatus = $statusFormats[strtolower($status)] ?? ucfirst($status);
                
                // Format injury type
                $injuryType = $injury->injury_type ?? 'N/A';
                $formattedInjuryType = ucwords(str_replace('_', ' ', $injuryType));
                
                // Format body part
                $bodyPart = $injury->body_part ?? 'N/A';
                $formattedBodyPart = ucwords(str_replace('_', ' ', $bodyPart));
                
                return [
                    'id' => $injury->id,
                    'injury_type' => $injuryType,
                    'formatted_injury_type' => $formattedInjuryType,
                    'severity' => $severity,
                    'formatted_severity' => $formattedSeverity,
                    'body_part' => $bodyPart,
                    'formatted_body_part' => $formattedBodyPart,
                    'status' => $status,
                    'formatted_status' => $formattedStatus,
                    'recovery_start_date' => $recoveryStartDate,
                    'formatted_start_date' => $formattedStartDate,
                    'recovery_end_date' => $recoveryEndDate,
                    'formatted_end_date' => $formattedEndDate,
                    'remarks' => $injury->remarks ?? '',
                    'created_at' => $injury->created_at ? Carbon::parse($injury->created_at)->format('M d, Y') : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedInjuries,
                'count' => $formattedInjuries->count()
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('InjuryRecordController error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch injury records: ' . $e->getMessage()
            ], 500);
        }
    }
}