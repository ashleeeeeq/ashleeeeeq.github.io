<?php

namespace App\Services\Donors;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Support\Str;

class ProvisionalDonorService
{
    /**
     * Create or find a provisional donor from payer information.
     * Accepts array with keys: email, name, contact_number
     */
    public function createFromPayer(array $payer): ?Donor
    {
        $email = isset($payer['email']) ? trim((string) $payer['email']) : null;
        $name = isset($payer['name']) ? trim((string) $payer['name']) : null;
        $contact = isset($payer['contact_number']) ? trim((string) $payer['contact_number']) : '';

        if ($email === null || $email === '') {
            return null;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'email' => $email,
                'password' => Str::random(32),
                'user_type' => 'donor',
            ]);
        }

        // If donor already exists for user, return it
        if ($user->donor) {
            return $user->donor;
        }

        // Try to split name into first/last
        $first = null;
        $last = null;

        if ($name) {
            $parts = preg_split('/\s+/', $name);
            $first = $parts[0] ?? null;
            $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null;
        }

        // If no name, use local-part of email
        if (! $first && $email) {
            $first = explode('@', $email)[0];
        }

        $donor = Donor::create([
            'user_id' => $user->id,
            'organization_name' => null,
            'first_name' => $first,
            'middle_name' => null,
            'last_name' => $last,
            'contact_number' => $contact ?? '',
            'dial_code' => '+63',
            'donor_type' => 'individual',
        ]);

        return $donor;
    }
}
