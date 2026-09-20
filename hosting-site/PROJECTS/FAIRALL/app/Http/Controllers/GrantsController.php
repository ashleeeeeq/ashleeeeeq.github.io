<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Grant;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrantsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Grant::with(['programs']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('grant_name', 'like', "%{$search}%")
                  ->orWhere('organization_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('program_id')) {
            $programId = $request->query('program_id');
            if ($programId === 'no_programs') {
                $query->whereDoesntHave('programs');
            } else {
                $query->whereHas('programs', fn($q) => $q->where('programs.id', $programId));
            }
        }

        $grants = $query->withSum('allocations', 'amount_cents')->latest('id')->paginate(15)->withQueryString();

        return view('grants.index', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'grants' => $grants,
            'programs' => Program::orderBy('id')->get(),
        ]);
    }

    public function show(Request $request, Grant $grant): View
    {
        $grant->load(['programs']);

        $activeTab = $request->query('tab', 'metrics');

        if (! in_array($activeTab, ['metrics', 'allocations', 'deliverables'], true)) {
            $activeTab = 'metrics';
        }

        $deliverables = null;
        $allocations = null;

        if ($activeTab === 'deliverables') {
            $deliverables = $grant->deliverables()->orderBy('start_date')->orderBy('end_date')->paginate(10)->withQueryString();
        } elseif ($activeTab === 'allocations') {
            $allocations = $grant->allocations()->with('beneficiary')->orderBy('date_allocated')->orderBy('id')->get();
        }

        $beneficiariesSupported = $grant->allocations()
            ->whereNotNull('beneficiary_id')
            ->distinct('beneficiary_id')
            ->count('beneficiary_id');

        $totalAllocatedCents = (int) $grant->allocations()->sum('amount_cents');
        $totalGrantCents = (int) round((float) $grant->total_amount * 100);
        $remainingCents = $totalGrantCents - $totalAllocatedCents;

        return view('grants.show', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'grant' => $grant,
            'deliverables' => $deliverables,
            'allocations' => $allocations,
            'activeTab' => $activeTab,
            'beneficiariesSupported' => $beneficiariesSupported,
            'totalAllocatedCents' => $totalAllocatedCents,
            'totalGrantCents' => $totalGrantCents,
            'remainingCents' => $remainingCents,
        ]);
    }

    public function create(): View
    {
        return view('grants.create', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'programs' => Program::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'contact_number_code' => ['nullable', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['nullable', 'regex:/^[0-9]{6,15}$/'],
            'grant_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'program_ids' => ['nullable', 'array'],
            'program_ids.*' => ['integer', 'exists:programs,id'],
        ]);

        $grant = Grant::create([
            'organization_name' => $validated['organization_name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
            'dial_code' => $validated['contact_number_code'] ?? (isset($validated['contact_number']) ? '+63' : null),
            'grant_name' => $validated['grant_name'],
            'description' => $validated['description'] ?? null,
            'total_amount' => $validated['total_amount'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'created_by' => Auth::user()?->staff?->id,
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        $grant->programs()->sync($validated['program_ids'] ?? []);

        return redirect('/grants')->with('status', 'Grant created successfully.');
    }

    public function edit(Grant $grant): View
    {
        $grant->load('programs');

        return view('grants.edit', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'grant' => $grant,
            'programs' => Program::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, Grant $grant): RedirectResponse
    {
        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'contact_number_code' => ['nullable', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['nullable', 'regex:/^[0-9]{6,15}$/'],
            'grant_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'program_ids' => ['nullable', 'array'],
            'program_ids.*' => ['integer', 'exists:programs,id'],
        ]);

        $grant->update([
            'organization_name' => $validated['organization_name'],
            'email' => $validated['email'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'dial_code' => $validated['contact_number_code'] ?? (isset($validated['contact_number']) ? '+63' : null),
            'grant_name' => $validated['grant_name'],
            'description' => $validated['description'] ?? null,
            'total_amount' => $validated['total_amount'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        $grant->programs()->sync($validated['program_ids'] ?? []);

        return redirect('/grants/' . $grant->id)->with('status', 'Grant updated successfully.');
    }

    public function destroy(Grant $grant): RedirectResponse
    {
        $grant->delete();

        return redirect('/grants')->with('status', 'Grant deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:grants,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids): void {
            $grants = Grant::whereIn('id', $ids)->get();
            foreach ($grants as $grant) {
                $grant->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' grants deleted successfully.');
    }
}
