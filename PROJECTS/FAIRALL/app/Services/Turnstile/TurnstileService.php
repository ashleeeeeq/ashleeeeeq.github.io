<?php

namespace App\Services\Turnstile;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function verify(string $token, ?string $remoteIp = null): bool
    {
        $response = Http::asForm()
            ->acceptJson()
            ->post(self::VERIFY_URL, [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $remoteIp ?? request()->ip(),
            ]);

        return $response->json('success') === true;
    }
}
