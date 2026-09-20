<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReceiptAndNotify;
use App\Models\Donation;
use App\Services\Donors\ProvisionalDonorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // Verify webhook: support callback token or HMAC signature
        $callbackToken = config('services.xendit.callback_token');
        $providedToken = $request->header('X-CALLBACK-TOKEN') ?? $request->header('X-Callback-Token');

        if ($callbackToken && $providedToken !== $callbackToken) {
            return response()->json(['message' => 'Invalid callback token.'], 422);
        }

        // Alternatively verify HMAC signature if secret configured
        $secret = config('services.xendit.secret');
        $signatureHeader = $request->header('X-Xendit-Signature');
        if ($secret && $signatureHeader) {
            $payload = $request->getContent();
            $expected = hash_hmac('sha256', $payload, (string) $secret);
            if (! hash_equals($expected, (string) $signatureHeader)) {
                return response()->json(['message' => 'Invalid signature.'], 422);
            }
        }

        $payload = $request->all();
        $eventType = data_get($payload, 'type') ?? data_get($payload, 'event') ?? null;
        $resource = data_get($payload, 'data', $payload);

        // Determine status and gateway references
        $status = strtoupper((string) (data_get($resource, 'status') ?? data_get($resource, 'invoice_status') ?? ''));
        $externalId = (string) (data_get($resource, 'external_id') ?? '');
        $providerInvoiceId = (string) (data_get($resource, 'id') ?? data_get($resource, 'invoice_id') ?? '');

        if ($providerInvoiceId === '' && $externalId === '') {
            return response()->json(['message' => 'Missing resource identifier.'], 422);
        }

        // Use the provider's actual invoice ID as gateway_reference for idempotent upserts.
        // For Xendit, 'id' is the UUID of the invoice. external_id is our custom correlation value.
        $gatewayReference = $providerInvoiceId;

        // Handle paid/PAID events
        if (str_contains(strtolower($eventType ?? ''), 'paid') || $status === 'PAID') {
            // idempotent upsert by gateway + gateway_reference
            // try to find corresponding checkout session by the provider session id or external_id in metadata
            $checkoutSession = $providerInvoiceId !== ''
                ? \App\Models\CheckoutSession::where('provider_session_id', $providerInvoiceId)->first()
                : null;

            if (! $checkoutSession && $externalId !== '') {
                $checkoutSession = \App\Models\CheckoutSession::where('metadata->external_id', $externalId)->first();
            }

            if (! $checkoutSession) {
                \Illuminate\Support\Facades\Log::warning('Xendit webhook: checkout session not found.', [
                    'provider_invoice_id' => $providerInvoiceId,
                    'external_id' => $externalId,
                    'resource' => $resource,
                ]);
            }

            $amount = data_get($resource, 'amount') ?? data_get($resource, 'paid_amount');

            if (($amount === null || $amount === '') && $checkoutSession) {
                $amount = $checkoutSession->amount;
            }

            $resolvedAmount = (float) ($amount ?? 0);

            if ($resolvedAmount <= 0) {
                Log::warning('Xendit webhook: resolved amount is zero or negative, skipping donation creation.', [
                    'gateway_reference' => $gatewayReference,
                    'event_type' => $eventType,
                    'provider_invoice_id' => $providerInvoiceId,
                    'external_id' => $externalId,
                ]);

                return response()->json(['message' => 'Event ignored: invalid amount.']);
            }

            // normalize useful provider fields
            $invoiceUrl = data_get($resource, 'invoice_url') ?? data_get($resource, 'url') ?? null;
            $payerEmail = data_get($resource, 'payer_email')
                ?? data_get($resource, 'payer.email')
                ?? data_get($resource, 'payer.email_address')
                ?? data_get($resource, 'payer.payer_info.email')
                ?? null;

            // attach normalized fields to checkout session metadata when available
            if ($checkoutSession) {
                $checkoutSession->update([
                    'metadata' => array_merge($checkoutSession->metadata ?? [], array_filter([
                        'external_id' => data_get($checkoutSession->metadata, 'external_id') ?? $externalId,
                        'provider_invoice_id' => $providerInvoiceId,
                        'invoice_url' => $invoiceUrl,
                        'payer_email' => $payerEmail,
                    ], static fn ($v) => $v !== null)),
                ]);
            }

            $donationMetadata = array_merge($resource, array_filter([
                'checkout_session_id' => $checkoutSession?->id,
                'provider_invoice_id' => $providerInvoiceId,
                'external_id' => $externalId !== '' ? $externalId : null,
                'invoice_url' => $invoiceUrl,
                'payer_email' => $payerEmail,
                'anonymous' => $checkoutSession?->metadata['anonymous'] ?? null,
            ], static fn ($v) => $v !== null));

            $donation = Donation::updateOrCreate(
                [
                    'gateway' => 'xendit',
                    'gateway_reference' => $gatewayReference,
                ],
                [
                    'donor_id' => $checkoutSession?->donor_id,
                    'program_id' => $checkoutSession?->program_id,
                    'donation_type' => 'financial',
                    'amount' => $resolvedAmount,
                    'currency' => data_get($resource, 'currency') ?? config('services.xendit.currency', config('services.paypal.currency', 'USD')),
                    'transaction_date' => now(),
                    'status' => 'completed',
                    'description' => $checkoutSession?->metadata['description'] ?? null,
                    'metadata' => $donationMetadata,
                    'checkout_session_id' => $checkoutSession?->id,
                ]
            );

            // Create provisional donor FIRST, then generate receipt and notify
            if ($donation && ! $donation->donor_id) {
                $payerEmail = data_get($resource, 'payer_email')
                    ?? data_get($resource, 'payer.email')
                    ?? data_get($resource, 'payer.email_address')
                    ?? data_get($resource, 'payer.payer_info.email')
                    ?? null;

                $payerName = data_get($resource, 'payer_name') ?? data_get($resource, 'payer.name') ?? null;

                if ($payerEmail || $payerName) {
                    $service = new ProvisionalDonorService();
                    $donor = $service->createFromPayer([
                        'email' => $payerEmail,
                        'name' => $payerName,
                    ]);

                    if ($donor) {
                        $donation->donor_id = $donor->id;
                        $donation->save();
                    }
                }
            }

            if ($checkoutSession) {
                $checkoutSession->update(['status' => 'completed']);
            }

            if ($donation) {
                $donation->ensureReceiptNumber();
                GenerateReceiptAndNotify::dispatch($donation->id);
            }

        }

        // Handle refunded or cancelled statuses
        if (str_contains(strtolower($eventType ?? ''), 'refund') || $status === 'REFUNDED' || $status === 'CANCELLED') {
            Donation::where('gateway', 'xendit')
                ->where('gateway_reference', $gatewayReference)
                ->update([
                    'status' => strtolower($status) ?: 'refunded',
                    'metadata' => $resource,
                ]);

            return response()->json(['message' => 'Donation updated.']);
        }

        // Default: acknowledge
        return response()->json(['message' => 'Event ignored.']);
    }
}
