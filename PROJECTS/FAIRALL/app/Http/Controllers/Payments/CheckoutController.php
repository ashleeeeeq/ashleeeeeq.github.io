<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReceiptAndNotify;
use App\Models\CheckoutSession;
use App\Models\Donation;
use App\Services\Payments\CheckoutService;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function create(Request $request, CheckoutService $checkout)
    {
        $validated = $request->validate([
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'donation_frequency' => ['required', 'in:one_time,subscription'],
            'subscription_plan' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'gateway' => ['nullable', 'in:xendit,paypal'],
            'currency' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'payer_email' => ['nullable', 'email', 'required_if:send_receipt,1'],
            'send_receipt' => ['nullable', 'in:1'],
            'anonymous' => ['nullable', 'in:1'],
            'return_url' => ['nullable', 'string'],
            'cancel_url' => ['nullable', 'string'],
        ]);

        [$session, $approveUrl] = $checkout->createCheckoutSession(
            $validated,
            Auth::check() ? Auth::user() : null,
        );

        return redirect()->away($approveUrl);
    }

    public function handleReturn(Request $request, PaymentManager $payments)
    {
        session()->flash('skip_loader', true);
        $checkoutId = $request->query('checkout_id');
        if (! $checkoutId) {
            return redirect('/donate')->with('status', 'Missing checkout identifier.');
        }

        $session = CheckoutSession::find($checkoutId);
        if (! $session) {
            return redirect('/donate')->with('status', 'Checkout session not found.');
        }

        if (($session->metadata['donation_frequency'] ?? 'one_time') === 'subscription') {
            $session->update(['status' => 'initiated']);

            return $this->redirectDonor('Subscription setup initiated. We will notify you when it is activated.');
        }

        if (! $session->provider_session_id) {
            $session->update(['status' => 'initiated']);

            return $this->redirectDonor('Payment initiated. We will notify you when it is complete.');
        }

        // For Xendit we rely on webhooks to confirm final invoice/payment status
        if ($session->gateway === 'xendit') {
            $session->update(['status' => 'initiated']);

            return $this->redirectDonor('Payment initiated. We will notify you when it is complete.');
        }

        try {
            $capture = $payments->captureOrderCheckout($session->gateway, [
                'order_id' => $session->provider_session_id,
            ]);

            $gatewayReference = (string) ($capture['order_id'] ?? $capture['id'] ?? $session->provider_session_id);

            $donation = Donation::updateOrCreate(
                [
                    'gateway' => $session->gateway,
                    'gateway_reference' => $gatewayReference,
                ],
                [
                    'donor_id' => $session->donor_id,
                    'program_id' => $session->program_id,
                    'donation_type' => 'financial',
                    'amount' => $capture['amount'] ?? $session->amount,
                    'currency' => $capture['currency'] ?? $session->currency,
                    'transaction_date' => now(),
                    'status' => 'completed',
                    'description' => $session->metadata['description'] ?? null,
                    'metadata' => array_merge($session->metadata ?? [], ['provider_raw' => $capture['raw'] ?? null]),
                    'checkout_session_id' => $session->id,
                ]
            );

            $session->update([
                'status' => 'completed',
                'metadata' => array_merge($session->metadata ?? [], ['capture_raw' => $capture['raw'] ?? null]),
            ]);

            $donation->ensureReceiptNumber();
            GenerateReceiptAndNotify::dispatch($donation->id);

            if ($donation->wasRecentlyCreated) {
                return $this->redirectDonor('Donation completed. Thank you.');
            }

            return $this->redirectDonor('Donation completed. Thank you.');
        } catch (\Throwable $exception) {
            Log::error('Failed to capture PayPal order on return.', [
                'checkout_session_id' => $session->id,
                'gateway' => $session->gateway,
                'provider_session_id' => $session->provider_session_id,
                'error' => $exception->getMessage(),
            ]);

            $session->update(['status' => 'initiated']);

            return $this->redirectDonor('Payment initiated. We will notify you when it is complete.');
        }
    }

    public function handleCancel(Request $request)
    {
        session()->flash('skip_loader', true);
        $checkoutId = $request->query('checkout_id');
        $session = null;

        if ($checkoutId) {
            $session = CheckoutSession::find($checkoutId);
            if ($session) {
                $session->update(['status' => 'cancelled']);
            }
        }

        return $this->redirectDonor('Donation cancelled.');
    }

    private function redirectDonor(string $message): RedirectResponse
    {
        return Auth::check() && Auth::user()->user_type === 'donor'
            ? redirect()->route('donor.portal.donations.index')->with('status', $message)
            : redirect('/donate')->with('status', $message);
    }

    public function xenditWebhook(Request $request)
    {
        // Placeholder for Xendit webhook handling. Implement webhook verification and donation creation.
        Log::info('Received Xendit webhook', ['payload' => $request->all()]);

        return response()->json(['message' => 'ok']);
    }

}
