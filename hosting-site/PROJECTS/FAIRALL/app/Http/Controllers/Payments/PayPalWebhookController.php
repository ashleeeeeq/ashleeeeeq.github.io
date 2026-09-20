<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\CheckoutSession;
use App\Models\Donation;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionCancelledNotification;
use App\Notifications\SubscriptionCancelledStaffNotification;
use App\Services\Payments\PayPalPaymentService;
use App\Jobs\GenerateReceiptAndNotify;
use App\Services\Donors\ProvisionalDonorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request, PayPalPaymentService $paypal): JsonResponse
    {
        Log::info('Received PayPal webhook.', [
            'event_type' => $request->input('event_type'),
            'resource_id' => data_get($request->input('resource', []), 'id'),
        ]);

        try {
            $verified = $paypal->verifyWebhook($request);
        } catch (\Throwable $e) {
            Log::error('PayPal webhook verification threw an exception.', [
                'event_type' => $request->input('event_type'),
                'resource_id' => data_get($request->input('resource', []), 'id'),
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            return response()->json(['message' => 'Webhook verification error.'], 500);
        }

        if (! $verified) {
            Log::warning('Rejected PayPal webhook due to signature verification failure.', [
                'event_type' => $request->input('event_type'),
                'resource_id' => data_get($request->input('resource', []), 'id'),
            ]);

            return response()->json(['message' => 'Invalid webhook signature.'], 422);
        }

        $eventType = (string) $request->input('event_type');
        $resource = (array) $request->input('resource', []);

        match ($eventType) {
            'BILLING.SUBSCRIPTION.ACTIVATED',
            'BILLING.SUBSCRIPTION.CREATED',
            'BILLING.SUBSCRIPTION.UPDATED' => $this->syncSubscription($resource),
            'BILLING.SUBSCRIPTION.CANCELLED',
            'BILLING.SUBSCRIPTION.SUSPENDED',
            'BILLING.SUBSCRIPTION.EXPIRED' => $this->markSubscriptionInactive($resource, $eventType),
            'PAYMENT.CAPTURE.COMPLETED',
            'PAYMENT.SALE.COMPLETED' => $this->recordCompletedPayment($resource, $eventType),
            'PAYMENT.CAPTURE.REFUNDED',
            'PAYMENT.SALE.REFUNDED' => $this->markPaymentRefunded($resource, $eventType),
            default => Log::info('Unhandled PayPal webhook event.', [
                'event_type' => $eventType,
                'resource_id' => data_get($resource, 'id'),
            ]),
        };

        return response()->json(['message' => 'Webhook processed.']);
    }

    protected function syncSubscription(array $resource): void
    {
        $subscriptionId = data_get($resource, 'id');

        if (! $subscriptionId) {
            return;
        }

        $checkoutSession = $this->checkoutSessionFromSubscriptionResource($resource);
        $planId = (string) (data_get($resource, 'plan_id')
            ?? $checkoutSession?->metadata['subscription_plan']['plan_id']
            ?? '');
        $plan = $this->subscriptionPlanById($planId);
        $status = data_get($resource, 'status');
        $subscriptionData = [
            'donor_id' => $checkoutSession?->donor_id,
            'plan_id' => $planId !== '' ? $planId : null,
            'amount' => $this->resolveSubscriptionAmount($resource, $checkoutSession, $plan),
            'currency' => $this->resolveSubscriptionCurrency($resource, $checkoutSession, $plan),
            'next_billing_date' => data_get($resource, 'billing_info.next_billing_time')
                ? Carbon::parse(data_get($resource, 'billing_info.next_billing_time'))->toDateString()
                : null,
            'status' => match ($status) {
                'CREATED' => 'pending',
                'APPROVAL_PENDING' => 'pending',
                default => 'active',
            },
            'metadata' => array_merge($resource, [
                'checkout_session_id' => $checkoutSession?->id,
                'subscription_plan' => $plan,
            ]),
        ];

        $subscription = Subscription::where('paypal_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            $subscription = $this->pendingSubscriptionForCheckout($checkoutSession?->id, (string) data_get($resource, 'plan_id'));
        }

        if ($subscription) {
            $subscription->forceFill(array_merge($subscriptionData, [
                'paypal_subscription_id' => $subscriptionId,
            ]))->save();

            $donor = $this->ensureProvisionalDonorFromSubscriptionResource($resource, $checkoutSession);

            if ($donor && ! $subscription->donor_id) {
                $subscription->forceFill(['donor_id' => $donor->id])->save();
            }

            if ($donor && $checkoutSession && ! $checkoutSession->donor_id) {
                $checkoutSession->forceFill(['donor_id' => $donor->id])->save();
            }

            return;
        }

        $subscription = Subscription::create(array_merge($subscriptionData, [
            'paypal_subscription_id' => $subscriptionId,
        ]));

        $donor = $this->ensureProvisionalDonorFromSubscriptionResource($resource, $checkoutSession);

        if ($donor && ! $subscription->donor_id) {
            $subscription->forceFill(['donor_id' => $donor->id])->save();
        }

        if ($donor && $checkoutSession && ! $checkoutSession->donor_id) {
            $checkoutSession->forceFill(['donor_id' => $donor->id])->save();
        }
    }

    protected function ensureProvisionalDonorFromSubscriptionResource(array $resource, ?CheckoutSession $checkoutSession = null): ?\App\Models\Donor
    {
        $payerEmail = $this->subscriptionPayerEmail($resource);
        $payerName = $this->subscriptionPayerName($resource);

        if (! $payerEmail && ! $payerName) {
            return null;
        }

        $service = new ProvisionalDonorService();
        $donor = $service->createFromPayer([
            'email' => $payerEmail,
            'name' => $payerName,
        ]);

        if ($donor && $checkoutSession && ! $checkoutSession->donor_id) {
            $checkoutSession->forceFill(['donor_id' => $donor->id])->save();
        }

        return $donor;
    }

    protected function subscriptionPayerEmail(array $resource): ?string
    {
        $candidates = [
            data_get($resource, 'subscriber.email_address'),
            data_get($resource, 'subscriber.payer_info.email'),
            data_get($resource, 'subscriber.payer_info.email_address'),
            data_get($resource, 'payer.email_address'),
            data_get($resource, 'payer.payer_info.email'),
            data_get($resource, 'payer.email'),
        ];

        foreach ($candidates as $candidate) {
            $candidate = is_string($candidate) ? trim($candidate) : '';

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    protected function subscriptionPayerName(array $resource): ?string
    {
        $candidates = [
            data_get($resource, 'subscriber.name.full_name'),
            data_get($resource, 'subscriber.name.given_name'),
            data_get($resource, 'subscriber.name'),
            data_get($resource, 'payer.name'),
            data_get($resource, 'payer.payer_info.name'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }

            if (is_array($candidate)) {
                $name = trim(implode(' ', array_filter([
                    data_get($candidate, 'given_name'),
                    data_get($candidate, 'surname'),
                    data_get($candidate, 'family_name'),
                ])));

                if ($name !== '') {
                    return $name;
                }
            }
        }

        return null;
    }

    protected function markSubscriptionInactive(array $resource, string $eventType): void
    {
        $subscriptionId = data_get($resource, 'id');

        if (! $subscriptionId) {
            return;
        }

        $subscription = Subscription::where('paypal_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            return;
        }

        $newStatus = match ($eventType) {
            'BILLING.SUBSCRIPTION.CANCELLED' => 'cancelled',
            'BILLING.SUBSCRIPTION.SUSPENDED' => 'paused',
            'BILLING.SUBSCRIPTION.EXPIRED' => 'expired',
            default => 'paused',
        };

        $wasAlready = $subscription->getOriginal('status') === $newStatus;

        $subscription->update([
            'status' => $newStatus,
            'metadata' => $resource,
        ]);

        if ($newStatus === 'cancelled' && !$wasAlready) {
            $this->notifyCancellation($subscription);
        }
    }

    protected function notifyCancellation(Subscription $subscription): void
    {
        $user = $subscription->donor?->user;
        if ($user) {
            try {
                $user->notify(new SubscriptionCancelledNotification($subscription));
            } catch (\Throwable $e) {
                Log::error('Failed to send cancellation notification to donor', [
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
                Log::error('Failed to send cancellation notification to staff', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $staffUser->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function recordCompletedPayment(array $resource, string $eventType): void
    {
        $gatewayReference = (string) (data_get($resource, 'supplementary_data.related_ids.order_id')
            ?? data_get($resource, 'related_ids.order_id')
            ?? data_get($resource, 'id'));

        if ($gatewayReference === '') {
            return;
        }

        $checkoutSession = $this->checkoutSessionFromPaymentResource($resource, $gatewayReference);
        $subscriptionId = $this->subscriptionIdFromPaymentResource($resource);
        $subscription = $subscriptionId ? Subscription::where('paypal_subscription_id', $subscriptionId)->first() : null;
        $checkoutSessionId = $checkoutSession?->id;
        $amount = $this->payPalAmountFromPaymentResource($resource, $eventType, $checkoutSession?->amount);

        if ($amount <= 0) {
            Log::warning('PayPal webhook: resolved amount is zero or negative, skipping donation creation.', [
                'gateway_reference' => $gatewayReference,
                'event_type' => $eventType,
            ]);

            return;
        }

        $currency = (string) (data_get($resource, 'amount.currency_code')
            ?? $checkoutSession?->currency
            ?? config('services.paypal.currency', 'USD'));
        $existingDonation = null;

        if ($checkoutSessionId) {
            $existingDonation = Donation::where('gateway', 'paypal')
                ->where('checkout_session_id', $checkoutSessionId)
                ->first();
        }

        if (! $existingDonation) {
            $existingDonation = Donation::where('gateway', 'paypal')
                ->where('gateway_reference', $gatewayReference)
                ->first();
        }

        $transactionDate = $existingDonation?->transaction_date ?? now();

        $donationData = [
            'gateway' => 'paypal',
            'gateway_reference' => $gatewayReference,
            'donor_id' => $checkoutSession?->donor_id ?? $subscription?->donor_id,
            'program_id' => $checkoutSession?->program_id,
            'donation_type' => 'financial',
            'amount' => $amount,
            'currency' => $currency,
            'transaction_date' => $transactionDate,
            'status' => 'completed',
            'description' => $checkoutSession?->metadata['description'] ?? null,
            'subscription_id' => $subscription?->id,
            'metadata' => array_merge($resource, [
                'checkout_session_id' => $checkoutSessionId,
                'subscription_id' => $subscription?->id,
                'anonymous' => $checkoutSession?->metadata['anonymous'] ?? null,
            ]),
            'checkout_session_id' => $checkoutSessionId,
        ];

        if ($existingDonation) {
            $existingDonation->forceFill($donationData)->save();
        } else {
            $existingDonation = Donation::create($donationData);
        }

        // Create provisional donor FIRST (if payer info exists), then generate receipt
        if ($existingDonation && ! $existingDonation->donor_id) {
            $payerEmail = data_get($resource, 'payer.email_address') ?? data_get($resource, 'payer.payer_info.email') ?? data_get($resource, 'payer.email') ?? $checkoutSession?->metadata['payer_email'] ?? null;
            $payerName = data_get($resource, 'payer.name') ?? data_get($resource, 'payer.payer_info.name') ?? null;

            if ($payerEmail || $payerName) {
                $service = new ProvisionalDonorService();
                $donor = $service->createFromPayer([
                    'email' => $payerEmail,
                    'name' => $payerName,
                ]);

                if ($donor) {
                    $existingDonation->donor_id = $donor->id;
                    $existingDonation->save();
                }
            }
        }

        if ($existingDonation) {
            $existingDonation->ensureReceiptNumber();
        }

        // enqueue receipt generation (idempotent job) — after donor is attached
        if ($existingDonation) {
            GenerateReceiptAndNotify::dispatch($existingDonation->id);
        }
    }

    protected function markPaymentRefunded(array $resource, string $eventType): void
    {
        $gatewayReference = (string) (data_get($resource, 'id') ?? data_get($resource, 'sale_id'));

        if ($gatewayReference === '') {
            return;
        }

        Donation::where('gateway', 'paypal')
            ->where('gateway_reference', $gatewayReference)
            ->update([
                'status' => 'refunded',
                'metadata' => $resource,
            ]);
    }

    protected function checkoutSessionFromSubscriptionResource(array $resource): ?CheckoutSession
    {
        // Try extracting a numeric checkout session ID from any custom_id format (e.g. "checkout-42", "xen_invoice_42")
        $customId = (string) data_get($resource, 'custom_id', '');

        if ($customId !== '') {
            preg_match('/\d+$/', $customId, $matches);

            if (! empty($matches)) {
                $checkoutSessionId = (int) $matches[0];

                if ($checkoutSessionId > 0) {
                    $checkoutSession = CheckoutSession::find($checkoutSessionId);

                    if ($checkoutSession) {
                        return $checkoutSession;
                    }
                }
            }
        }

        // Fallback: look up via pending subscription linked to this PayPal subscription ID
        $paypalSubscriptionId = (string) data_get($resource, 'id', '');

        if ($paypalSubscriptionId !== '') {
            $subscription = Subscription::where('paypal_subscription_id', $paypalSubscriptionId)->first();

            if ($subscription?->metadata['checkout_session_id'] ?? null) {
                return CheckoutSession::find((int) $subscription->metadata['checkout_session_id']);
            }
        }

        return null;
    }

    protected function checkoutSessionFromPaymentResource(array $resource, ?string $gatewayReference = null): ?CheckoutSession
    {
        // First, try extracting session ID from custom_id (format: xen_invoice_{sessionId})
        $customId = (string) data_get($resource, 'custom_id', '');

        if ($customId !== '') {
            preg_match('/\d+$/', $customId, $matches);

            if (! empty($matches)) {
                $checkoutSessionId = (int) $matches[0];

                if ($checkoutSessionId > 0) {
                    $checkoutSession = CheckoutSession::find($checkoutSessionId);

                    if ($checkoutSession) {
                        return $checkoutSession;
                    }
                }
            }
        }

        $candidateReferences = array_values(array_filter([
            $gatewayReference,
            (string) data_get($resource, 'supplementary_data.related_ids.order_id'),
            (string) data_get($resource, 'related_ids.order_id'),
            (string) data_get($resource, 'billing_agreement_id'),
            (string) data_get($resource, 'billing_agreement_details.billing_agreement_id'),
            (string) data_get($resource, 'subscription_id'),
            (string) data_get($resource, 'parent_payment'),
            $customId,
        ]));

        foreach ($candidateReferences as $reference) {
            $reference = trim($reference);

            if ($reference === '') {
                continue;
            }

            $checkoutSession = CheckoutSession::where('provider_session_id', $reference)->first();

            if ($checkoutSession) {
                return $checkoutSession;
            }
        }

        return null;
    }

    protected function payPalAmountFromPaymentResource(array $resource, string $eventType, mixed $fallbackAmount = null): float
    {
        $amount = match ($eventType) {
            'PAYMENT.CAPTURE.COMPLETED' => data_get($resource, 'amount.value'),
            'PAYMENT.SALE.COMPLETED' => data_get($resource, 'amount.total'),
            default => null,
        };

        return (float) ($amount ?: $fallbackAmount ?: 0);
    }

    protected function subscriptionIdFromPaymentResource(array $resource): ?string
    {
        $candidates = [
            data_get($resource, 'supplementary_data.related_ids.subscription_id'),
            data_get($resource, 'subscription_id'),
            data_get($resource, 'billing_agreement_id'),
            data_get($resource, 'billing_agreement_details.billing_agreement_id'),
        ];

        foreach ($candidates as $candidate) {
            $candidate = is_string($candidate) ? trim($candidate) : '';

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    protected function pendingSubscriptionForCheckout(?int $checkoutSessionId, ?string $planId): ?Subscription
    {
        // Require at least one filter to avoid matching an unrelated pending subscription
        if ($checkoutSessionId === null && ($planId === null || $planId === '')) {
            return null;
        }

        $query = Subscription::query()->where('status', 'pending');

        if ($checkoutSessionId) {
            $query->where('metadata->checkout_session_id', $checkoutSessionId);
        }

        if ($planId) {
            $query->where('plan_id', $planId);
        }

        return $query->latest('id')->first();
    }

    protected function subscriptionPlanById(?string $planId): ?array
    {
        $planId = trim((string) $planId);

        if ($planId === '') {
            return null;
        }

        foreach (config('services.paypal.subscription_plans', []) as $plan) {
            if (($plan['plan_id'] ?? null) === $planId) {
                return $plan;
            }
        }

        return null;
    }

    protected function resolveSubscriptionAmount(array $resource, ?CheckoutSession $checkoutSession = null, ?array $plan = null): float
    {
        $candidates = [
            data_get($resource, 'billing_info.last_payment.amount.value'),
            $checkoutSession?->amount,
            data_get($checkoutSession?->metadata, 'subscription_plan.amount'),
            $plan['amount'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null || $candidate === '') {
                continue;
            }

            $amount = (float) $candidate;

            if ($amount > 0) {
                return $amount;
            }
        }

        return 0.0;
    }

    protected function resolveSubscriptionCurrency(array $resource, ?CheckoutSession $checkoutSession = null, ?array $plan = null): string
    {
        $candidates = [
            data_get($resource, 'billing_info.last_payment.amount.currency_code'),
            data_get($resource, 'amount.currency_code'),
            $checkoutSession?->currency,
            data_get($checkoutSession?->metadata, 'subscription_plan.currency'),
            $plan['currency'] ?? null,
            config('services.paypal.currency', 'USD'),
        ];

        foreach ($candidates as $candidate) {
            $candidate = is_string($candidate) ? trim($candidate) : '';

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return config('services.paypal.currency', 'USD');
    }
}
