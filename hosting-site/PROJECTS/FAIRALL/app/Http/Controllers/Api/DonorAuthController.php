<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Donor;
use App\Rules\Turnstile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DonorAuthController extends Controller
{
    /**
     * Register a new donor via API
     */
    public function register(Request $request): JsonResponse
    {
        $type = $request->input('donor_type');

        if (!in_array($type, ['individual', 'organization'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid donor type. Must be "individual" or "organization".',
            ], 422);
        }

        // Common validation rules
        $commonRules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'donor_type' => ['required', 'in:individual,organization'],
            'cf-turnstile-response' => ['required', 'string', new Turnstile],
        ];

        // Type-specific validation rules
        $typeRules = $type === 'individual'
            ? [
                'first_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
                'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
                'last_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            ]
            : [
                'organization_name' => ['required', 'string', 'max:100'],
            ];

        try {
            $validated = $request->validate(array_merge($commonRules, $typeRules), [
                'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
                'first_name.not_regex' => 'The first name must not contain numbers.',
                'middle_name.not_regex' => 'The middle name must not contain numbers.',
                'last_name.not_regex' => 'The last name must not contain numbers.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $currentYear = now()->year;

        try {
            $user = DB::transaction(function () use ($validated, $type, $currentYear) {
                // Create user record
                $user = User::create([
                    'login_id' => null,
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'is_active' => true,
                    'user_type' => 'donor',
                    'email_verified_at' => null,
                ]);

                // Create donor record
                $donorData = [
                    'user_id' => $user->id,
                    'contact_number' => $validated['contact_number'],
                    'dial_code' => $validated['contact_number_code'] ?? '+63',
                    'donor_type' => $type,
                ];

                if ($type === 'individual') {
                    $donorData['first_name'] = $validated['first_name'];
                    $donorData['middle_name'] = $validated['middle_name'] ?? null;
                    $donorData['last_name'] = $validated['last_name'];
                } else {
                    $donorData['organization_name'] = $validated['organization_name'];
                }

                $donor = Donor::create($donorData);

                // Generate and set login_id
                $loginId = "DON-{$currentYear}-{$donor->id}";
                $user->update(['login_id' => $loginId]);

                return $user;
            });

            // Generate verification URL
            $verificationUrl = url('/api/verify-email/' . $user->id . '/' . sha1($user->email));

            // Send verification email
            try {
                $name = $type === 'individual' 
                    ? ($validated['first_name'] . ' ' . $validated['last_name'])
                    : $validated['organization_name'];
                
                $organizationName = $type === 'organization' ? $validated['organization_name'] : null;

                \Illuminate\Support\Facades\Mail::send(
                    'emails.donor-verification',
                    [
                        'name' => $name,
                        'email' => $user->email,
                        'donorType' => $type,
                        'organizationName' => $organizationName,
                        'verificationUrl' => $verificationUrl,
                    ],
                    function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Welcome to FAIRALL - Verify Your Donor Account');
                    }
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send verification email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please check your email to verify your account.',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'login_id' => $user->login_id,
                    'user_type' => $user->user_type,
                    'donor_type' => $type,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}