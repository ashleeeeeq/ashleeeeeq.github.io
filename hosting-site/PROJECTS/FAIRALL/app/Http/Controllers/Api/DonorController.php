<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Donation;
use App\Models\Program;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionCancelledNotification;
use App\Notifications\SubscriptionCancelledStaffNotification;
use App\Services\Payments\PayPalPaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Notifications trait is on User model, accessible via request()->user()

class DonorController extends Controller
{
    /**
     * Get donor dashboard data with stats and recent activity.
     *
     * Flutter: Display stats cards + recent donations list.
     * next_billing is omitted when no active subscriptions exist.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;

        $allDonations = Donation::with(['program', 'donor'])
            ->where('donor_id', $donor->id)
            ->latest('transaction_date')
            ->get();

        $completedDonations = $allDonations->where('status', 'completed');

        $yearToDate = $completedDonations->filter(
            fn (Donation $d) => $d->transaction_date?->isCurrentYear()
        );

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

        $stats = [
            'total_donated' => $completedDonations->sum('amount'),
            'total_donations' => $completedDonations->count(),
            'active_subscriptions' => Subscription::where('donor_id', $donor->id)->where('status', 'active')->count(),
            'year_to_date_total' => $yearToDate->sum('amount'),
            'year_to_date_count' => $yearToDate->count(),
            'programs_supported' => $completedDonations->pluck('program_id')->unique()->filter()->count(),
            'beneficiaries_supported' => $beneficiariesSupported,
        ];

        $givingByProgram = $completedDonations
            ->groupBy(fn (Donation $d) => $d->program_id ?: 0)
            ->map(fn ($group) => [
                'program_id' => $group->first()->program_id,
                'program_name' => $group->first()->program?->program_name ?? 'Unassigned',
                'total' => (float) $group->sum('amount'),
                'donation_count' => $group->count(),
            ])
            ->values();

        $maxProgramTotal = $givingByProgram->max('total') ?: 1;
        $givingByProgram = $givingByProgram->map(fn ($p) => array_merge($p, [
            'progress' => $maxProgramTotal > 0 ? round($p['total'] / $maxProgramTotal, 4) : 0,
        ]));

        $recentDonations = $allDonations->take(5)->map(fn (Donation $d) => [
            'id' => $d->id,
            'reference_number' => $d->reference_number,
            'gateway_reference' => $d->gateway_reference,
            'receipt_number' => $d->receipt_number,
            'amount' => $d->amount,
            'currency' => $d->currency,
            'status' => $d->status,
            'donation_type' => $d->donation_type,
            'gateway' => $d->gateway,
            'is_subscription' => !is_null($d->subscription_id),
            'organization_name' => $d->donor?->organization_name,
            'program_name' => $d->program?->program_name,
            'has_receipt' => $d->receipts()->exists(),
            'receipt_url' => $d->receipts()->exists()
                ? url("/api/donor/receipts/{$d->id}/download")
                : null,
            'transaction_date' => $d->transaction_date?->toIso8601String(),
        ]);

        $activeSubscription = Subscription::where('donor_id', $donor->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        $data = [
            'donor' => [
                'id' => $donor->id,
                'display_name' => $donor->display_name,
                'donor_type' => $donor->donor_type,
                'email' => $user->email,
            ],
            'stats' => $stats,
            'giving_by_program' => $givingByProgram,
            'recent_donations' => $recentDonations,
            'first_donation_date' => $firstDonationDate?->toIso8601String(),
            'supported_beneficiaries' => $supportedBeneficiaries,
            'current_streak' => $currentStreak,
            'milestones' => array_values($milestones),
        ];

        if ($activeSubscription) {
            $data['next_billing'] = [
                'date' => $activeSubscription->next_billing_date?->toDateString(),
                'amount' => $activeSubscription->amount,
                'plan_name' => $activeSubscription->displayPlanName(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * List all programs for the donation form dropdown.
     *
     * Flutter: Populate a dropdown/selector when the donor chooses a program.
     */
    public function programs(): JsonResponse
    {
        $programs = Program::orderBy('program_name')->get(['id', 'program_name as name']);

        return response()->json([
            'success' => true,
            'data' => $programs,
        ], 200);
    }

    /**
     * List available PayPal subscription plans from config.
     *
     * Flutter: Display these as selectable subscription tiers.
     * The key is used as subscription_plan in POST /checkout.
     */
    public function subscriptionPlans(): JsonResponse
    {
        $plans = config('services.paypal.subscription_plans', []);

        $data = collect($plans)->map(fn (array $plan, string $key) => [
            'key' => $key,
            'name' => $plan['name'],
            'description' => $plan['description'],
            'amount' => (string) $plan['amount'],
            'currency' => $plan['currency'],
            'program_name' => $plan['program_name'],
        ])->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * Get donation history with pagination.
     *
     * Flutter: Display a paginated list. Use query params page and per_page.
     * has_receipt indicates whether a receipt PDF is available for download.
     */
    public function donations(Request $request): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;

        $perPage = (int) $request->query('per_page', 10);

        $paginator = Donation::with('program')
            ->where('donor_id', $donor->id)
            ->latest('transaction_date')
            ->paginate(min($perPage, 100));

        $donations = collect($paginator->items())->map(fn (Donation $d) => [
            'id' => $d->id,
            'reference_number' => $d->reference_number,
            'gateway_reference' => $d->gateway_reference,
            'receipt_number' => $d->receipt_number,
            'amount' => $d->amount,
            'currency' => $d->currency,
            'status' => $d->status,
            'donation_type' => $d->donation_type,
            'gateway' => $d->gateway,
            'program' => $d->program ? ['id' => $d->program->id, 'name' => $d->program->program_name] : null,
            'has_receipt' => $d->receipts()->exists(),
            'transaction_date' => $d->transaction_date?->toIso8601String(),
            'created_at' => $d->created_at?->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'donations' => $donations,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                ],
            ],
        ], 200);
    }

    /**
     * Get a single donation's details with receipt info.
     *
     * Flutter: Navigate here from the donation list to show full details.
     */
    public function donationDetail(Donation $donation): JsonResponse
    {
        $user = request()->user();
        $donor = $user->donor;

        abort_if($donation->donor_id !== $donor->id, 403);

        $donation->loadMissing(['program', 'subscription', 'receipts']);

        $receipt = $donation->receipts->sortByDesc('id')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $donation->id,
                'reference_number' => $donation->reference_number,
                'gateway_reference' => $donation->gateway_reference,
                'receipt_number' => $donation->receipt_number,
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'status' => $donation->status,
                'donation_type' => $donation->donation_type,
                'gateway' => $donation->gateway,
                'program' => $donation->program
                    ? ['id' => $donation->program->id, 'name' => $donation->program->program_name]
                    : null,
                'subscription' => $donation->subscription
                    ? ['id' => $donation->subscription->id, 'plan_name' => $donation->subscription->displayPlanName()]
                    : null,
                'description' => $donation->description,
                'has_receipt' => $receipt !== null,
                'receipt' => $receipt
                    ? ['id' => $receipt->id, 'sent_at' => $receipt->sent_at?->toIso8601String()]
                    : null,
                'transaction_date' => $donation->transaction_date?->toIso8601String(),
                'created_at' => $donation->created_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * List the donor's subscriptions with pagination.
     *
     * Flutter: Display active/paused/cancelled subscriptions.
     * Each shows the next billing date and plan name.
     */
    public function subscriptions(Request $request): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;

        $perPage = (int) $request->query('per_page', 10);

        $paginator = Subscription::where('donor_id', $donor->id)
            ->latest('id')
            ->paginate(min($perPage, 100));

        $subscriptions = collect($paginator->items())->map(fn (Subscription $s) => [
            'id' => $s->id,
            'plan_id' => $s->plan_id,
            'plan_name' => $s->displayPlanName(),
            'amount' => $s->amount,
            'currency' => $s->currency,
            'status' => $s->status,
            'next_billing_date' => $s->next_billing_date?->toDateString(),
            'paypal_subscription_id' => $s->paypal_subscription_id,
            'created_at' => $s->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'subscriptions' => $subscriptions,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                ],
            ],
        ], 200);
    }

    /**
     * Cancel a subscription via PayPal API.
     *
     * Flutter: Show a confirmation dialog, then POST here.
     * On success, refresh the subscriptions list.
     */
    public function cancelSubscription(Subscription $subscription, PayPalPaymentService $paypal): JsonResponse
    {
        $user = request()->user();
        $donor = $user->donor;

        abort_if($subscription->donor_id !== $donor->id, 403);

        if (! in_array($subscription->status, ['active', 'paused', 'pending'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription is not cancellable in its current state.',
            ], 422);
        }

        if (! $subscription->paypal_subscription_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to cancel this subscription: missing PayPal identifier.',
            ], 422);
        }

        try {
            $paypal->cancelSubscription($subscription->paypal_subscription_id, 'Cancelled by donor via mobile app');

            $metadata = $subscription->metadata ?? [];
            $subscription->update([
                'status' => 'cancelled',
                'metadata' => array_merge($metadata, [
                    'cancelled_by_donor' => true,
                    'cancelled_at' => now()->toISOString(),
                ]),
            ]);

            $this->notifyCancellation($subscription);

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully.',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to cancel subscription right now. Please try again later.',
            ], 500);
        }
    }

    /**
     * Get donation summary statistics only
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;

        $lastDonation = Donation::where('donor_id', $donor->id)
            ->latest('transaction_date')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_donated' => (float) Donation::where('donor_id', $donor->id)->sum('amount'),
                'total_donations' => (int) Donation::where('donor_id', $donor->id)->count(),
                'active_subscriptions' => (int) Subscription::where('donor_id', $donor->id)->where('status', 'active')->count(),
                'last_donation_date' => $lastDonation ? $lastDonation->transaction_date?->toDateString() : null,
            ],
        ], 200);
    }

    /**
     * Get donation trends over time
     */
    public function trends(Request $request): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;

        $months = (int) $request->query('months', 12);
        $startDate = Carbon::now()->subMonths($months);

        $trends = Donation::where('donor_id', $donor->id)
            ->where('transaction_date', '>=', $startDate)
            ->select(
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(fn ($item) => [
                'period' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                'total_amount' => (float) $item->total,
                'donation_count' => (int) $item->count,
            ]);

        return response()->json([
            'success' => true,
            'data' => $trends,
        ], 200);
    }

    /**
     * Stream the receipt PDF inline (view in browser).
     *
     * Flutter: Download response bytes, save to temp file via path_provider,
     * then open with system PDF viewer using open_file or share_plus.
     */
    public function receipt(Donation $donation): StreamedResponse
    {
        return $this->serveReceipt($donation, false);
    }

    /**
     * Download the receipt PDF as an attachment.
     *
     * Flutter: Same approach as receipt() above but with download semantics.
     */
    public function downloadReceipt(Donation $donation): StreamedResponse
    {
        return $this->serveReceipt($donation, true);
    }

    /**
     * Stream the receipt PDF file from private storage.
     */
    private function serveReceipt(Donation $donation, bool $download): StreamedResponse
    {
        $user = request()->user();
        $donor = $user->donor;

        abort_if($donation->donor_id !== $donor->id, 403);

        $donation->loadMissing('receipts');

        $receipt = $donation->receipts->sortByDesc('id')->first();
        $receiptPath = $receipt?->path ?? $donation->receipt_path;

        abort_if(! $receiptPath, 404, 'Receipt is not available for this donation.');

        $disk = Storage::disk(config('filesystems.default'));
        abort_if(! $disk->exists($receiptPath), 404, 'Receipt file could not be found.');

        $filename = basename($receiptPath);
        $stream = $disk->readStream($receiptPath);

        abort_if(! is_resource($stream), 404, 'Receipt file could not be read.');

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline') . '; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($stream): void {
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, $headers);
    }

    // --- Notification methods (same pattern as BeneficiaryController) ---

    public function notifications(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = $user->notifications();

        if ($request->query('filter') === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->get()->map(fn ($n) => [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->data['title'] ?? match ($n->type) {
                'donation_cleared' => 'DONATION CLEARED',
                'donation_received' => 'DONATION RECEIVED',
                'manual_donation_added' => 'DONATION ADDED',
                'subscription_activated' => 'SUBSCRIPTION ACTIVE',
                default => 'NOTIFICATION',
            },
            'message' => $n->data['message'] ?? '',
            'is_read' => $n->read_at !== null,
            'created_at' => $n->created_at?->diffForHumans(),
        ]);

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    public function unreadNotificationCount(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => request()->user()->unreadNotifications()->count(),
            ],
        ]);
    }

    public function markNotificationRead(string $id): JsonResponse
    {
        $user = request()->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead(): JsonResponse
    {
        request()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    public function deleteNotification(string $id): JsonResponse
    {
        $user = request()->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    private function notifyCancellation(Subscription $subscription): void
    {
        $user = $subscription->donor?->user;
        if ($user) {
            try {
                $user->notify(new SubscriptionCancelledNotification($subscription));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send cancellation notification to donor', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $staffUsers = User::whereHas('staff', function ($query): void {
            $query->whereIn('role', [
                Staff::ROLE_ADMINISTRATOR,
                Staff::ROLE_EXECUTIVE_DIRECTOR,
                Staff::ROLE_DONOR_MANAGER,
                Staff::ROLE_ADMIN_FINANCE_STAFF,
            ]);
        })->get();

        foreach ($staffUsers as $staffUser) {
            try {
                $staffUser->notify(new SubscriptionCancelledStaffNotification($subscription));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send cancellation notification to staff', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $staffUser->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}