<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckoutSession;
use App\Services\Payments\CheckoutService;
use App\Services\Payments\PaymentManager;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkout,
        private PaymentManager $payments,
    ) {}

    /**
     * Create a checkout session and return the payment provider's approval URL.
     *
     * Flutter: On success (201), open the approve_url in the system browser
     * via url_launcher. Do NOT use a WebView — PayPal/Xendit may reject it.
     * After the user completes payment, poll GET /api/donor/checkout/{id}.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'donation_frequency' => ['required', 'in:one_time,subscription'],
            'subscription_plan' => ['nullable', 'string', 'required_if:donation_frequency,subscription'],
            'amount' => ['nullable', 'numeric', 'min:0.01', 'required_if:donation_frequency,one_time'],
            'gateway' => ['nullable', 'in:xendit,paypal', 'required_if:donation_frequency,one_time'],
            'currency' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        [$session, $approveUrl] = $this->checkout->createCheckoutSession(
            $validated,
            $request->user(),
        );

        return response()->json([
            'success' => true,
            'data' => [
                'checkout_id' => $session->id,
                'approve_url' => $approveUrl,
                'provider_session_id' => $session->provider_session_id,
                'status' => $session->status,
            ],
        ], 201);
    }

    /**
     * Poll the checkout/donation status after the user completes payment.
     *
     * Flutter: Call this every 2-3 seconds after opening approve_url.
     * When donation is not null, the flow is complete.
     * States: redirected → initiated → completed | cancelled | expired
     */
    public function show(string $id): JsonResponse
    {
        $session = CheckoutSession::query()
            ->with('donation')
            ->where('id', $id)
            ->where('donor_id', request()->user()?->donor?->id)
            ->firstOrFail();

        // If still waiting and enough time has passed, check the provider directly
        if ($session->status === 'redirected' && $session->created_at->diffInSeconds(Carbon::now()) > 15) {
            $this->syncWithProvider($session);
            $session->refresh();
        }

        $donation = null;
        if ($session->donation) {
            $donation = [
                'id' => $session->donation->id,
                'reference_number' => $session->donation->reference_number,
                'gateway_reference' => $session->donation->gateway_reference,
                'receipt_number' => $session->donation->receipt_number,
                'amount' => $session->donation->amount,
                'currency' => $session->donation->currency,
                'status' => $session->donation->status,
                'donation_type' => $session->donation->donation_type,
                'gateway' => $session->donation->gateway,
                'program' => $session->donation->program
                    ? ['id' => $session->donation->program->id, 'name' => $session->donation->program->program_name]
                    : null,
                'has_receipt' => $session->donation->receipts()->exists(),
                'transaction_date' => $session->donation->transaction_date?->toIso8601String(),
                'created_at' => $session->donation->created_at->toIso8601String(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $session->status,
                'donation' => $donation,
            ],
        ]);
    }

    private function syncWithProvider(CheckoutSession $session): void
    {
        try {
            $result = $this->payments->captureOrderCheckout($session->gateway, [
                'id' => $session->provider_session_id,
            ]);

            if ($result['status'] === 'completed') {
                $session->update(['status' => 'completed']);
                Log::info('Checkout session synced with provider as completed.', [
                    'checkout_id' => $session->id,
                ]);
            } elseif (in_array($result['status'], ['expired', 'cancelled'])) {
                $session->update(['status' => $result['status']]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to sync checkout session with provider.', [
                'checkout_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
