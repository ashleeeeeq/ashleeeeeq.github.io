<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get user profile
    */
    public function show(Request $request): JsonResponse
{
    $user = $request->user();
    
    if ($user->user_type === 'beneficiary') {
        $user->load([
            'beneficiary.address',
            'beneficiary.educationEnrollments',
            'beneficiary.ffaAssessmentRecords.assessmentCategory',
            'beneficiary.injuryRecords',
            'beneficiary.programs',
            'beneficiary.statuses.statusType',
        ]);
    } elseif ($user->user_type === 'donor') {
        $user->load('donor');
    }
    
    $response = [
        'success' => true,
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'login_id' => $user->login_id,
            'user_type' => $user->user_type,
            'is_active' => $user->is_active,
            'email_verified_at' => $user->email_verified_at,
            'avatar_path' => $user->avatar_path,
            'beneficiary' => $user->beneficiary,
            'donor' => $user->donor,
        ],
    ];
    
    // Organize records by program type for mobile
    if ($user->user_type === 'beneficiary' && $user->beneficiary) {
        $beneficiary = $user->beneficiary;
        $programNames = $beneficiary->programs->pluck('program_name')->unique()->values();
        $programType = match (true) {
            $programNames->count() >= 2 => 'mixed',
            $programNames->first() === 'Education' => 'education',
            $programNames->first() === 'Sports' => 'sports',
            default => 'none',
        };
        
        $sortedNames = $programNames->sort()->values();
        $programDisplay = match ($programNames->count()) {
            0 => 'None',
            2 => $sortedNames->implode(' & '),
            default => $programNames->first() ?? 'None',
        };
        
        $activeStatuses = $beneficiary->statuses
            ->filter(fn($s) => is_null($s->end_date) || $s->end_date->isToday() || $s->end_date->isFuture())
            ->map(fn($s) => [
                'id' => $s->id,
                'status_name' => $s->statusType?->status_name,
                'program_ids' => $s->statusType?->programs?->pluck('id')?->toArray() ?? [],
                'program_names' => $s->statusType?->programs?->pluck('program_name')?->toArray() ?? [],
                'start_date' => $s->start_date?->toDateString(),
                'end_date' => $s->end_date?->toDateString(),
            ])
            ->values();
        
        $response['program_type'] = $programType;
        $response['program_display'] = $programDisplay;
        $response['active_statuses'] = $activeStatuses;

        if ($user->beneficiary) {
            $beneficiaryArray = $user->beneficiary->toArray();
            $beneficiaryArray['program_display'] = $programDisplay;
            $response['user']['beneficiary'] = $beneficiaryArray;
        }
        $response['records'] = [
            'enrollments' => $beneficiary->educationEnrollments,
            'ffa_assessments' => $beneficiary->ffaAssessmentRecords,
            'injury_records' => $beneficiary->injuryRecords,
        ];
    }
    
    return response()->json($response, 200);
}
    
    /**
     * Update user profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $changes = [];
        
        $validated = $request->validate([
            'email' => ['sometimes', 'string', 'email', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
        ]);
        
        // Track email change
        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $user->email = $validated['email'];
            $changes[] = 'Email';
        }
        
        // Track password change
        if (isset($validated['password'])) {
            if (Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your new password must be different from your current password.',
                    'errors' => ['password' => ['Your new password must be different from your current password.']],
                ], 422);
            }

            $user->password = Hash::make($validated['password']);
            $user->password_changed_at = now(); // used by mobile app to enforce first-sign-in password change
            $changes[] = 'Password';
        }
        
        // If no changes were made
        if (empty($changes)) {
            return response()->json([
                'success' => true,
                'message' => 'No changes were made to your profile.',
                'has_changes' => false,
            ], 200);
        }
        
        $user->save();
        
        $changeList = implode(' and ', $changes);
        $message = count($changes) === 1 
            ? "Your {$changeList} has been updated successfully."
            : "Your {$changeList} have been updated successfully.";
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'has_changes' => true,
            'updated_fields' => $changes,
        ], 200);
    }
    
    /**
     * Update beneficiary profile
     */
    public function updateBeneficiary(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->user_type !== 'beneficiary') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only beneficiaries can update this profile.',
            ], 403);
        }
        
        $beneficiary = $user->beneficiary;
        
        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficiary profile not found.',
            ], 404);
        }
        
        $validated = $request->validate([
            'contact_number_code' => ['sometimes', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['sometimes', 'regex:/^[0-9]{6,15}$/'],
            'address' => ['sometimes', 'string'],
        ]);

        $changes = [];

        // Track contact number change
        if (isset($validated['contact_number']) && $validated['contact_number'] !== $beneficiary->contact_number) {
            $beneficiary->contact_number = $validated['contact_number'];
            $beneficiary->dial_code = $validated['contact_number_code'] ?? $beneficiary->dial_code ?? '+63';
            $changes[] = 'Contact Number';
        }
        
        // Track address change
        if (isset($validated['address'])) {
            $address = $beneficiary->address;
            if ($address) {
                if ($validated['address'] !== $address->address_line) {
                    $address->address_line = $validated['address'];
                    $address->save();
                    $changes[] = 'Address';
                }
            } else {
                $beneficiary->address()->create([
                    'address_line' => $validated['address'],
                    'city' => '',
                    'province' => '',
                    'zip' => '',
                    'country' => '',
                ]);
                $changes[] = 'Address';
            }
        }
        
        // If no changes were made
        if (empty($changes)) {
            return response()->json([
                'success' => true,
                'message' => 'No changes were made to your beneficiary profile.',
                'has_changes' => false,
            ], 200);
        }
        
        $beneficiary->save();
        
        $changeList = implode(', ', $changes);
        $isPlural = count($changes) > 1;
        $message = $isPlural 
            ? "Your {$changeList} have been updated successfully."
            : "Your {$changeList} has been updated successfully.";
        
        // Reload to get updated full_address accessor
        $beneficiary->load('address');
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'has_changes' => true,
            'updated_fields' => $changes,
            'beneficiary' => [
                'id' => $beneficiary->id,
                'full_name' => $beneficiary->full_name,
                'contact_number' => $beneficiary->contact_number,
                'grade_level' => $beneficiary->grade_level,
                'with_disability' => $beneficiary->with_disability,
                'full_address' => $beneficiary->full_address,
            ],
        ], 200);
    }
    
    /**
     * Update donor profile
     */
    public function updateDonor(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->user_type !== 'donor') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only donors can update this profile.',
            ], 403);
        }
        
        $donor = $user->donor;
        
        if (!$donor) {
            return response()->json([
                'success' => false,
                'message' => 'Donor profile not found.',
            ], 404);
        }
        
        $validated = $request->validate([
            'contact_number_code' => ['sometimes', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['sometimes', 'regex:/^[0-9]{6,15}$/'],
            'organization_name' => ['sometimes', 'string', 'max:100'],
        ]);

        $changes = [];

        // Track contact number change
        if (isset($validated['contact_number']) && $validated['contact_number'] !== $donor->contact_number) {
            $donor->contact_number = $validated['contact_number'];
            $donor->dial_code = $validated['contact_number_code'] ?? $donor->dial_code ?? '+63';
            $changes[] = 'Contact Number';
        }
        
        // Track organization name change
        if (isset($validated['organization_name']) && $validated['organization_name'] !== $donor->organization_name) {
            $donor->organization_name = $validated['organization_name'];
            $changes[] = 'Organization Name';
        }
        
        // If no changes were made
        if (empty($changes)) {
            return response()->json([
                'success' => true,
                'message' => 'No changes were made to your donor profile.',
                'has_changes' => false,
            ], 200);
        }
        
        $donor->save();
        
        $changeList = implode(' and ', $changes);
        $message = count($changes) === 1 
            ? "Your {$changeList} has been updated successfully."
            : "Your {$changeList} have been updated successfully.";
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'has_changes' => true,
            'updated_fields' => $changes,
            'donor' => $donor,
        ], 200);
    }

    /**
     * Upload profile avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            
            // Delete old avatar if exists
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            
            $user->avatar_path = $path;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'avatar_url' => Storage::url($path),
            ], 200);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No file uploaded',
        ], 400);
    }

    /**
     * Delete profile avatar
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }
        
        $user->avatar_path = null;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Profile picture removed successfully',
        ], 200);
    }
    
    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }
        
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'You have been successfully logged out.',
        ], 200);
    }
}