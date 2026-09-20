<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\User;
use App\Models\Staff;
use App\Notifications\DonorRegisteredNotification;
use App\Rules\Turnstile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class DonorRegistrationController extends Controller
{
    /**
     * Show donor type selector
     */
    public function create(): View
    {
        return view('auth.donor-type-selector');
    }

    /**
     * Show registration form for selected type
     */
    public function showForm(string $type): View
    {
        if (!in_array($type, ['individual', 'organization'], true)) {
            abort(404, 'Invalid donor type');
        }

        return view('auth.donor-register-' . $type, [
            'type' => $type,
        ]);
    }

    /**
     * Store new donor registration
     */
    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('donor_type');
        $existingUser = $this->findClaimableProvisionalDonor($request->input('email'));
        $claimingProvisional = $existingUser !== null;

        if (!in_array($type, ['individual', 'organization'], true)) {
            abort(422, 'Invalid donor type');
        }

        // Common validation rules
        $commonRules = [
            'email' => $claimingProvisional
                ? ['required', 'string', 'email', 'max:255']
                : ['required', 'string', 'email', 'max:255', 'unique:users,email'],
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

        $validated = $request->validate(array_merge($commonRules, $typeRules), [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
        ]);

        $currentYear = now()->year;

        try {
            DB::transaction(function () use ($validated, $type, $currentYear, $existingUser, $claimingProvisional): void {
                if ($claimingProvisional) {
                    $user = $existingUser->refresh();
                    $donor = $user->donor()->firstOrFail();

                    $user->update([
                        'password' => $validated['password'],
                        'is_active' => true,
                        'user_type' => 'donor',
                    ]);

                    $donor->update($this->buildDonorData($validated, $type));

                    if (! $user->login_id) {
                        $user->update([
                            'login_id' => "DON-{$currentYear}-{$donor->id}",
                        ]);
                    }
                } else {
                    $user = User::create([
                        'login_id' => null,  // Will be set after donor is created
                        'email' => $validated['email'],
                        'password' => $validated['password'],
                        'is_active' => true,
                        'user_type' => 'donor',
                        'email_verified_at' => null,  // Pending verification
                    ]);

                    $donor = Donor::create(array_merge([
                        'user_id' => $user->id,
                    ], $this->buildDonorData($validated, $type)));

                    // Generate and set login_id now that we have the donor id
                    $loginId = "DON-{$currentYear}-{$donor->id}";
                    $user->update(['login_id' => $loginId]);
                }

                // Send email verification notification
                $user->sendEmailVerificationNotification();

                DB::afterCommit(function () use ($donor): void {
                    $this->notifyDonorRegistrationStaff($donor);
                });
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle race condition: duplicate email from concurrent submission
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'users_email_unique')) {
                return back()->withErrors(['email' => 'This email is already taken.'])->withInput();
            }

            throw $e;
        }

        $status = $claimingProvisional
            ? 'Your provisional donor account has been updated. Please verify your email before logging in.'
            : 'Registration successful! Please verify your email before logging in.';

        return redirect('/login')->with('status', $status);
    }

    private function findClaimableProvisionalDonor(?string $email): ?User
    {
        if (! is_string($email) || trim($email) === '') {
            return null;
        }

        $user = User::query()
            ->with('donor')
            ->where('email', trim($email))
            ->first();

        if (! $user || $user->user_type !== 'donor' || ! $user->donor) {
            return null;
        }

        if ($user->login_id !== null || $user->email_verified_at !== null) {
            return null;
        }

        return $user;
    }

    private function buildDonorData(array $validated, string $type): array
    {
        $donorData = [
            'contact_number' => $validated['contact_number'],
            'dial_code' => $validated['contact_number_code'] ?? '+63',
            'donor_type' => $type,
        ];

        if ($type === 'individual') {
            $donorData['first_name'] = $validated['first_name'];
            $donorData['middle_name'] = $validated['middle_name'] ?? null;
            $donorData['last_name'] = $validated['last_name'];
            $donorData['organization_name'] = null;
        } else {
            $donorData['organization_name'] = $validated['organization_name'];
            $donorData['first_name'] = null;
            $donorData['middle_name'] = null;
            $donorData['last_name'] = null;
        }

        return $donorData;
    }

    protected function notifyDonorRegistrationStaff(Donor $donor): void
    {
        $recipients = User::query()
            ->where('user_type', 'staff')
            ->whereHas('staff', function ($query): void {
                $query->whereIn('role', [
                    Staff::ROLE_ADMINISTRATOR,
                    Staff::ROLE_EXECUTIVE_DIRECTOR,
                    Staff::ROLE_DONOR_MANAGER,
                    Staff::ROLE_ADMIN_FINANCE_STAFF,
                ]);
            })
            ->with('staff')
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new DonorRegisteredNotification([
                'donor_id' => $donor->id,
                'message' => sprintf('A new donor has registered: %s', $donor->display_name),
                'action_url' => route('donors.show', $donor->id),
                'meta' => [
                    'donor_type' => $donor->donor_type,
                    'donor_name' => $donor->display_name,
                    'donor_email' => $donor->user?->email,
                ],
            ]));
        }
    }
}