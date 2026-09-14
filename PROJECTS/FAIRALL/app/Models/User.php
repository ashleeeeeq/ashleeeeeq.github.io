<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['login_id', 'email', 'password', 'is_active', 'user_type', 'avatar_path', 'password_changed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // relationships

    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    public function beneficiary(): HasOne
    {
        return $this->hasOne(Beneficiary::class);
    }

    public function donor(): HasOne
    {
        return $this->hasOne(Donor::class);
    }

    public function getFullNameAttribute(): string
    {
        $fullName = trim((string) ($this->staff?->full_name ?? $this->beneficiary?->full_name ?? $this->donor?->full_name ?? ''));

        return $fullName !== '' ? $fullName : (string) $this->email;
    }

    public function getDisplayNameAttribute(): string
    {
        return Str::of($this->full_name)->squish()->title()->value();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            try {
                $disk = Storage::disk('s3');

                // Prefer a temporary signed URL for private objects
                if (is_callable([$disk, 'temporaryUrl'])) {
                    return $disk->temporaryUrl($this->avatar_path, now()->addDays(3));
                }
            } catch (\Throwable $e) {
                // swallow and fallback
            }

            $baseUrl = (string) config('filesystems.disks.s3.url');

            if ($baseUrl !== '') {
                return rtrim($baseUrl, '/') . '/' . ltrim($this->avatar_path, '/');
            }

            try {
                return Storage::disk('s3')->url($this->avatar_path);
            } catch (\Throwable $e) {
                // swallow and fallthrough to local asset
            }
        }

        return asset('images/default-avatar.svg');
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        if ($this->hasVerifiedEmail()) {
            return false;
        }

        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    public function getEmailForVerification(): string
    {
        return (string) $this->email;
    }

    public static function getAdminAndEdUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('staff', fn($q) =>
            $q->whereIn('role', [Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR])
        )->get();
    }
}
