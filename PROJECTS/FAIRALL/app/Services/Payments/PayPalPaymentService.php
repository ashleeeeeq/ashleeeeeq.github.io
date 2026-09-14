<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayPalPaymentService implements PaymentServiceInterface
{
    public function baseUrl(): string
    {
        return rtrim((string) config('services.paypal.base_url'), '/');
    }

    public function currency(): string
    {
        return (string) config('services.paypal.currency', 'USD');
    }

    public function captureOrderCheckout(array $data): array
    {
        $orderId = (string) ($data['order_id'] ?? '');

        if ($orderId === '') {
            throw new RuntimeException('Missing PayPal order identifier.');
        }

        $response = $this->paypal()->withBody('{}', 'application/json')
            ->post($this->baseUrl() . '/v2/checkout/orders/' . $orderId . '/capture');

        if (! $response->successful()) {
            throw new RuntimeException('Unable to capture PayPal order checkout: ' . $response->status() . ' ' . $response->body());
        }

        $json = $response->json();
        $capture = data_get($json, 'purchase_units.0.payments.captures.0', []);

        return [
            'id' => data_get($capture, 'id') ?: data_get($json, 'id'),
            'order_id' => data_get($json, 'id'),
            'status' => data_get($capture, 'status') ?: data_get($json, 'status'),
            'amount' => data_get($capture, 'amount.value') ?: data_get($json, 'purchase_units.0.amount.value'),
            'currency' => data_get($capture, 'amount.currency_code') ?: data_get($json, 'purchase_units.0.amount.currency_code'),
            'raw' => $json,
        ];
    }

    public function createSubscriptionCheckout(array $data): array
    {
        $payload = [
            'plan_id' => $data['plan_id'],
            'custom_id' => $data['custom_id'] ?? null,
            'application_context' => [
                'brand_name' => config('app.name', 'App'),
                'locale' => 'en-US',
                'user_action' => 'SUBSCRIBE_NOW',
                'return_url' => $data['return_url'] ?? url('/donate/return'),
                'cancel_url' => $data['cancel_url'] ?? url('/donate/cancel'),
            ],
        ];

        $response = $this->paypal()->post($this->baseUrl() . '/v1/billing/subscriptions', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Unable to create PayPal subscription checkout.');
        }

        return [
            'id' => data_get($response->json(), 'id'),
            'status' => data_get($response->json(), 'status'),
            'approve_url' => data_get(
                collect(data_get($response->json(), 'links', []))->firstWhere('rel', 'approve'),
                'href'
            ),
            'raw' => $response->json(),
        ];
    }

    public function createOrderCheckout(array $data): array
    {
        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $data['currency'] ?? $this->currency(),
                    'value' => number_format((float) $data['amount'], 2, '.', ''),
                ],
                'custom_id' => $data['custom_id'] ?? null,
            ]],
            'application_context' => [
                'brand_name' => config('app.name', 'App'),
                'locale' => 'en-US',
                'user_action' => 'PAY_NOW',
                'return_url' => $data['return_url'] ?? url('/donate/return'),
                'cancel_url' => $data['cancel_url'] ?? url('/donate/cancel'),
            ],
        ];

        $response = $this->paypal()->post($this->baseUrl() . '/v2/checkout/orders', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Unable to create PayPal order checkout.');
        }

        return [
            'id' => data_get($response->json(), 'id'),
            'status' => data_get($response->json(), 'status'),
            'approve_url' => data_get(
                collect(data_get($response->json(), 'links', []))->firstWhere('rel', 'approve'),
                'href'
            ),
            'raw' => $response->json(),
        ];
    }

    public function verifyWebhook(Request $request): bool
    {
        $webhookId = config('services.paypal.webhook_id');

        if (! $webhookId) {
            return false;
        }

        $event = json_decode($request->getContent(), true);

        if (! is_array($event)) {
            $event = $request->all();
        }

        $payload = [
            'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
            'cert_url' => $request->header('PAYPAL-CERT-URL'),
            'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
            'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
            'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
            'webhook_id' => $webhookId,
            'webhook_event' => $event,
        ];

        $response = $this->paypal()->post($this->baseUrl() . '/v1/notifications/verify-webhook-signature', $payload);

        return $response->successful() && data_get($response->json(), 'verification_status') === 'SUCCESS';
    }

    public function accessToken(): string
    {
        return Cache::remember('paypal.access_token', now()->addMinutes(50), function (): string {
            $response = Http::withBasicAuth((string) config('services.paypal.client_id'), (string) config('services.paypal.secret'))
                ->asForm()
                ->post($this->baseUrl() . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $response->successful()) {
                throw new RuntimeException('Unable to fetch PayPal access token.');
            }

            return (string) $response->json('access_token');
        });
    }

    protected function paypal()
    {
        return Http::withToken($this->accessToken())
            ->acceptJson()
            ->asJson();
    }

    public function cancelSubscription(string $subscriptionId, ?string $reason = null): bool
    {
        $subscriptionId = trim($subscriptionId);

        if ($subscriptionId === '') {
            throw new RuntimeException('Missing PayPal subscription identifier.');
        }

        $payload = [];
        if ($reason !== null && trim($reason) !== '') {
            $payload['reason'] = $reason;
        }

        $response = $this->paypal()
            ->withBody(json_encode($payload), 'application/json')
            ->post($this->baseUrl() . '/v1/billing/subscriptions/' . $subscriptionId . '/cancel');

        return $response->successful() || $response->status() === 204;
    }
}
