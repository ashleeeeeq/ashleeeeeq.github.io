<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonorProfileController extends Controller
{
    /**
     * Get donor profile details
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'donor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only donors can access this endpoint.'
                ], 403);
            }
            
            $donor = $user->donor;
            
            if (!$donor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donor profile not found.'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $donor->id,
                    'user_id' => $donor->user_id,
                    'donor_type' => $donor->donor_type,
                    'first_name' => $donor->first_name,
                    'middle_name' => $donor->middle_name,
                    'last_name' => $donor->last_name,
                    'organization_name' => $donor->organization_name,
                    'contact_number' => $donor->contact_number,
                    'full_name' => $donor->full_name,
                    'display_name' => $donor->display_name,
                    'created_at' => $donor->created_at ? $donor->created_at->toIso8601String() : null,
                    'updated_at' => $donor->updated_at ? $donor->updated_at->toIso8601String() : null,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            DB::error('DonorProfileController show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch donor profile: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update donor profile
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'donor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only donors can update this profile.'
                ], 403);
            }
            
            $donor = $user->donor;
            
            if (!$donor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donor profile not found.'
                ], 404);
            }
            
            $validated = $request->validate([
                'first_name' => ['sometimes', 'nullable', 'string', 'max:100', 'not_regex:/\d/'],
                'middle_name' => ['sometimes', 'nullable', 'string', 'max:100', 'not_regex:/\d/'],
                'last_name' => ['sometimes', 'nullable', 'string', 'max:100', 'not_regex:/\d/'],
                'organization_name' => ['sometimes', 'nullable', 'string', 'max:200'],
                'contact_number_code' => ['sometimes', 'nullable', 'string', 'starts_with:+', 'max:6'],
                'contact_number' => ['sometimes', 'nullable', 'regex:/^[0-9]{6,15}$/'],
            ], [
                'first_name.not_regex' => 'The first name must not contain numbers.',
                'middle_name.not_regex' => 'The middle name must not contain numbers.',
                'last_name.not_regex' => 'The last name must not contain numbers.',
            ]);
            
            $changes = [];
            $updatedFields = [];
            
            // Track changes for individual donor
            if ($donor->donor_type === 'individual') {
                if (isset($validated['first_name']) && $validated['first_name'] !== $donor->first_name) {
                    $donor->first_name = $validated['first_name'];
                    $changes[] = 'First Name';
                    $updatedFields[] = 'first_name';
                }
                
                if (isset($validated['middle_name']) && $validated['middle_name'] !== $donor->middle_name) {
                    $donor->middle_name = $validated['middle_name'];
                    $changes[] = 'Middle Name';
                    $updatedFields[] = 'middle_name';
                }
                
                if (isset($validated['last_name']) && $validated['last_name'] !== $donor->last_name) {
                    $donor->last_name = $validated['last_name'];
                    $changes[] = 'Last Name';
                    $updatedFields[] = 'last_name';
                }
            }
            
            // Track changes for organization donor
            if ($donor->donor_type === 'organization') {
                if (isset($validated['organization_name']) && $validated['organization_name'] !== $donor->organization_name) {
                    $donor->organization_name = $validated['organization_name'];
                    $changes[] = 'Organization Name';
                    $updatedFields[] = 'organization_name';
                }
            }
            
            // Track contact number change for both types
            if (isset($validated['contact_number']) && $validated['contact_number'] !== $donor->contact_number) {
                $donor->contact_number = $validated['contact_number'];
                $donor->dial_code = $validated['contact_number_code'] ?? $donor->dial_code ?? '+63';
                $changes[] = 'Contact Number';
                $updatedFields[] = 'contact_number';
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
            
            $changeList = implode(', ', $changes);
            $isPlural = count($changes) > 1;
            $message = $isPlural 
                ? "Your {$changeList} have been updated successfully."
                : "Your {$changeList} has been updated successfully.";
            
            // Refresh user data to include updated donor info
            $user->refresh();
            $updatedDonor = $user->donor;
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'has_changes' => true,
                'updated_fields' => $updatedFields,
                'donor' => [
                    'id' => $updatedDonor->id,
                    'user_id' => $updatedDonor->user_id,
                    'donor_type' => $updatedDonor->donor_type,
                    'first_name' => $updatedDonor->first_name,
                    'middle_name' => $updatedDonor->middle_name,
                    'last_name' => $updatedDonor->last_name,
                    'organization_name' => $updatedDonor->organization_name,
                    'contact_number' => $updatedDonor->contact_number,
                    'full_name' => $updatedDonor->full_name,
                    'display_name' => $updatedDonor->display_name,
                ],
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::error('DonorProfileController update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update donor profile: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update donor contact information only
     */
    public function updateContact(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'donor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only donors can update this profile.'
                ], 403);
            }
            
            $donor = $user->donor;
            
            if (!$donor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donor profile not found.'
                ], 404);
            }
            
            $validated = $request->validate([
                'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
                'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            ]);

            $donor->contact_number = $validated['contact_number'];
            $donor->dial_code = $validated['contact_number_code'];
            $donor->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Contact number updated successfully.',
                'data' => [
                    'contact_number' => $donor->contact_number
                ]
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::error('DonorProfileController updateContact error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact number: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get donor donation statistics
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->user_type !== 'donor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only donors can access this endpoint.'
                ], 403);
            }
            
            $donor = $user->donor;
            
            if (!$donor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donor profile not found.'
                ], 404);
            }
            
            $totalDonated = $donor->donations()->where('status', 'completed')->sum('amount');
            $totalDonations = $donor->donations()->where('status', 'completed')->count();
            $activeSubscriptions = $donor->subscriptions()->where('status', 'active')->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_donated' => (float) $totalDonated,
                    'total_donations' => (int) $totalDonations,
                    'active_subscriptions' => (int) $activeSubscriptions,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            DB::error('DonorProfileController stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch donor stats: ' . $e->getMessage()
            ], 500);
        }
    }
}