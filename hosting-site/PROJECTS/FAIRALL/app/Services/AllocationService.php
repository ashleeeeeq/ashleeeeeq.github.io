<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\Allocation;
use App\Models\Donation;
use App\Models\Grant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AllocationService
{
    /**
     * Create an allocation while validating business rules.
     * $data expects: donation_id or grant_id, beneficiary_id, amount_cents, date_allocated
     */
    public function createAllocation(array $data): Allocation
    {
        return DB::transaction(function () use ($data) {
            $amount = intval($data['amount_cents'] ?? 0);
            $beneficiary = Beneficiary::with('programs')->findOrFail($data['beneficiary_id']);

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Amount must be positive.',
                ]);
            }

            if (!empty($data['donation_id'])) {
                $donation = Donation::where('id', $data['donation_id'])->lockForUpdate()->firstOrFail();
                $donation->load('program');

                if ($donation->program_id && ! $beneficiary->programs->contains('id', $donation->program_id)) {
                    throw ValidationException::withMessages([
                        'beneficiary_id' => "Selected beneficiary is not enrolled in the donation's linked program.",
                    ]);
                }

                $allocated = Allocation::where('donation_id', $donation->id)->sum('amount_cents');
                $donationCents = intval(round(floatval($donation->amount) * 100));
                $remaining = $donationCents - intval($allocated);

                if ($amount > $remaining) {
                    throw ValidationException::withMessages([
                        'amount' => 'Allocation exceeds donation remaining amount.',
                    ]);
                }
            }

            if (!empty($data['grant_id'])) {
                $grant = Grant::where('id', $data['grant_id'])->lockForUpdate()->firstOrFail();
                $grant->load('programs');

                $grantProgramIds = $grant->programs->pluck('id');

                if ($grantProgramIds->isNotEmpty() && ! $beneficiary->programs->whereIn('id', $grantProgramIds)->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'beneficiary_id' => "Selected beneficiary is not enrolled in the grant's linked program.",
                    ]);
                }

                $allocated = Allocation::where('grant_id', $grant->id)->sum('amount_cents');
                $grantCents = intval(round(floatval($grant->total_amount) * 100));
                $remaining = $grantCents - intval($allocated);

                if ($amount > $remaining) {
                    throw ValidationException::withMessages([
                        'amount' => 'Allocation exceeds grant remaining amount.',
                    ]);
                }
            }

            if (!empty($data['date_allocated'])) {
                $data['date_allocated'] = Carbon::parse($data['date_allocated'])->toDateString();
            }

            $data['created_by'] = $data['created_by'] ?? (Auth::user()?->staff?->id ?: null);

            $allocation = Allocation::create($data);

            return $allocation;
        });
    }

    /**
     * Bulk allocations for a single donation or grant source.
     * $data expects: donation_id or grant_id + allocations[] each with beneficiary_id, amount_cents, date_allocated, notes
     * Returns collection of created allocations.
     */
    public function createAllocations(array $data): \Illuminate\Support\Collection
    {
        return DB::transaction(function () use ($data) {
            $sourceIsDonation = !empty($data['donation_id']);
            $sourceIsGrant = !empty($data['grant_id']);

            if ($sourceIsDonation) {
                $donation = Donation::where('id', $data['donation_id'])->lockForUpdate()->firstOrFail();
                $donation->load('program');
                $totalCents = intval(round(floatval($donation->amount) * 100));
                $allocated = intval(Allocation::where('donation_id', $donation->id)->sum('amount_cents'));
                $remaining = $totalCents - $allocated;
            } elseif ($sourceIsGrant) {
                $grant = Grant::where('id', $data['grant_id'])->lockForUpdate()->firstOrFail();
                $grant->load('programs');
                $totalCents = intval(round(floatval($grant->total_amount) * 100));
                $allocated = intval(Allocation::where('grant_id', $grant->id)->sum('amount_cents'));
                $remaining = $totalCents - $allocated;
                $grantProgramIds = $grant->programs->pluck('id');
            } else {
                throw ValidationException::withMessages(['allocation_source' => 'Select a donation or grant.']);
            }

            $created = collect();

            foreach ($data['allocations'] as $idx => $row) {
                $beneficiary = Beneficiary::with('programs')->findOrFail($row['beneficiary_id']);
                $amount = intval($row['amount_cents'] ?? 0);

                if ($amount <= 0) {
                    throw ValidationException::withMessages([
                        "allocations.$idx.amount" => 'Amount must be positive.',
                    ]);
                }

                if ($sourceIsDonation) {
                    if ($donation->program_id && ! $beneficiary->programs->contains('id', $donation->program_id)) {
                        throw ValidationException::withMessages([
                            "allocations.$idx.beneficiary_id" => "Selected beneficiary is not enrolled in the donation's linked program.",
                        ]);
                    }
                    if ($amount > $remaining) {
                        throw ValidationException::withMessages([
                            "allocations.$idx.amount" => 'Allocation exceeds donation remaining amount (available '.number_format($remaining/100,2).').',
                        ]);
                    }
                }

                if ($sourceIsGrant) {
                    if ($grantProgramIds->isNotEmpty() && ! $beneficiary->programs->whereIn('id', $grantProgramIds)->isNotEmpty()) {
                        throw ValidationException::withMessages([
                            "allocations.$idx.beneficiary_id" => "Selected beneficiary is not enrolled in the grant's linked program.",
                        ]);
                    }
                    if ($amount > $remaining) {
                        throw ValidationException::withMessages([
                            "allocations.$idx.amount" => 'Allocation exceeds grant remaining amount (available '.number_format($remaining/100,2).').',
                        ]);
                    }
                }

                $allocation = Allocation::create([
                    'donation_id' => $data['donation_id'] ?? null,
                    'grant_id' => $data['grant_id'] ?? null,
                    'beneficiary_id' => $beneficiary->id,
                    'amount_cents' => $amount,
                    'date_allocated' => !empty($row['date_allocated']) ? Carbon::parse($row['date_allocated'])->toDateString() : Carbon::now()->toDateString(),
                    'notes' => $row['notes'] ?? null,
                    'created_by' => Auth::user()?->staff?->id,
                ]);

                $created->push($allocation);
                $remaining -= $amount;
            }

            return $created;
        });
    }
}
