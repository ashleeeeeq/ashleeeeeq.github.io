<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['donor_id','program_id','subscription_id','checkout_session_id','donation_type','gateway','gateway_reference','reference_number','receipt_number','receipt_path','amount','currency','transaction_date','status','description','metadata','created_by','updated_by'])]
class Donation extends Model
{
    use SoftDeletes;
    protected $table = 'donations';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'datetime',
            'metadata' => 'array',
            'donation_type' => 'string',
            'deleted_at' => 'datetime',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function checkoutSession(): BelongsTo
    {
        return $this->belongsTo(CheckoutSession::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    /**
     * Generate a stable human-readable receipt number for this donation.
     */
    public function generateReceiptNumber(): string
    {
        $date = ($this->transaction_date ?? $this->created_at ?? now())->format('Ymd');

        return 'RCP-' . $date . '-' . str_pad((string) $this->id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Ensure receipt_number exists and persist it once.
     */
    public function ensureReceiptNumber(): void
    {
        if (! $this->receipt_number) {
            $this->receipt_number = $this->generateReceiptNumber();
            $this->save();
        }
    }

    /**
     * Returns true if this donation was confirmed by a payment gateway
     * and should be treated as immutable by staff UIs.
     */
    public function isGatewayConfirmed(): bool
    {
        return $this->gateway !== 'manual' && strtolower((string) $this->status) === 'completed';
    }

    /**
     * Return the payer email if present in donation metadata or related checkout session.
     */
    public function payerEmail(): ?string
    {
        $meta = $this->metadata ?? [];

        $candidates = [
            data_get($meta, 'payer_email'),
            data_get($meta, 'payer.email'),
            data_get($meta, 'payer_email_address'),
            data_get($meta, 'payer_email_address.email'),
        ];

        foreach ($candidates as $c) {
            if ($c && is_string($c) && trim($c) !== '') {
                return trim($c);
            }
        }

        // fallback to checkout session metadata if available
        if ($this->checkoutSession?->metadata) {
            $cs = $this->checkoutSession->metadata;
            if (isset($cs['payer_email']) && $cs['payer_email']) {
                return trim((string) $cs['payer_email']);
            }
        }

        return null;
    }

    /**
     * Whether this donation appears to be a guest (no donor linked).
     */
    public function isGuest(): bool
    {
        return $this->donor_id === null;
    }

    /**
     * Whether the donor requested an anonymous donation (no contact info provided or opted out of receipts).
     */
    public function isAnonymous(): bool
    {
        // Explicit anonymous flag takes precedence
        $anonymous = data_get($this->metadata ?? [], 'anonymous');
        if ($anonymous === true || $anonymous === '1' || $anonymous === 1) {
            return true;
        }

        // If send_receipt is explicitly true, it's not anonymous.
        $sendReceipt = data_get($this->metadata ?? [], 'send_receipt');
        if ($sendReceipt === true || $sendReceipt === '1' || $sendReceipt === 1) {
            return false;
        }

        return $this->payerEmail() === null;
    }
}