<?php

namespace App\Services\Payments;

use RuntimeException;

class PaymentManager
{
    public function driver(string $gateway): PaymentServiceInterface
    {
        $gateway = strtolower($gateway);

        return match ($gateway) {
            'paypal' => app(PayPalPaymentService::class),
            'xendit' => app(XenditPaymentService::class),
            default => throw new RuntimeException('Unsupported payment gateway: ' . $gateway),
        };
    }

    public function createOrderCheckout(string $gateway, array $data): array
    {
        return $this->driver($gateway)->createOrderCheckout($data);
    }

    public function captureOrderCheckout(string $gateway, array $data): array
    {
        return $this->driver($gateway)->captureOrderCheckout($data);
    }

    public function createSubscriptionCheckout(string $gateway, array $data): array
    {
        return $this->driver($gateway)->createSubscriptionCheckout($data);
    }
}
