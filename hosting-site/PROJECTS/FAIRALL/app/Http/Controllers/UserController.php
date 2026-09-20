<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\User;
use App\Services\Reports\ReportRateLimiter;
use App\Services\StaffDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Show user dashboard
     */
    public function dashboard(Request $request, StaffDashboardService $dashboardService, string $section = 'beneficiary'): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            $user->load(['staff.department', 'staff.position', 'donor.deliverables', 'donor.donations', 'beneficiary']);
        }

        if ($user?->user_type === 'donor') {
            $donor = $user->donor;

            $donor?->loadMissing([
                'subscriptions',
                'donations.program',
                'donations.receipts',
            ]);

            $donations = $donor?->donations ?? collect();
            $completedDonations = $donations->where('status', 'completed');

            $donationCount = $donations->count();
            $recentDonations = $donations->sortByDesc('transaction_date')->take(10);

            $activeSubscriptions = $donor?->subscriptions
                ?->where('status', 'active')
                ?->values();

            $givingByProgram = $completedDonations
                ->groupBy(fn ($d) => $d->program_id ?: 0)
                ->map(function ($group) {
                    $program = $group->first()->program;
                    return [
                        'program_name' => $program?->program_name ?? 'Unassigned',
                        'program_id' => $group->first()->program_id,
                        'total' => $group->sum('amount'),
                        'count' => $group->count(),
                    ];
                })->values();

            $yearToDate = $completedDonations->filter(
                fn ($d) => $d->transaction_date?->isCurrentYear()
            );

            $programsSupported = $completedDonations
                ->pluck('program.program_name')
                ->unique()
                ->filter()
                ->values();

            $beneficiariesSupported = 0;
            $donationIds = $completedDonations->pluck('id');

            $supportedBeneficiaries = collect();
            if ($donationIds->isNotEmpty()) {
                $beneficiariesSupported = Allocation::whereIn('donation_id', $donationIds)
                    ->distinct('beneficiary_id')
                    ->count('beneficiary_id');

                $beneficiaryRows = Allocation::whereIn('donation_id', $donationIds)
                    ->with('beneficiary')
                    ->get()
                    ->pluck('beneficiary')
                    ->unique('id');

                $supportedBeneficiaries = $beneficiaryRows->map(fn($b) => trim(
                    $b->first_name . ' ' . substr($b->last_name ?? '', 0, 1) . '.'
                ))->filter()->values();
            }

            $firstDonation = $completedDonations->sortBy('transaction_date')->first();
            $firstDonationDate = $firstDonation?->transaction_date;

            $activeMonths = $completedDonations
                ->pluck('transaction_date')
                ->filter()
                ->map(fn($d) => $d->format('Y-m'))
                ->unique()
                ->sort()
                ->values();

            $currentStreak = 0;
            $streakMonths = count($activeMonths);
            for ($i = $streakMonths - 1; $i >= 0; $i--) {
                $expected = now()->subMonths($streakMonths - 1 - $i)->format('Y-m');
                if ($activeMonths[$i] === $expected) {
                    $currentStreak++;
                } else {
                    break;
                }
            }

            $totalCompletedAmount = $completedDonations->sum('amount');
            $totalCompletedCount = $completedDonations->count();

            $milestones = [];
            $countThresholds = [10, 25, 50, 100];
            foreach ($countThresholds as $t) {
                if ($totalCompletedCount >= $t) {
                    $milestones[] = $t === 100 ? '100th Donation' : $t . 'th Donation';
                }
            }
            $amountThresholds = [50000, 100000, 250000, 500000, 1000000];
            $amountLabels = ['₱50k Total', '₱100k Total', '₱250k Total', '₱500k Total', '₱1M Total'];
            foreach ($amountThresholds as $i => $t) {
                if ($totalCompletedAmount >= $t) {
                    $milestones[] = $amountLabels[$i];
                }
            }
            if ($firstDonationDate && $firstDonationDate->diffInYears(now()) >= 1) {
                $years = (int) $firstDonationDate->diffInYears(now());
                $milestones[] = $years . ' Year' . ($years > 1 ? 's' : '') . ' Giving';
            }
            $milestones = array_unique($milestones);

            return view('donors.dashboard', [
                'name' => $user->display_name,
                'donor' => $donor,
                'donationCount' => $donationCount,
                'recentDonations' => $recentDonations,
                'activeSubscriptions' => $activeSubscriptions,
                'givingByProgram' => $givingByProgram,
                'yearToDateTotal' => $yearToDate->sum('amount'),
                'yearToDateCount' => $yearToDate->count(),
                'programsSupported' => $programsSupported,
                'beneficiariesSupported' => $beneficiariesSupported,
                'firstDonationDate' => $firstDonationDate,
                'supportedBeneficiaries' => $supportedBeneficiaries,
                'currentStreak' => $currentStreak,
                'milestones' => $milestones,
                'totalCompletedAmount' => $totalCompletedAmount,
                'totalCompletedCount' => $totalCompletedCount,
            ]);
        }

        $payload = $dashboardService->build($user, $request->all(), $section);
        $allowedSections = array_keys(array_filter($payload['sectionVisibility'] ?? []));
        $requestedSection = in_array($section, ['beneficiary', 'activity', 'donor'], true) ? $section : 'beneficiary';

        if (!in_array($requestedSection, $allowedSections, true)) {
            $requestedSection = $allowedSections[0] ?? 'beneficiary';
        }

        $payload['activeSection'] = $requestedSection;

        // Report generation quota (5 per 24h sliding window, only for eligible staff)
        if ($user && $user->staff && \Illuminate\Support\Facades\Gate::allows('manage-reports')) {
            $payload['reportQuota'] = ReportRateLimiter::quotaFor($user);
        } else {
            $payload['reportQuota'] = null;
        }

        return view('dashboard', $payload);
    }

    /**
     * Show user profile
     */
    public function showProfile(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load(['staff', 'beneficiary', 'donor']);

        return view('profile.show', [
            'user' => $user,
            'displayName' => $user->staff?->display_name ?? $user->beneficiary?->full_name ?? $user->donor?->full_name ?? $user->email,
        ]);
    }

    /**
     * Update profile picture
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'avatar_cropped' => ['nullable', 'string', 'max:15000000'],
        ]);

        if (empty($validated['avatar']) && empty($validated['avatar_cropped'])) {
            return back()->withErrors([
                'avatar' => 'Please choose an image before uploading.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        $oldAvatarPath = $user->avatar_path;
        $directory = "avatars/users/{$user->id}";
        $fileName = Str::uuid()->toString() . '.webp';
        $storagePath = $directory . '/' . $fileName;

        try {
            $sourceBinary = $this->extractAvatarBinary($request, $validated);
            $normalizedBinary = $this->normalizeAvatarImage($sourceBinary);

            Log::info('Uploading user avatar', [
                'user_id' => $user->id,
                'path' => $storagePath,
                'bytes' => strlen($normalizedBinary),
            ]);

            $stored = Storage::disk('s3')->put($storagePath, $normalizedBinary, 'private');

            Log::info('S3 avatar upload returned', ['stored' => $stored, 'storedPath' => $storagePath]);

            if (! $stored) {
                Log::error('S3 avatar upload returned falsy result', ['user_id' => $user->id]);
                return back()->withErrors([
                    'avatar' => 'Unable to upload your profile picture. Please try again.',
                ]);
            }

            $user->forceFill([
                'avatar_path' => $storagePath,
            ])->save();

            if ($oldAvatarPath && $oldAvatarPath !== $storagePath) {
                Storage::disk('s3')->delete($oldAvatarPath);
            }
        } catch (\Throwable $throwable) {
            Log::error('Avatar upload failed', ['user_id' => $user->id, 'error' => $throwable->getMessage()]);

            if (isset($storagePath) && $storagePath) {
                Storage::disk('s3')->delete($storagePath);
            }

            return back()->withErrors([
                'avatar' => 'Unable to upload your profile picture. Please try again.',
            ]);
        }

        return redirect('/profile')->with('status', 'Profile picture updated successfully.');
    }

    /**
     * Remove profile picture
     */
    public function removeAvatar(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->avatar_path) {
            Storage::disk('s3')->delete($user->avatar_path);
            $user->forceFill(['avatar_path' => null])->save();
        }

        return redirect('/profile')->with('status', 'Profile picture removed successfully.');
    }

    /**
     * Show email edit form
     */
    public function editEmail(): View
    {
        return view('profile.edit-email', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update user email
     */
    public function updateEmail(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ], [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
        ]);

        // If email changed, mark as unverified and send verification email
        if ($user->email !== $validated['email']) {
            $user->update([
                'email' => $validated['email'],
                'email_verified_at' => null,  // Reset verification
            ]);

            $user->sendEmailVerificationNotification();

            return redirect('/profile')
                ->with('status', 'Email updated successfully. Please verify your new email address.');
        }

        return redirect('/profile')
            ->with('status', 'Email is the same as before.');
    }

    /**
     * Show password edit form
     */
    public function editPassword(): View
    {
        return view('profile.edit-password');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.',
            ]);
        }

        if (Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Your new password must be different from your current password.',
            ]);
        }

        // Update password
        $user->update([
            'password' => $validated['password'],
            'password_changed_at' => now(),
        ]);

        return redirect('/profile')
            ->with('status', 'Password updated successfully.');
    }

    /**
     * Show forced password change form (first sign-in)
     */
    public function showForcePasswordChange(): View
    {
        return view('auth.force-password-change');
    }

    /**
     * Handle forced password change (first sign-in)
     */
    public function forcePasswordChange(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Your new password must be different from your current password.',
            ]);
        }

        $user->update([
            'password' => $validated['password'],
            'password_changed_at' => now(),
        ]);

        return redirect('/dashboard')
            ->with('status', 'Password changed successfully.');
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if email is already verified
        if ($user->email_verified_at) {
            return redirect('/profile')
                ->with('status', 'Your email is already verified.');
        }

        // Send verification email
        $user->sendEmailVerificationNotification();

        return redirect('/profile')
            ->with('status', 'Verification email sent. Please check your inbox.');
    }

    private function extractAvatarBinary(Request $request, array $validated): string
    {
        if (!empty($validated['avatar_cropped'])) {
            $dataUrl = (string) $validated['avatar_cropped'];

            if (!preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/', $dataUrl)) {
                throw new \RuntimeException('Invalid cropped avatar format.');
            }

            $payload = substr($dataUrl, strpos($dataUrl, ',') + 1);
            $decoded = base64_decode(str_replace(' ', '+', $payload), true);

            if ($decoded === false || $decoded === '') {
                throw new \RuntimeException('Failed to decode cropped avatar image.');
            }

            return $decoded;
        }

        $file = $request->file('avatar');

        if (!$file) {
            throw new \RuntimeException('No avatar file was provided.');
        }

        $content = $file->getContent();

        if ($content === false || $content === '') {
            throw new \RuntimeException('Failed to read avatar file content.');
        }

        return $content;
    }

    private function normalizeAvatarImage(string $sourceBinary): string
    {
        $source = imagecreatefromstring($sourceBinary);

        if ($source === false) {
            throw new \RuntimeException('Invalid image data.');
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $sourceSquare = min($sourceWidth, $sourceHeight);
        $sourceX = (int) floor(($sourceWidth - $sourceSquare) / 2);
        $sourceY = (int) floor(($sourceHeight - $sourceSquare) / 2);

        $targetSize = 512;
        $target = imagecreatetruecolor($targetSize, $targetSize);

        if ($target === false) {
            imagedestroy($source);
            throw new \RuntimeException('Failed to initialize image canvas.');
        }

        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetSize, $targetSize, $transparent);

        $resampled = imagecopyresampled(
            $target,
            $source,
            0,
            0,
            $sourceX,
            $sourceY,
            $targetSize,
            $targetSize,
            $sourceSquare,
            $sourceSquare
        );

        if (!$resampled) {
            imagedestroy($target);
            imagedestroy($source);
            throw new \RuntimeException('Failed to process avatar image.');
        }

        ob_start();
        $encoded = imagewebp($target, null, 82);
        $binary = ob_get_clean();

        imagedestroy($target);
        imagedestroy($source);

        if (!$encoded || !is_string($binary) || $binary === '') {
            throw new \RuntimeException('Failed to encode avatar image.');
        }

        return $binary;
    }
}
