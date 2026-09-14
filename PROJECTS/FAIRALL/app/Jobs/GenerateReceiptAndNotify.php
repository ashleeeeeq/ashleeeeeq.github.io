<?php

namespace App\Jobs;

use App\Mail\DonationReceipt;
use App\Models\Donation;
use App\Models\Receipt;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\DonationReceivedStaffNotification;
use App\Notifications\GuestDonationReceived;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\Storage;

class GenerateReceiptAndNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $donationId,
        public bool $forceSend = false,
    )
    {
    }

    public function handle(): void
    {
        $donation = Donation::with('donor.user')->find($this->donationId);

        if (! $donation) {
            return;
        }

        // Ensure stable receipt number exists for all confirmed donations.
        $donation->ensureReceiptNumber();

        $hash = Receipt::computeHashForDonation($donation);
        $receipt = Receipt::where('donation_id', $donation->id)->first()
            ?? Receipt::where('hash', $hash)->first();

        if (! $receipt) {
            // render PDF
            $viewData = ['donation' => $donation];

            $pdf = Pdf::loadView('emails.receipt_pdf', $viewData);

            $filename = 'receipts/receipt-' . $donation->id . '-' . substr($hash, 0, 12) . '.pdf';

            Storage::put($filename, $pdf->output());

            $receipt = Receipt::create([
                'donation_id' => $donation->id,
                'path' => $filename,
                'hash' => $hash,
            ]);
        }

        $this->notifyDonor($donation);

        // Always notify staff — independent of email success
        $this->notifyStaff($donation);

        // determine recipient email
        $email = $donation->donor?->user?->email
            ?? data_get($donation->metadata, 'payer_email')
            ?? data_get($donation->metadata, 'payer.email')
            ?? data_get($donation->metadata, 'payer.email_address')
            ?? data_get($donation->metadata, 'payer.payer_info.email');

        if ($email && ($this->forceSend || ! $receipt->sent_at)) {
            try {
                Mail::to($email)->send(new DonationReceipt($donation, $receipt));
                $receipt->sent_at = now();
                $receipt->save();
            } catch (\Throwable $e) {
                Log::error('Failed to send donation receipt email', [
                    'donation_id' => $donation->id,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }

            return;
        }

        if ($email) {
            return;
        }

        if (! $email && ! $receipt->sent_at) {
            $recipients = User::query()
                ->whereHas('staff', function ($query): void {
                    $query->whereIn('role', [
                        Staff::ROLE_ADMINISTRATOR,
                        Staff::ROLE_EXECUTIVE_DIRECTOR,
                        Staff::ROLE_DONOR_MANAGER,
                        Staff::ROLE_ADMIN_FINANCE_STAFF,
                    ]);
                })
                ->get();

            if ($recipients->isNotEmpty()) {
                try {
                    NotificationFacade::send($recipients, new GuestDonationReceived($donation));
                    $receipt->sent_at = now();
                    $receipt->save();
                } catch (\Throwable $e) {
                    Log::error('Failed to send guest donation notification', [
                        'donation_id' => $donation->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    protected function notifyDonor(Donation $donation): void
    {
        $user = $donation->donor?->user;
        $notificationNamespace = 'App\\Notifications\\';
        $subscriptionActivatedNotification = $notificationNamespace . 'SubscriptionActivatedNotification';
        $donationClearedNotification = $notificationNamespace . 'DonationClearedNotification';

        if (! $user || strtolower((string) $donation->gateway) === 'manual') {
            return;
        }

        $subscription = $donation->subscription;

        if ($subscription) {
            $subscriptionDonationCount = $subscription->donations()->count();

            if ($subscriptionDonationCount === 1) {
                if (! $this->hasNotification($user, $subscriptionActivatedNotification, $donation->id)) {
                    $user->notify(new $subscriptionActivatedNotification($donation));
                }

                return;
            }
        }

        if (! $this->hasNotification($user, $donationClearedNotification, $donation->id)) {
            $user->notify(new $donationClearedNotification($donation));
        }
    }

    protected function notifyStaff(Donation $donation): void
    {
        $users = User::whereHas('staff', function ($query): void {
            $query->whereIn('role', [
                Staff::ROLE_ADMINISTRATOR,
                Staff::ROLE_EXECUTIVE_DIRECTOR,
                Staff::ROLE_DONOR_MANAGER,
                Staff::ROLE_ADMIN_FINANCE_STAFF,
            ]);
        })->get();

        $type = DonationReceivedStaffNotification::class;

        foreach ($users as $user) {
            if (! $this->hasNotification($user, $type, $donation->id)) {
                $user->notify(new DonationReceivedStaffNotification($donation));
            }
        }
    }

    protected function hasNotification(User $user, string $type, int $donationId): bool
    {
        return $user->notifications()
            ->where('type', $type)
            ->where('data->donation_id', $donationId)
            ->exists();
    }
}
