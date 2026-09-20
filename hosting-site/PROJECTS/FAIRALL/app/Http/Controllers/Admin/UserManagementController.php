<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\Position;
use App\Models\Staff;
use App\Models\User;
use App\Services\GlobalAddressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use App\Notifications\StaffInviteNotification;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'staff');
        $search = $request->query('search');

        $staffQuery = User::with(['staff.department', 'staff.position'])
            ->where('user_type', 'staff');

        $beneficiaryQuery = User::with(['beneficiary.programs', 'beneficiary.educationEnrollments'])
            ->where('user_type', 'beneficiary');

        $donorQuery = User::where('user_type', 'donor');

        if ($search) {
            $searchCallback = function ($q) use ($search) {
                $q->where('login_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            };

            $staffQuery->where(function ($q) use ($search, $searchCallback) {
                $searchCallback($q);
                $q->orWhereHas('staff', fn($s) => $s->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%"));
            });

            $beneficiaryQuery->where(function ($q) use ($search, $searchCallback) {
                $searchCallback($q);
                $q->orWhereHas('beneficiary', fn($b) => $b->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%"));
            });

            $donorQuery->where(function ($q) use ($search, $searchCallback) {
                $searchCallback($q);
                $q->orWhereHas('donor', fn($d) => $d->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('organization_name', 'like', "%{$search}%"));
            });
        }

        $staffUsers = $staffQuery->latest('id')
            ->paginate(10, ['*'], 'staff_page')
            ->appends(['tab' => 'staff'] + $request->only('search'));

        $beneficiaryUsers = $beneficiaryQuery->latest('id')
            ->paginate(10, ['*'], 'beneficiary_page')
            ->appends(['tab' => 'beneficiaries'] + $request->only('search'));

        $donorUsers = $donorQuery->latest('id')
            ->paginate(10, ['*'], 'donor_page')
            ->appends(['tab' => 'donors'] + $request->only('search'));

        return view('admin.users.index', [
            'name' => Auth::user()?->display_name ?? 'Admin',
            'staffUsers' => $staffUsers,
            'beneficiaryUsers' => $beneficiaryUsers,
            'donorUsers' => $donorUsers,
            'activeTab' => $activeTab,
        ]);
    }

    public function show(User $user): View
    {
        if ($user->user_type === 'beneficiary' && $user->beneficiary) {
            return redirect('/beneficiaries/' . $user->beneficiary->id . '/profile');
        }

        if ($user->user_type === 'donor' && $user->donor) {
            return redirect('/donors/' . $user->donor->id);
        }

        $user->load(['staff.department', 'staff.position', 'staff.address']);

        return view('admin.users.show', [
            'name' => Auth::user()?->display_name ?? 'Admin',
            'user' => $user,
        ]);
    }

    public function create(): View
    {
        $roles = array_filter(Staff::ROLE_LABELS, fn(string $key): bool => $key !== Staff::ROLE_ADMINISTRATOR, ARRAY_FILTER_USE_KEY);

        $positions = Position::query()->orderBy('name')
            ->where('name', '!=', 'System Admin')
            ->get();

        return view('admin.users.create', [
            'name' => Auth::user()?->display_name ?? 'Admin',
            'departments' => Department::query()->orderBy('name')->get(),
            'positions' => $positions,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request, GlobalAddressService $addressService): RedirectResponse
    {
        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in(array_keys(Staff::ROLE_LABELS)),
            ],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'first_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'last_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'address_line' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'contact_number_code' => ['nullable', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['nullable', 'regex:/^[0-9]{6,15}$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ], [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
        ]);

        if ($validated['role'] === Staff::ROLE_ADMINISTRATOR && Staff::where('role', Staff::ROLE_ADMINISTRATOR)->exists()) {
            return back()->withErrors(['role' => 'An administrator account already exists.'])->withInput();
        }

        if ($validated['position_id']) {
            $systemAdminPositionId = Position::where('name', 'System Admin')->value('id');
            if ($systemAdminPositionId && (int) $validated['position_id'] === $systemAdminPositionId && Staff::where('position_id', $systemAdminPositionId)->exists()) {
                return back()->withErrors(['position_id' => 'A System Admin position is already assigned.'])->withInput();
            }
        }

        $currentYear = now()->year;
        $temporaryPassword = Str::password(12);

        try {
            $user = DB::transaction(function () use ($validated, $addressService, $temporaryPassword, $currentYear): User {
                $location = $addressService->validateLocation($validated);

                $user = User::create([
                    'login_id' => null,
                    'email' => $validated['email'],
                    'password' => $temporaryPassword,
                    'is_active' => true,
                    'user_type' => 'staff',
                ]);

                $staff = Staff::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'dial_code' => $validated['contact_number_code'] ?? '+63',
                    'department_id' => $validated['department_id'] ?? null,
                    'position_id' => $validated['position_id'] ?? null,
                    'role' => $validated['role'] ?? null,
                ]);

                $staff->address()->create($location);

                $user->update(['login_id' => "STAFF-{$currentYear}-{$staff->id}"]);

                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(60),
                    [
                        'id' => $user->id,
                        'hash' => sha1($user->getEmailForVerification()),
                    ]
                );

                DB::afterCommit(function () use ($user, $temporaryPassword, $verificationUrl): void {
                    $user->notify(new StaffInviteNotification($temporaryPassword, $verificationUrl));
                });

                return $user;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'users_email_unique')) {
                return back()->withErrors(['email' => 'This email is already taken.'])->withInput();
            }

            throw $e;
        }

        return redirect('/users')->with('status', 'User account created successfully. Invite email sent.');
    }

    public function edit(User $user): View
    {
        $user->load(['staff.address', 'beneficiary.programs', 'donor']);
        $addressParts = $user->staff?->address?->toFormArray() ?? [];

        if ($user->user_type === 'beneficiary') {
            return view('admin.users.edit-beneficiary', [
                'name' => Auth::user()?->display_name ?? 'Admin',
                'user' => $user,
                'beneficiary' => $user->beneficiary,
            ]);
        }

        if ($user->user_type === 'donor') {
            return view('admin.users.edit-donor', [
                'name' => Auth::user()?->display_name ?? 'Admin',
                'user' => $user,
                'donor' => $user->donor,
            ]);
        }

        return view('admin.users.edit', [
            'name' => Auth::user()?->display_name ?? 'Admin',
            'user' => $user,
            'departments' => Department::query()->orderBy('name')->get(),
            'positions' => Position::query()->orderBy('name')->get(),
            'roles' => Staff::ROLE_LABELS,
            'addressParts' => $addressParts,
        ]);
    }

    public function update(Request $request, User $user, GlobalAddressService $addressService): RedirectResponse
    {
        if (in_array($user->user_type, ['beneficiary', 'donor'], true)) {
            return $this->updateLinkedAccount($request, $user);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(array_keys(Staff::ROLE_LABELS))],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'first_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'last_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'address_line' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ], [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
        ]);

        if ($validated['role'] === Staff::ROLE_ADMINISTRATOR && Staff::where('role', Staff::ROLE_ADMINISTRATOR)->where('user_id', '!=', $user->id)->exists()) {
            return back()->withErrors(['role' => 'An administrator account already exists.'])->withInput();
        }

        if ($validated['position_id']) {
            $systemAdminPositionId = Position::where('name', 'System Admin')->value('id');
            if ($systemAdminPositionId && (int) $validated['position_id'] === $systemAdminPositionId && Staff::where('position_id', $systemAdminPositionId)->where('user_id', '!=', $user->id)->exists()) {
                return back()->withErrors(['position_id' => 'A System Admin position is already assigned.'])->withInput();
            }
        }

        $location = $addressService->validateLocation($validated);

        DB::transaction(function () use ($validated, $user, $location): void {
            $data = [
                'email' => $validated['email'],
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ];

            if (!empty($validated['password'])) {
                $data['password'] = $validated['password'];
            }

            $user->update($data);

            $staff = $user->staff()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'dial_code' => $validated['contact_number_code'] ?? '+63',
                    'department_id' => $validated['department_id'] ?? null,
                    'position_id' => $validated['position_id'] ?? null,
                    'role' => $validated['role'] ?? null,
                ]
            );

            $staff->address()->updateOrCreate([], $location);
        });

        return redirect('/users')->with('status', 'User updated successfully.');
    }

    private function updateLinkedAccount(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'login_id' => ['required', 'string', 'max:255', Rule::unique('users', 'login_id')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ]);

        $data = [
            'login_id' => $validated['login_id'],
            'email' => $validated['email'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return redirect('/users')->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return redirect('/users')->with('status', 'You cannot delete your own account.');
        }

        $user->load(['staff', 'beneficiary', 'donor']);

        if ($user->staff) $user->staff->delete();
        if ($user->beneficiary) $user->beneficiary->delete();
        if ($user->donor) $user->donor->delete();

        $user->delete();

        return back()->with('status', 'User moved to archive. Will be automatically deleted after 90 days.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $ids = $validated['ids'];

        if (in_array(Auth::id(), $ids, true)) {
            return back()->withErrors(['ids' => 'You cannot delete your own account.']);
        }

        DB::transaction(function () use ($ids): void {
            $users = User::whereIn('id', $ids)->with(['staff', 'beneficiary', 'donor'])->get();
            foreach ($users as $user) {
                if ($user->staff) $user->staff->delete();
                if ($user->beneficiary) $user->beneficiary->delete();
                if ($user->donor) $user->donor->delete();
                $user->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' users moved to archive. Will be automatically deleted after 90 days.');
    }
}
