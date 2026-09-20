<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Get all enrollments for the authenticated beneficiary
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only beneficiaries can view enrollment records.'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary profile not found.'
                ], 404);
            }
            
            $enrollments = EducationEnrollment::where('beneficiary_id', $beneficiary->id)
                ->with('academicRecords')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $formattedEnrollments = $enrollments->map(function ($enrollment) {
                // Format dates
                $startDate = $enrollment->academic_year_start_date;
                $endDate = $enrollment->academic_year_end_date;
                
                // Determine academic year string
                $academicYear = 'N/A';
                if ($startDate && $endDate) {
                    $academicYear = $startDate->format('Y') . '-' . $endDate->format('Y');
                } elseif ($startDate) {
                    $academicYear = $startDate->format('Y');
                } elseif ($endDate) {
                    $academicYear = $endDate->format('Y');
                }
                
                // Format enrollment date from created_at
                $enrollmentDate = $enrollment->created_at ? $enrollment->created_at->format('M d, Y') : null;
                
                // Determine if enrollment is active
                $isActive = $enrollment->enrollment_status === 'active' || $enrollment->enrollment_status === 'enrolled';
                $status = $enrollment->enrollment_status ?? ($isActive ? 'active' : 'inactive');
                
                return [
                    'id' => $enrollment->id,
                    'school_name' => $enrollment->school_name ?? 'N/A',
                    'education_level' => $enrollment->education_level ?? 'N/A',
                    'grade_level' => $enrollment->grade_level ?? 'N/A',
                    'academic_year' => $academicYear,
                    'academic_year_start' => $startDate ? $startDate->format('Y-m-d') : null,
                    'academic_year_end' => $endDate ? $endDate->format('Y-m-d') : null,
                    'enrollment_date' => $enrollmentDate,
                    'status' => $enrollment->enrollment_status ?? 'active',
                    'is_active' => $isActive,
                    'has_academic_records' => $enrollment->academicRecords->count() > 0,
                    'academic_records_count' => $enrollment->academicRecords->count(),
                    'created_at' => $enrollment->created_at ? $enrollment->created_at->toIso8601String() : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedEnrollments,
                'count' => $formattedEnrollments->count()
            ], 200);
            
        } catch (\Exception $e) {
            DB::error('EnrollmentController index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch enrollment records: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a specific enrollment details
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only beneficiaries can view enrollment records.'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary profile not found.'
                ], 404);
            }
            
            $enrollment = EducationEnrollment::where('beneficiary_id', $beneficiary->id)
                ->with('academicRecords')
                ->where('id', $id)
                ->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment record not found.'
                ], 404);
            }
            
            // Format dates
            $startDate = $enrollment->academic_year_start_date;
            $endDate = $enrollment->academic_year_end_date;
            
            // Determine academic year string
            $academicYear = 'N/A';
            if ($startDate && $endDate) {
                $academicYear = $startDate->format('Y') . '-' . $endDate->format('Y');
            } elseif ($startDate) {
                $academicYear = $startDate->format('Y');
            } elseif ($endDate) {
                $academicYear = $endDate->format('Y');
            }
            
            // Format enrollment date from created_at
            $enrollmentDate = $enrollment->created_at ? $enrollment->created_at->format('M d, Y') : null;
            
            // Determine if enrollment is active
            $isActive = $enrollment->enrollment_status === 'active' || $enrollment->enrollment_status === 'enrolled';
            $status = $enrollment->enrollment_status ?? ($isActive ? 'active' : 'inactive');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $enrollment->id,
                    'school_name' => $enrollment->school_name ?? 'N/A',
                    'education_level' => $enrollment->education_level ?? 'N/A',
                    'grade_level' => $enrollment->grade_level ?? 'N/A',
                    'academic_year' => $academicYear,
                    'academic_year_start' => $startDate ? $startDate->format('Y-m-d') : null,
                    'academic_year_end' => $endDate ? $endDate->format('Y-m-d') : null,
                    'enrollment_date' => $enrollmentDate,
                    'status' => $status,
                    'is_active' => $isActive,
                    'has_academic_records' => $enrollment->academicRecords->count() > 0,
                    'academic_records' => $enrollment->academicRecords->map(function ($record) {
                        return [
                            'id' => $record->id,
                            'term' => $record->term,
                            'gwa' => $record->gwa,
                            'school_attendance' => $record->school_attendance,
                        ];
                    }),
                    'created_at' => $enrollment->created_at ? $enrollment->created_at->toIso8601String() : null,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            DB::error('EnrollmentController show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch enrollment details: ' . $e->getMessage()
            ], 500);
        }
    }
}