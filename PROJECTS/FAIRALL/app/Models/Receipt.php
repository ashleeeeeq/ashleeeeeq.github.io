<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['donation_id','path','hash','sent_at'])]
class Receipt extends Model
{
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public static function computeHashForDonation(Donation $donation): string
    {
        $parts = [
            $donation->id,
            $donation->receipt_number ?? '',
            (string) number_format($donation->amount, 2, '.', ''),
            $donation->currency ?? '',
        ];

        return hash('sha256', implode('|', $parts));
    }
}
