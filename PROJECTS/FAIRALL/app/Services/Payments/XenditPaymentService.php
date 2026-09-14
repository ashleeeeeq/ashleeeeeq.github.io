<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class XenditPaymentService implements PaymentServiceInterface
{
    public function baseUrl(): string
    {
        return rtrim((string) config('services.xendit.base_url', 'https://api.xendit.co'), '/');
    }

    public function createOrderCheckout(array $data): array
    {
        $payload = [
            'external_id' => $data['custom_id'] ?? ('donation-' . uniqid()),
            'amount' => number_format((float) $data['amount'], 2, '.', ''),
            'success_redirect_url' => $data['return_url'] ?? url('/donate'),
            'failure_redirect_url' => $data['cancel_url'] ?? url('/donate'),
        ];

        if (filled($data['description'] ?? null)) {
            $payload['description'] = $data['description'];
        }

        if (filled($data['payer_email'] ?? null)) {
            $payload['payer_email'] = $data['payer_email'];
        }

        $response = Http::withBasicAuth((string) config('services.xendit.key'), '')->post($this->baseUrl() . '/v2/invoices', $payload);

        if (! $response->successful()) {
            Log::error('Xendit invoice creation failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => [
                    'external_id' => $data['custom_id'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'description' => $data['description'] ?? null,
                    'return_url' => $data['return_url'] ?? null,
                    'cancel_url' => $data['cancel_url'] ?? null,
                ],
            ]);

            throw new RuntimeException('Unable to create Xendit invoice: HTTP ' . $response->status() . '.');
        }

        $json = $response->json();

        return [
            'id' => data_get($json, 'id') ?? data_get($json, 'external_id'),
            'status' => data_get($json, 'status'),
            'approve_url' => data_get($json, 'invoice_url') ?? data_get($json, 'url'),
            'raw' => $json,
        ];
    }

    public function createSubscriptionCheckout(array $data): array
    {
        // Xendit does not manage PayPal-like subscriptions here; stubbed.
        throw new RuntimeException('Xendit subscription creation not implemented.');
    }

    public function captureOrderCheckout(array $data): array
    {
        $id = $data['id'] ?? $data['external_id'] ?? null;

        if (! $id) {
            throw new RuntimeException('Xendit capture requires an invoice id or external_id.');
        }

        $response = Http::withBasicAuth((string) config('services.xendit.key'), '')->get($this->baseUrl() . '/v2/invoices/' . urlencode($id));

        if (! $response->successful()) {
            Log::error('Xendit invoice fetch failed.', [
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Unable to fetch Xendit invoice: HTTP ' . $response->status() . '.');
        }

        $json = $response->json();

        $status = strtoupper((string) (data_get($json, 'status') ?? data_get($json, 'invoice_status') ?? ''));

        // Normalize to app statuses
        $normalized = match ($status) {
            'PAID', 'SETTLED', 'PAID_SETTLED' => 'completed',
            'EXPIRED' => 'expired',
            'CANCELLED' => 'cancelled',
            'REFUNDED' => 'refunded',
            default => strtolower($status) ?: 'pending',
        };

        return [
            'id' => data_get($json, 'id') ?? data_get($json, 'external_id'),
            'external_id' => data_get($json, 'external_id'),
            'status' => $normalized,
            'amount' => data_get($json, 'amount') ?? data_get($json, 'paid_amount'),
            'approve_url' => data_get($json, 'invoice_url') ?? data_get($json, 'url'),
            'raw' => $json,
        ];
    }

    public function verifyWebhook(Request $request): bool
    {
        // Xendit provides signature verification; implement as needed.
        return true;
    }
}
