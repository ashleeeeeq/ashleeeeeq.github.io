<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Program;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionCancelledNotification;
use App\Notifications\SubscriptionCancelledStaffNotification;
use App\Services\Payments\PayPalPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DonorPortalController extends Controller
{
    public function donationPage(Request $request): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && $user->user_type !== 'donor') {
            abort(403);
        }

        $view = $user ? 'donors.donate' : 'donate';

        return view($view, [
            'programs' => Program::orderBy('program_name')->get(),
            'subscriptionPlans' => config('services.paypal.subscription_plans', []),
            'name' => $user?->display_name,
        ]);
    }

    public function donations(Request $request): View
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user?->user_type === 'donor', 403);

        $user->loadMissing(['donor.donations']);
        $donor = $user->donor;

        abort_unless($donor, 404);

        $donations = Donation::query()
            ->with(['receipts'])
            ->where('donor_id', $donor->id)
            ->latest('transaction_date')
            ->paginate(10)
            ->withQueryString();

        return view('donors.donations.index', [
            'name' => $user->display_name,
            'donor' => $donor,
            'donations' => $donations,
        ]);
    }

    public function viewDonationReceipt(Donation $donation)
    {
        return $this->serveDonationReceipt($donation, false);
    }

    public function downloadDonationReceipt(Donation $donation)
    {
        return $this->serveDonationReceipt($donation, true);
    }

    public function transactions(Request $request): View
    {
        return $this->donations($request);
    }

    public function subscriptions(): View
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user?->user_type === 'donor', 403);

        $user->loadMissing('donor');
        $donor = $user->donor;

        abort_unless($donor, 404);

        $subscriptions = Subscription::query()
            ->where('donor_id', $donor->id)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('donors.subscriptions.index', [
            'name' => $user->display_name,
            'donor' => $donor,
            'subscriptions' => $subscriptions,
        ]);
    }

    public function cancelSubscription(Subscription $subscription, PayPalPaymentService $paypal): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user?->user_type === 'donor', 403);

        $user->loadMissing('donor');
        $donor = $user->donor;

        abort_unless($donor && $subscription->donor_id === $donor->id, 403);

        if (! in_array($subscription->status, ['active', 'paused', 'pending'], true)) {
            return back()->with('status', 'Subscription is not cancellable in its current state.');
        }

        if (! $subscription->paypal_subscription_id) {
            return back()->with('status', 'Unable to cancel this subscription: missing PayPal identifier.');
        }

        try {
            $paypal->cancelSubscription($subscription->paypal_subscription_id, 'Cancelled by donor via portal');

            $metadata = $subscription->metadata ?? [];
            $subscription->update([
                'status' => 'cancelled',
                'metadata' => array_merge($metadata, [
                    'cancelled_by_donor' => true,
                    'cancelled_at' => now()->toISOString(),
                ]),
            ]);

            $this->notifyCancellation($subscription);

            return back()->with('status', 'Subscription cancelled successfully.');
        } catch (\Throwable $e) {
            return back()->with('status', 'Unable to cancel subscription right now. Please try again later.');
        }
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

    private function serveDonationReceipt(Donation $donation, bool $download)
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user?->user_type === 'donor', 403);

        $user->loadMissing('donor');
        $donor = $user->donor;

        abort_unless($donor && $donation->donor_id === $donor->id, 403);

        $donation->loadMissing('receipts');

        $receipt = $donation->receipts->sortByDesc('id')->first();
        $receiptPath = $receipt?->path ?? $donation->receipt_path;

        abort_unless($receiptPath, 404, 'Receipt is not available for this donation.');

        $disk = Storage::disk(config('filesystems.default'));
        abort_unless($disk->exists($receiptPath), 404, 'Receipt file could not be found.');

        $filename = basename($receiptPath);
        $stream = $disk->readStream($receiptPath);

        abort_unless(is_resource($stream), 404, 'Receipt file could not be read.');

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline') . '; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }
}