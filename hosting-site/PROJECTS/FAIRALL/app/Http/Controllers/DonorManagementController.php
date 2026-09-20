<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Donor;
use App\Models\User;
use App\Notifications\DonorInviteNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DonorManagementController extends Controller
{
    public function create(): View
    {
        return view('donor-management.create', [
            'name' => auth()->user()?->display_name ?? 'Staff',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('donor_type');

        if (!in_array($type, ['individual', 'organization'], true)) {
            abort(422, 'Invalid donor type');
        }

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'donor_type' => ['required', 'in:individual,organization'],
            'first_name' => ['required_if:donor_type,individual', 'nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'last_name' => ['required_if:donor_type,individual', 'nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'organization_name' => ['required_if:donor_type,organization', 'nullable', 'string', 'max:100'],
        ], [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
        ]);

        $temporaryPassword = Str::password(12);
        $currentYear = now()->year;
        $staffId = Auth::user()?->staff?->id;

        try {
            $user = DB::transaction(function () use ($validated, $type, $temporaryPassword, $currentYear, $staffId): User {
                $user = User::create([
                    'login_id' => null,
                    'email' => $validated['email'],
                    'password' => $temporaryPassword,
                    'is_active' => true,
                    'user_type' => 'donor',
                    'email_verified_at' => null,
                ]);

                $donorData = [
                    'user_id' => $user->id,
                    'contact_number' => $validated['contact_number'],
                    'dial_code' => $validated['contact_number_code'] ?? '+63',
                    'donor_type' => $type,
                    'created_by' => $staffId,
                    'updated_by' => $staffId,
                ];

                if ($type === 'individual') {
                    $donorData['first_name'] = $validated['first_name'];
                    $donorData['middle_name'] = $validated['middle_name'] ?? null;
                    $donorData['last_name'] = $validated['last_name'];
                } else {
                    $donorData['organization_name'] = $validated['organization_name'];
                }

                $donor = Donor::create($donorData);

                $user->update([
                    'login_id' => "DON-{$currentYear}-{$donor->id}",
                ]);

                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(60),
                    [
                        'id' => $user->id,
                        'hash' => sha1($user->getEmailForVerification()),
                    ]
                );

                DB::afterCommit(function () use ($user, $temporaryPassword, $verificationUrl): void {
                    $user->notify(new DonorInviteNotification($temporaryPassword, $verificationUrl));
                });

                return $user;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle race condition: duplicate email from concurrent submission
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'users_email_unique')) {
                return back()->withErrors(['email' => 'This email is already taken.'])->withInput();
            }

            throw $e;
        }

        return redirect('/donors')
            ->with('status', 'Donor created successfully and invite email sent to ' . $user->email . '.');
    }

    public function index(Request $request): View
    {
        $query = Donor::query()->with(['user'])->withCount(['deliverables', 'donations']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('organization_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('login_id', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('donor_type')) {
            $query->where('donor_type', $request->query('donor_type'));
        }

        $donors = $query->latest('id')->paginate(10)->withQueryString();

        return view('donor-management.index', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'donors' => $donors,
        ]);
    }

    public function show(Request $request, Donor $donor): View
    {
        $activeTab = $request->query('tab', 'metrics');

        if (!in_array($activeTab, ['metrics', 'donations', 'deliverables', 'allocations'], true)) {
            $activeTab = 'metrics';
        }

        $donor->load(['user'])->loadCount(['donations', 'deliverables']);

        $totalDonated = (float) $donor->donations()->sum('amount');
        $totalAllocatedCents = (int) Allocation::whereHas('donation', fn($q) => $q->where('donor_id', $donor->id))->sum('amount_cents');
        $totalAllocated = $totalAllocatedCents / 100;
        $donationsCount = $donor->donations_count;
        $averageDonation = $donationsCount > 0 ? $totalDonated / $donationsCount : 0.0;

        $firstDonationDate = $donor->donations()->min('transaction_date');
        $lastDonationDate = $donor->donations()->max('transaction_date');

        // donation frequency: compute average days between donations
        $avgDaysBetweenDonations = null;

        if ($donationsCount > 1 && $firstDonationDate && $lastDonationDate) {
            $first = Carbon::parse($firstDonationDate);
            $last = Carbon::parse($lastDonationDate);

            $daysSpan = max(1, $first->diffInDays($last));

            $avgDaysBetweenDonations = $daysSpan / ($donationsCount - 1);
        }

        // next deliverable due: next upcoming end_date for incomplete deliverables
        $nextDeliverable = $donor->deliverables()
            ->where('progress', '<', 100)
            ->whereNotNull('end_date')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('end_date')
            ->first();
        $nextDeliverableDue = $nextDeliverable?->end_date ?? null;

        // deliverable counts
        $overdueDeliverablesCount = $donor->deliverables()
            ->where('progress', '<', 100)
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', now()->toDateString())
            ->count();

        $ongoingDeliverablesCount = $donor->deliverables()
            ->where('progress', '<', 100)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('start_date')->whereDate('start_date', '<=', now()->toDateString());
                })->where(function ($q3) {
                    $q3->whereNull('end_date')->orWhereDate('end_date', '>=', now()->toDateString());
                });
            })
            ->count();

        $donations = null;
        $deliverables = null;
        $sources = null;

        if ($activeTab === 'donations') {
            $donations = $donor->donations()->latest('id')->paginate(10)->withQueryString();
        } elseif ($activeTab === 'deliverables') {
            $deliverables = $donor->deliverables()->orderBy('start_date')->orderBy('end_date')->paginate(10)->withQueryString();
        } elseif ($activeTab === 'allocations') {
            $sources = $donor->donations()
                ->whereHas('allocations')
                ->with(['allocations' => fn($q) => $q->with('beneficiary')->orderBy('date_allocated')->orderBy('id')])
                ->latest('id')
                ->paginate(5)
                ->withQueryString();
        }

        return view('donor-management.show', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'donor' => $donor,
            'activeTab' => $activeTab,
            'metrics' => [
                'donations_count' => $donor->donations_count,
                'deliverables_count' => $donor->deliverables_count,
                'total_donated' => $totalDonated,
                'total_allocated' => $totalAllocated,
                'total_allocated_pct' => $totalDonated > 0 ? min(100, round(($totalAllocated / $totalDonated) * 100)) : 0,
                'remaining_amount' => $totalDonated - $totalAllocated,
                'average_donation' => $averageDonation,
                'average_days_between_donations' => $avgDaysBetweenDonations,
                'overdue_deliverables_count' => $overdueDeliverablesCount,
                'ongoing_deliverables_count' => $ongoingDeliverablesCount,
                'last_donation_date' => $lastDonationDate ? Carbon::parse($lastDonationDate) : null,
                'next_deliverable_due' => $nextDeliverableDue ? Carbon::parse($nextDeliverableDue) : null,
            ],
            'donations' => $donations,
            'deliverables' => $deliverables,
            'sources' => $sources,
        ]);
    }

    public function edit(Donor $donor): View
    {
        return view('donor-management.edit', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'donor' => $donor->load('user'),
        ]);
    }

    public function update(Request $request, Donor $donor): RedirectResponse
    {
        $validated = $request->validate([
            'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'donor_type' => ['required', 'in:individual,organization'],
            'first_name' => ['required_if:donor_type,individual', 'nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'last_name' => ['required_if:donor_type,individual', 'nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'organization_name' => ['required_if:donor_type,organization', 'nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $donor->user_id],
        ], [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
        ]);

        // update user email if changed
        $user = $donor->user;
        if ($user && $user->email !== $validated['email']) {
            $user->update(['email' => $validated['email']]);
        }

        $donorData = [
            'contact_number' => $validated['contact_number'],
            'dial_code' => $validated['contact_number_code'] ?? '+63',
            'donor_type' => $validated['donor_type'],
            'updated_by' => Auth::user()?->staff?->id,
        ];

        if ($validated['donor_type'] === 'individual') {
            $donorData['first_name'] = $validated['first_name'];
            $donorData['middle_name'] = $validated['middle_name'] ?? null;
            $donorData['last_name'] = $validated['last_name'];
            $donorData['organization_name'] = null;
        } else {
            $donorData['organization_name'] = $validated['organization_name'];
            $donorData['first_name'] = null;
            $donorData['middle_name'] = null;
            $donorData['last_name'] = null;
        }

        $donor->update($donorData);

        return redirect('/donors/' . $donor->id)->with('status', 'Donor updated successfully.');
    }

    public function destroy(Donor $donor): RedirectResponse
    {
        // delete related user first
        if ($donor->user) {
            $donor->user->delete();
        }

        $donor->delete();

        return back()->with('status', 'Donor moved to archive. Will be automatically deleted after 90 days.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:donors,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids): void {
            $donors = Donor::whereIn('id', $ids)->get();
            foreach ($donors as $donor) {
                $donor->load('user');
                if ($donor->user) {
                    $donor->user->delete();
                }
                $donor->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' donors moved to archive. Will be automatically deleted after 90 days.');
    }
}
