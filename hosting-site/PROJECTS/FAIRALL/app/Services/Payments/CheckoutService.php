<?php

namespace App\Services\Payments;

use App\Models\CheckoutSession;
use App\Models\Program;
use App\Models\Subscription;
use App\Models\User;

class CheckoutService
{
    public function __construct(
        private PaymentManager $payments,
    ) {}

    public function createCheckoutSession(array $validated, ?User $user): array
    {
        $donorId = $user?->donor?->id;
        $isSubscription = $validated['donation_frequency'] === 'subscription';
        $subscriptionPlan = $isSubscription ? $this->subscriptionPlan($validated['subscription_plan'] ?? null) : null;
        $programId = $validated['program_id'] ?? null;
        $amount = (float) ($validated['amount'] ?? 0);
        $gateway = $validated['gateway'] ?? null;
        $currency = $validated['currency'] ?? config('services.paypal.currency', 'USD');

        if ($isSubscription) {
            $programId = $subscriptionPlan['program_name']
                ? Program::query()->where('program_name', $subscriptionPlan['program_name'])->value('id')
                : null;
            $amount = (float) $subscriptionPlan['amount'];
            $gateway = 'paypal';
            $currency = (string) ($subscriptionPlan['currency'] ?? config('services.paypal.currency', 'USD'));
        }

        if (! $isSubscription) {
            if ($amount <= 0) {
                abort(422, 'Please enter a valid donation amount.');
            }

            if (! $gateway) {
                abort(422, 'Please choose a payment gateway.');
            }
        }

        $session = CheckoutSession::create([
            'donor_id' => $donorId,
            'program_id' => $programId,
            'gateway' => $gateway,
            'amount' => $amount,
            'currency' => $currency,
            'return_url' => $validated['return_url'] ?? url('/donate/return'),
            'cancel_url' => $validated['cancel_url'] ?? url('/donate/cancel'),
            'metadata' => array_filter([
                'description' => $validated['description'] ?? null,
                'payer_email' => $validated['payer_email'] ?? null,
                'send_receipt' => isset($validated['send_receipt']) && $validated['send_receipt'] === '1' ? true : false,
                'anonymous' => isset($validated['anonymous']) && $validated['anonymous'] === '1' ? true : false,
                'donation_frequency' => $validated['donation_frequency'],
                'subscription_plan' => $subscriptionPlan ? [
                    'key' => $validated['subscription_plan'],
                    'plan_id' => $subscriptionPlan['plan_id'],
                    'name' => $subscriptionPlan['name'],
                    'amount' => $subscriptionPlan['amount'],
                    'currency' => $subscriptionPlan['currency'],
                    'program_name' => $subscriptionPlan['program_name'],
                ] : null,
            ], static fn ($value) => $value !== null),
            'created_by' => $user?->staff?->id ?? null,
        ]);

        $returnUrl = $session->return_url;
        $cancelUrl = $session->cancel_url;

        $separator = str_contains($returnUrl, '?') ? '&' : '?';
        $returnUrlWithId = $returnUrl . $separator . 'checkout_id=' . $session->id;

        $separator = str_contains($cancelUrl, '?') ? '&' : '?';
        $cancelUrlWithId = $cancelUrl . $separator . 'checkout_id=' . $session->id;

        $externalId = 'invoice_' . $session->id;
        $session->update([
            'metadata' => array_merge($session->metadata ?? [], ['external_id' => $externalId]),
        ]);

        if ($isSubscription) {
            $payload = [
                'plan_id' => $subscriptionPlan['plan_id'],
                'custom_id' => $externalId,
                'return_url' => $returnUrlWithId,
                'cancel_url' => $cancelUrlWithId,
                'payer_email' => $session->metadata['payer_email'] ?? null,
            ];

            $result = $this->payments->createSubscriptionCheckout($session->gateway, $payload);

            $session->update([
                'provider_session_id' => $result['id'] ?? null,
                'status' => 'redirected',
                'metadata' => array_merge($session->metadata ?? [], ['provider_raw' => $result['raw'] ?? null]),
            ]);

            Subscription::updateOrCreate(
                ['paypal_subscription_id' => $result['id'] ?? null],
                [
                    'donor_id' => $session->donor_id,
                    'plan_id' => $subscriptionPlan['plan_id'],
                    'amount' => $session->amount,
                    'currency' => $session->currency,
                    'status' => 'pending',
                    'metadata' => array_merge($session->metadata ?? [], [
                        'checkout_session_id' => $session->id,
                        'provider_raw' => $result['raw'] ?? null,
                    ]),
                    'created_by' => $user?->staff?->id ?? null,
                ]
            );

            return [$session, $result['approve_url']];
        }

        $payload = [
            'amount' => $session->amount,
            'currency' => $session->currency,
            'custom_id' => $externalId,
            'description' => $validated['description'] ?? null,
            'return_url' => $returnUrlWithId,
            'cancel_url' => $cancelUrlWithId,
            'payer_email' => $session->metadata['payer_email'] ?? null,
        ];

        $result = $this->payments->createOrderCheckout($session->gateway, $payload);

        $session->update([
            'provider_session_id' => $result['id'] ?? null,
            'status' => 'redirected',
            'metadata' => array_merge($session->metadata ?? [], [
                'provider_invoice_id' => $result['id'] ?? null,
                'provider_raw' => $result['raw'] ?? null,
            ]),
        ]);

        return [$session, $result['approve_url']];
    }

    public function subscriptionPlan(?string $planKey): array
    {
        $plans = config('services.paypal.subscription_plans', []);

        if (! $planKey || ! isset($plans[$planKey])) {
            abort(422, 'Please choose a valid subscription plan.');
        }

        return $plans[$planKey];
    }
}
