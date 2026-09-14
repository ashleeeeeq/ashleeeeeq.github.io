<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function logAttendance(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'beneficiary_id' => 'required|exists:beneficiaries,id',
        ]);

        $event = Event::where('qr_token', $request->qr_code)->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code'
            ], 404);
        }

        // Check for duplicate attendance
        $existing = Attendance::where('beneficiary_id', $request->beneficiary_id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded'
            ], 400);
        }

        $attendance = Attendance::create([
            'beneficiary_id' => $request->beneficiary_id,
            'event_id' => $event->id,
            'attendance_status' => 'present',
            'attendance_method' => 'QR',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully',
            'attendance' => $attendance,
        ], 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            $beneficiary = $user?->beneficiary;

            $event = Event::with(['program', 'eventType'])->find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            $hasAttendance = $beneficiary
                ? Attendance::where('beneficiary_id', $beneficiary->id)
                    ->where('event_id', $id)
                    ->exists()
                : false;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $event->id,
                    'title' => $event->name,
                    'description' => $event->description,
                    'category' => 'Events',
                    'program' => $event->program?->program_name ?? 'N/A',
                    'location' => $event->location,
                    'start' => $event->start?->format('M d, Y h:i A'),
                    'end' => $event->end?->format('M d, Y h:i A'),
                    'is_registered' => $hasAttendance,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Event show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load event details'
            ], 500);
        }
    }

    public function getHistory(Request $request, $beneficiaryId = null)
    {
        $beneficiaryId = $beneficiaryId ?? $request->beneficiary_id;

        if (!$beneficiaryId && $request->user() && $request->user()->beneficiary) {
            $beneficiaryId = $request->user()->beneficiary->id;
        }

        if (!$beneficiaryId) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary ID required'
            ], 400);
        }

        $attendance = Attendance::where('beneficiary_id', $beneficiaryId)
            ->whereNotNull('event_id')
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
        ]);
    }
}