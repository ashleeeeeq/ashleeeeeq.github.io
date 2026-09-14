<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Beneficiary;
use App\Models\Concerns\HasFormattedContact;
use App\Models\Concerns\HasPhoneNumber;
use App\Models\Program;

class Staff extends Model
{
    use HasFactory, Notifiable, HasFormattedContact, HasPhoneNumber, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'dial_code',
        'department_id',
        'position_id',
        'role',
    ];

    public const ROLE_ADMINISTRATOR = 'administrator';
    public const ROLE_PROGRAM_STAFF = 'program_staff';
    public const ROLE_ADMIN_FINANCE_STAFF = 'admin_finance_staff';
    public const ROLE_PROGRAM_MANAGER = 'program_manager';
    public const ROLE_DONOR_MANAGER = 'donor_manager';
    public const ROLE_EXECUTIVE_DIRECTOR = 'executive_director';

    public const ROLE_LABELS = [
        self::ROLE_ADMINISTRATOR => 'Administrator',
        self::ROLE_PROGRAM_STAFF => 'Program Staff',
        self::ROLE_ADMIN_FINANCE_STAFF => 'Admin/Finance Staff',
        self::ROLE_PROGRAM_MANAGER => 'Program Manager',
        self::ROLE_DONOR_MANAGER => 'Donor Manager',
        self::ROLE_EXECUTIVE_DIRECTOR => 'Executive Director',
    ];

    // relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function hasAnyPosition(array $positions): bool
    {
        return in_array($this->position->name, $positions);
    }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? 'Staff';
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function getDisplayNameAttribute(): string
    {
        return Str::of($this->full_name)->squish()->title()->value();
    }

    protected function normalizedDepartment(): string
    {
        return Str::of((string) $this->department?->name)->lower()->trim()->value();
    }

    protected function normalizedPosition(): string
    {
        return Str::of((string) $this->position?->name)->lower()->trim()->value();
    }

    public function matchesProgram(Beneficiary $beneficiary): bool
    {
        if (!$this->department || $beneficiary->programs->isEmpty()) {
            return false;
        }

        $dept = Str::lower(trim((string) $this->department->name));

        return $beneficiary->programs->contains(function ($p) use ($dept) {
            return $dept === Str::lower(trim((string) $p->program_name));
        });
    }

    public function matchesProgramModel(Program $program): bool
    {
        if (!$this->department || !$program) {
            return false;
        }

        return Str::lower(trim((string) $this->department->name)) === Str::lower(trim((string) $program->program_name));
    }

    public function staffProgram(): Program|null
    {
        if (!$this->department) {
            return null;
        }

        return Program::where('program_name', $this->department->name)->first();
    }
}
