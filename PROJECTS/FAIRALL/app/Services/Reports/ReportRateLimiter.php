<?php

namespace App\Services\Reports;

use App\Models\Report;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportRateLimiter
{
    public const LIMIT = 5;

    public const WINDOW_HOURS = 24;

    public static function isExempt(?User $user): bool
    {
        $staff = $user?->staff;
        if (!$staff) {
            // Ensure staff relation is loaded if needed
            if ($user && $user->relationLoaded('staff')) {
                $staff = $user->getRelation('staff');
            } else {
                $user?->loadMissing('staff.position');
                $staff = $user?->staff;
            }
        } else {
            // Ensure position is loaded for hasAnyPosition check
            if (!$staff->relationLoaded('position')) {
                $staff->loadMissing('position');
            }
        }

        if (!$staff) {
            return false;
        }

        // Role check: administrator
        if ($staff->hasAnyRole([Staff::ROLE_ADMINISTRATOR])) {
            return true;
        }

        // Position check: System Admin (case-insensitive)
        if (strcasecmp(trim((string) ($staff->position?->name ?? '')), 'System Admin') === 0) {
            return true;
        }

        // Fallback via helper if position relation is available
        try {
            if ($staff->hasAnyPosition(['System Admin'])) {
                return true;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return false;
    }

    /**
     * Return quota info for a user.
     *
     * @return array{used:int, remaining:int, limit:int, resetAt:?Carbon, retryAfter:int, is_exempt:bool, isExempt:bool}
     */
    public static function quotaFor(?User $user): array
    {
        if (!$user) {
            return [
                'used' => 0,
                'remaining' => self::LIMIT,
                'limit' => self::LIMIT,
                'resetAt' => null,
                'retryAfter' => 0,
                'is_exempt' => false,
                'isExempt' => false,
            ];
        }

        if (self::isExempt($user)) {
            return [
                'used' => 0,
                'remaining' => self::LIMIT,
                'limit' => self::LIMIT,
                'resetAt' => null,
                'retryAfter' => 0,
                'is_exempt' => true,
                'isExempt' => true,
            ];
        }

        $windowStart = now()->subHours(self::WINDOW_HOURS);

        /** @var \Illuminate\Database\Eloquent\Collection<int, Report> $recent */
        $recent = Report::withTrashed()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $windowStart)
            ->orderBy('created_at', 'asc')
            ->get(['created_at']);

        $used = $recent->count();
        $remaining = max(0, self::LIMIT - $used);

        $resetAt = null;
        $retryAfter = 0;

        if ($recent->isNotEmpty()) {
            if ($used >= self::LIMIT) {
                // Sliding window: next slot frees when the (used - LIMIT + 1)th oldest expires
                // e.g. used=5 => oldest+24, used=20 => 16th report+24
                $thresholdIndex = $used - self::LIMIT;
                $thresholdReport = $recent->get($thresholdIndex);
                $base = $thresholdReport?->created_at ?? $recent->first()->created_at;
                $resetAt = (clone $base)->addHours(self::WINDOW_HOURS);
                $retryAfter = max(0, $resetAt->getTimestamp() - now()->getTimestamp());
            } else {
                // Not yet limited — show when oldest will slide out (informational)
                $oldestInWindow = $recent->first()->created_at;
                $resetAt = (clone $oldestInWindow)->addHours(self::WINDOW_HOURS);
                $retryAfter = max(0, $resetAt->getTimestamp() - now()->getTimestamp());
            }
        }

        return [
            'used' => $used,
            'remaining' => $remaining,
            'limit' => self::LIMIT,
            'resetAt' => $resetAt,
            'retryAfter' => $retryAfter,
            'is_exempt' => false,
            'isExempt' => false,
        ];
    }

    public static function isLimited(?User $user): bool
    {
        if (self::isExempt($user)) {
            return false;
        }

        return self::quotaFor($user)['remaining'] <= 0;
    }

    /**
     * For controller guard — returns 429 response payload if limited.
     */
    public static function limitResponse(?User $user): ?array
    {
        if (self::isExempt($user)) {
            return null;
        }

        $quota = self::quotaFor($user);

        if ($quota['remaining'] > 0) {
            return null;
        }

        return [
            'error' => 'Report generation limit reached (5 per 24 hours). Try again ' . ($quota['resetAt'] ? $quota['resetAt']->diffForHumans() : 'later') . '.',
            'retry_after' => $quota['retryAfter'],
            'reset_at' => $quota['resetAt']?->toIso8601String(),
            'limit' => $quota['limit'],
            'remaining' => 0,
        ];
    }
}
