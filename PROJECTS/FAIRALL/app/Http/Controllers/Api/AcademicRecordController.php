<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicRecord;
use Illuminate\Http\Request;

class AcademicRecordController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only beneficiaries can view academic records.'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary profile not found.'
                ], 404);
            }
            
            $academicRecords = AcademicRecord::where('beneficiary_id', $beneficiary->id)
                ->with('subjectGrades', 'educationEnrollment') 
                ->orderBy('created_at', 'desc')
                ->get();
            
            $formattedRecords = $academicRecords->map(function ($record) {
                $subjects = $record->subjectGrades->map(function ($subject) {
                    return [
                        'name' => $subject->subject_name ?? 'N/A',
                        'grade' => $subject->grade ?? 0
                    ];
                });

                $grades = $subjects->pluck('grade')->filter(fn($g) => $g !== null && $g !== '');
                $gwa = $grades->isNotEmpty() ? round($grades->avg(), 2) : ($record->gwa ?? 0);

                return [
                    'id' => $record->id,
                    'school_name' => $record->school_name, 
                    'academic_year' => $record->academic_year, 
                    'term' => $record->term ?? 'N/A',
                    'grade_level' => $record->grade_level, 
                    'gwa' => $gwa,
                    'school_attendance' => $record->school_attendance ?? 0, 
                    'subjects' => $subjects,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedRecords,
                'count' => $formattedRecords->count()
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('AcademicRecordController error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch academic records: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only beneficiaries can view academic records.'
                ], 403);
            }
            
            $beneficiary = $user->beneficiary;
            
            if (!$beneficiary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beneficiary profile not found.'
                ], 404);
            }
            
            $academicRecord = AcademicRecord::where('beneficiary_id', $beneficiary->id)
                ->with('subjectGrades', 'educationEnrollment')
                ->where('id', $id)
                ->first();
            
            if (!$academicRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Academic record not found.'
                ], 404);
            }
            
            $subjects = $academicRecord->subjectGrades->map(function ($subject) {
                return [
                    'name' => $subject->subject_name ?? 'N/A',
                    'grade' => $subject->grade ?? 0
                ];
            });

            $grades = $subjects->pluck('grade')->filter(fn($g) => $g !== null && $g !== '');
            $gwa = $grades->isNotEmpty() ? round($grades->avg(), 2) : ($academicRecord->gwa ?? 0);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $academicRecord->id,
                    'school_name' => $academicRecord->school_name,
                    'academic_year' => $academicRecord->academic_year,
                    'term' => $academicRecord->term ?? 'N/A',
                    'grade_level' => $academicRecord->grade_level,
                    'gwa' => $gwa,
                    'school_attendance' => $academicRecord->school_attendance ?? 0,
                    'subjects' => $subjects,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('AcademicRecordController show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch academic record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Optional: Store method for creating academic records with school_attendance
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'beneficiary') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only beneficiaries can create academic records.'
                ], 403);
            }
            
            $validated = $request->validate([
                'education_enrollment_id' => 'required|exists:education_enrollments,id',
                'term' => 'required|string',
                'gwa' => 'nullable|numeric|min:0|max:100',
                'school_attendance' => 'nullable|numeric|min:0|max:100',
            ]);
            
            $beneficiary = $user->beneficiary;

            if (AcademicRecord::where('education_enrollment_id', $validated['education_enrollment_id'])
                ->where('term', $validated['term'])
                ->exists()
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'An academic record for this term already exists for this enrollment.',
                ], 422);
            }
            
            $academicRecord = AcademicRecord::create([
                'beneficiary_id' => $beneficiary->id,
                'education_enrollment_id' => $validated['education_enrollment_id'],
                'term' => $validated['term'],
                'gwa' => $validated['gwa'] ?? null,
                'school_attendance' => $validated['school_attendance'] ?? null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Academic record created successfully',
                'data' => $academicRecord
            ], 201);
            
        } catch (\Exception $e) {
            \Log::error('AcademicRecordController store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create academic record: ' . $e->getMessage()
            ], 500);
        }
    }
}