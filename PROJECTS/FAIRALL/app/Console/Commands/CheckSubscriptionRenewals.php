<?php

namespace App\Console\Commands;

use App\Models\Staff;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionMissedPaymentNotification;
use App\Notifications\SubscriptionMissedPaymentStaffNotification;
use App\Notifications\SubscriptionRenewalReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionRenewals extends Command
{
    protected $signature = 'app:check-subscription-renewals';

    protected $description = 'Check for upcoming renewal reminders and missed payments on subscriptions';

    public function handle(): int
    {
        $this->info('Checking subscription renewals...');

        $this->sendRenewalReminders();
        $this->detectMissedPayments();

        $this->info('Done.');

        return self::SUCCESS;
    }

    protected function sendRenewalReminders(): void
    {
        $upcoming = Subscription::query()
            ->where('status', 'active')
            ->whereDate('next_billing_date', '>=', now()->toDateString())
            ->whereDate('next_billing_date', '<=', now()->addDays(3)->toDateString())
            ->get();

        foreach ($upcoming as $subscription) {
            if ($this->hasRecentNotification($subscription->id, 'subscription_renewal_reminder')) {
                continue;
            }

            $user = $subscription->donor?->user;
            if (!$user) {
                continue;
            }

            try {
                $user->notify(new SubscriptionRenewalReminderNotification($subscription));
                $this->line('  Sent renewal reminder for subscription #' . $subscription->id);
            } catch (\Throwable $e) {
                Log::error('Failed to send renewal reminder', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function detectMissedPayments(): void
    {
        $overdue = Subscription::query()
            ->where('status', 'active')
            ->whereDate('next_billing_date', '<=', now()->subDays(2)->toDateString())
            ->get();

        foreach ($overdue as $subscription) {
            if ($this->hasRecentNotification($subscription->id, 'subscription_missed_payment')) {
                continue;
            }

            $hasPayment = DB::table('donations')
                ->where('subscription_id', $subscription->id)
                ->where('status', 'completed')
                ->where('transaction_date', '>=', $subscription->next_billing_date?->startOfDay())
                ->exists();

            if ($hasPayment) {
                continue;
            }

            // Notify donor
            $user = $subscription->donor?->user;
            if ($user) {
                try {
                    $user->notify(new SubscriptionMissedPaymentNotification($subscription));
                    $this->line('  Sent missed-payment alert to donor for subscription #' . $subscription->id);
                } catch (\Throwable $e) {
                    Log::error('Failed to send missed-payment notification to donor', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Notify staff
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
                    $staffUser->notify(new SubscriptionMissedPaymentStaffNotification($subscription));
                } catch (\Throwable $e) {
                    Log::error('Failed to send missed-payment notification to staff', [
                        'subscription_id' => $subscription->id,
                        'user_id' => $staffUser->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    protected function hasRecentNotification(int $subscriptionId, string $type, int $hours = 24): bool
    {
        return DB::table('notifications')
            ->where('type', 'like', '%' . $type . '%')
            ->where('data->subscription_id', $subscriptionId)
            ->where('created_at', '>=', now()->subHours($hours))
            ->exists();
    }
}
