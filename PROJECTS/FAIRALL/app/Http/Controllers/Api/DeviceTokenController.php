<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceTokenController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'device_type' => 'required|string|in:android,ios,web',
        ]);

        $user = $request->user();

        // Deactivate this token for all users (same device, different user)
        DeviceToken::where('token', $validated['token'])
            ->update(['is_active' => false]);

        // Create or update for the current user
        DeviceToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'token' => $validated['token'],
            ],
            [
                'device_type' => $validated['device_type'],
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token registered',
        ], 201);
    }

    public function revoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        DeviceToken::where('user_id', $request->user()->id)
            ->where('token', $validated['token'])
            ->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Device token revoked',
        ]);
    }
}
