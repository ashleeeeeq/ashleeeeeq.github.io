<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;

interface PaymentServiceInterface
{
    public function createOrderCheckout(array $data): array;

    public function captureOrderCheckout(array $data): array;

    public function createSubscriptionCheckout(array $data): array;

    public function verifyWebhook(Request $request): bool;
}
