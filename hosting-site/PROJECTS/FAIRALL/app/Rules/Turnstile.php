<?php

namespace App\Rules;

use App\Services\Turnstile\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(TurnstileService::class);

        if (! $service->verify($value)) {
            $fail('Security verification failed. Please try again.');
        }
    }
}
