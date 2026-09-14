<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DonationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any donations.
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user->staff;
    }

    /**
     * Determine whether the user can view the donation.
     */
    public function view(User $user, Donation $donation): bool
    {
        return (bool) $user->staff;
    }

    /**
     * Determine whether the user can create donations (manual/staff-created).
     */
    public function create(User $user): bool
    {
        return (bool) $user->staff;
    }

    /**
     * Determine whether the user can update the donation.
     * Gateway-confirmed (provider) donations are read-only.
     */
    public function update(User $user, Donation $donation): bool
    {
        if (! $user->staff) {
            return false;
        }

        // Allow update only for non-provider donations (manual/offline) or when gateway is not set
        return ! $donation->isGatewayConfirmed();
    }

    /**
     * Determine whether the user can delete the donation.
     */
    public function delete(User $user, Donation $donation): bool
    {
        if (! $user->staff) {
            return false;
        }

        return ! $donation->isGatewayConfirmed();
    }
}
