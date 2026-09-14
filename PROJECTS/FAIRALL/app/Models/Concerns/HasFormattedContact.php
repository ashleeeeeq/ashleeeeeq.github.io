<?php

namespace App\Models\Concerns;

trait HasFormattedContact
{
    public function getFormattedContactAttribute(): string
    {
        $dialCode = $this->dial_code ?? '+63';
        $number = $this->contact_number ?? '';

        if (empty($number)) {
            return $dialCode;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $number);

        if (strlen($cleaned) === 10) {
            $cleaned = substr($cleaned, 0, 3) . ' ' . substr($cleaned, 3, 3) . ' ' . substr($cleaned, 6);
        } elseif (strlen($cleaned) === 7) {
            $cleaned = substr($cleaned, 0, 3) . ' ' . substr($cleaned, 3);
        }

        return trim($dialCode . ' ' . $cleaned);
    }
}
