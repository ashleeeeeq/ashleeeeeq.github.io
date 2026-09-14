<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FfaAssessmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only beneficiaries can view FFA assessments.'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary profile not found.'
                ], 404);
            }
            
            $assessments = DB::table('ffa_assessment_records')
                ->join('assessment_categories', 'ffa_assessment_records.assessment_category_id', '=', 'assessment_categories.id')
                ->where('ffa_assessment_records.beneficiary_id', $beneficiary->id)
                ->select(
                    'ffa_assessment_records.id',
                    'ffa_assessment_records.name',
                    'ffa_assessment_records.score',
                    'ffa_assessment_records.max_score',
                    'ffa_assessment_records.date',
                    'ffa_assessment_records.remarks',
                    'assessment_categories.assessment_name as category'
                )
                ->orderBy('ffa_assessment_records.date', 'desc')
                ->get();
            
            $formattedAssessments = $assessments->map(function ($assessment) {
                return [
                    'id' => $assessment->id,
                    'name' => $assessment->name ?? 'Assessment',
                    'category' => $assessment->category ?? 'General',
                    'score' => floatval($assessment->score ?? 0),
                    'max_score' => floatval($assessment->max_score ?? 100),
                    'date' => $assessment->date ? date('Y-m-d', strtotime($assessment->date)) : null,
                    'remarks' => $assessment->remarks ?? '',
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedAssessments,
                'count' => $formattedAssessments->count()
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('FfaAssessmentController error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch FFA assessments: ' . $e->getMessage()
            ], 500);
        }
    }
}