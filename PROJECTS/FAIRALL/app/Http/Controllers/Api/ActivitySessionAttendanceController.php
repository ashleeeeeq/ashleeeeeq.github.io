<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ActivitySession;
use App\Models\ActivityParticipant;
use Illuminate\Http\Request;

class ActivitySessionAttendanceController extends Controller
{
    public function logAttendance(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'beneficiary_id' => 'required|exists:beneficiaries,id',
        ]);

        $activitySession = ActivitySession::where('qr_token', $request->qr_code)->first();

        if (!$activitySession) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code'
            ], 404);
        }

        // Check if beneficiary is registered for this activity
        $isRegistered = ActivityParticipant::where('activity_id', $activitySession->activity_id)
            ->where('beneficiary_id', $request->beneficiary_id)
            ->whereNull('exited_at')
            ->exists();

        if (!$isRegistered) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered for this activity'
            ], 403);
        }

        // Check for duplicate
        $existing = Attendance::where('beneficiary_id', $request->beneficiary_id)
            ->where('activity_session_id', $activitySession->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded'
            ], 400);
        }

        $attendance = Attendance::create([
            'beneficiary_id' => $request->beneficiary_id,
            'activity_session_id' => $activitySession->id,
            'attendance_status' => 'present',
            'attendance_method' => 'QR',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully',
            'attendance' => $attendance,
        ], 201);
    }

    public function getHistory(Request $request, $beneficiaryId = null)
    {
        $beneficiaryId = $beneficiaryId ?? $request->beneficiary_id;

        if (!$beneficiaryId && $request->user() && $request->user()->beneficiary) {
            $beneficiaryId = $request->user()->beneficiary->id;
        }

        $attendance = Attendance::where('beneficiary_id', $beneficiaryId)
            ->whereNotNull('activity_session_id')
            ->with('activitySession.activity')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
        ]);
    }
}