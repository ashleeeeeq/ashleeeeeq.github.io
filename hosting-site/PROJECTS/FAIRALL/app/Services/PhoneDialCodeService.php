<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PhoneDialCodeService
{
    public function getDialCodes(): array
    {
        return Cache::rememberForever('dial_codes', function () {
            $path = public_path('data/dial-codes.json');

            if (!file_exists($path)) {
                return [];
            }

            return json_decode(file_get_contents($path), true) ?? [];
        });
    }
}
