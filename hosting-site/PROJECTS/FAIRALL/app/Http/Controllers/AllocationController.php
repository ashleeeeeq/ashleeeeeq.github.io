<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Donation;
use App\Models\Grant;
use App\Models\Program;
use App\Services\AllocationService;
use App\Models\Allocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;

class AllocationController extends Controller
{
    protected $service;

    public function __construct(AllocationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        $baseQuery = $this->buildFilteredQuery($request, $type);

        // Get distinct source IDs from filtered allocations
        $donationIds = (clone $baseQuery)
            ->whereNotNull('donation_id')
            ->select('donation_id')->distinct()
            ->pluck('donation_id')->filter()->values()->toArray();

        $grantIds = (clone $baseQuery)
            ->whereNotNull('grant_id')
            ->select('grant_id')->distinct()
            ->pluck('grant_id')->filter()->values()->toArray();

        // Load sources with ALL their allocations
        $donations = Donation::whereIn('id', $donationIds)
            ->with(['allocations' => fn($q) => $q->with(['beneficiary','donation','grant'])->orderBy('date_allocated')->orderBy('id')])
            ->get()
            ->map(fn($d) => [
                'type' => 'donation',
                'model' => $d,
                'sort_date' => $d->transaction_date ?? $d->created_at ?? now(),
            ]);

        $grants = Grant::whereIn('id', $grantIds)
            ->with(['allocations' => fn($q) => $q->with(['beneficiary','donation','grant'])->orderBy('date_allocated')->orderBy('id')])
            ->get()
            ->map(fn($g) => [
                'type' => 'grant',
                'model' => $g,
                'sort_date' => $g->created_at ?? now(),
            ]);

        // Merge, sort by date desc, paginate
        $sources = $donations->toBase()->merge($grants->toBase())
            ->sortByDesc('sort_date')
            ->values();

        $perPage = 10;
        $page = Paginator::resolveCurrentPage();
        $total = $sources->count();
        $slice = $sources->slice(($page - 1) * $perPage, $perPage)->values();

        $sourcesPaginated = new LengthAwarePaginator($slice, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('funding.allocations.index', [
            'sources' => $sourcesPaginated,
            'programs' => Program::orderBy('program_name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        [$donations, $grants] = $this->loadAllocationSources();
        $beneficiaries = Beneficiary::with('programs')->orderBy('first_name')->orderBy('last_name')->get();
        $programs = Program::orderBy('program_name')->get();
        $sourceType = old('allocation_source', $request->query('allocation_source', 'donation'));
        $donationId = old('donation_id', $request->query('donation_id'));
        $grantId = old('grant_id', $request->query('grant_id'));
        $programFilter = old('program_filter', $request->query('program_filter'));
        $sourceSummary = $this->buildSourceSummary(
            $sourceType,
            $donationId,
            $grantId,
            $donations,
            $grants
        );

        $returnUrl = $request->query('return', url()->previous());
        session()->flash('return_url', $returnUrl);

        return view('funding.allocations.create', compact('donations', 'grants', 'beneficiaries', 'programs', 'sourceType', 'sourceSummary', 'donationId', 'grantId', 'programFilter'));
    }

    public function store(Request $request)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        if ($request->has('allocations')) {
            $validated = $request->validate([
                'allocation_source' => ['required', 'in:donation,grant'],
                'donation_id' => ['nullable','exists:donations,id'],
                'grant_id' => ['nullable','exists:grants,id'],
                'allocations' => ['required','array','min:1','max:20'],
                'allocations.*.beneficiary_id' => ['required','exists:beneficiaries,id'],
                'allocations.*.amount' => ['nullable','regex:/^\d+(\.\d{1,2})?$/'],
                'allocations.*.amount_cents' => ['nullable','integer','min:1'],
                'allocations.*.date_allocated' => ['required','date'],
                'allocations.*.notes' => ['nullable','string','max:1000'],
            ]);

            if ($validated['allocation_source'] === 'donation') {
                $validated['grant_id'] = null;
                if (empty($validated['donation_id'])) {
                    return back()->withErrors(['donation_id' => 'Select a donation.'])->withInput();
                }
            } else {
                $validated['donation_id'] = null;
                if (empty($validated['grant_id'])) {
                    return back()->withErrors(['grant_id' => 'Select a grant.'])->withInput();
                }
            }

            // Normalize each allocation amount to cents
            $normalized = [];
            foreach ($validated['allocations'] as $row) {
                if (!empty($row['amount_cents'])) {
                    $row['amount_cents'] = intval($row['amount_cents']);
                } elseif (!empty($row['amount'])) {
                    $row['amount_cents'] = intval(round(floatval($row['amount']) * 100));
                } else {
                    return back()->withErrors(['allocations' => 'Amount is required for each row.'])->withInput();
                }
                unset($row['amount']);
                $normalized[] = $row;
            }

            $payload = [
                'donation_id' => $validated['donation_id'],
                'grant_id' => $validated['grant_id'],
                'allocations' => $normalized,
            ];

            $created = $this->service->createAllocations($payload);

            $returnUrl = $request->input('return_url', session()->pull('return_url'));
            $count = $created->count();

            return redirect()->to($returnUrl ?: route('funding.allocations.index'))->with('success', $count === 1 ? 'Allocation created' : $count . ' allocations created');
        }

        $validated = $request->validate([
            'allocation_source' => ['required', 'in:donation,grant'],
            'donation_id' => ['nullable','exists:donations,id'],
            'grant_id' => ['nullable','exists:grants,id'],
            'beneficiary_id' => ['required','exists:beneficiaries,id'],
            'amount' => ['nullable','regex:/^\d+(\.\d{1,2})?$/'],
            'amount_cents' => ['nullable','integer','min:1'],
            'date_allocated' => ['required','date'],
            'notes' => ['nullable','string']
        ]);

        if ($validated['allocation_source'] === 'donation') {
            $validated['grant_id'] = null;
            if (empty($validated['donation_id'])) {
                return back()->withErrors(['donation_id' => 'Select a donation.'])->withInput();
            }
        } else {
            $validated['donation_id'] = null;
            if (empty($validated['grant_id'])) {
                return back()->withErrors(['grant_id' => 'Select a grant.'])->withInput();
            }
        }

        // normalize amount to cents
        if (!empty($validated['amount_cents'])) {
            $validated['amount_cents'] = intval($validated['amount_cents']);
        } elseif (!empty($validated['amount'])) {
            $validated['amount_cents'] = intval(round(floatval($validated['amount']) * 100));
        } else {
            return back()->withErrors(['amount' => 'Amount is required'])->withInput();
        }

        $allocation = $this->service->createAllocation($validated);

        $returnUrl = $request->input('return_url', session()->pull('return_url'));

        return redirect()->to($returnUrl ?: route('funding.allocations.show', $allocation))->with('success', 'Allocation created');
    }

    public function show(Allocation $allocation)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        return view('funding.allocations.show', compact('allocation'));
    }

    public function edit(Request $request, Allocation $allocation)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        [$donations, $grants] = $this->loadAllocationSources();
        $sourceType = $allocation->donation_id ? 'donation' : 'grant';
        $sourceSummary = $this->buildSourceSummary(
            $sourceType,
            $allocation->donation_id,
            $allocation->grant_id,
            $donations,
            $grants
        );

        if ($sourceSummary) {
            $sourceSummary['editable_max_cents'] = $sourceSummary['available_cents'] + $allocation->amount_cents;
        }

        $beneficiariesQuery = Beneficiary::with('programs')->orderBy('first_name')->orderBy('last_name');

        if ($allocation->donation_id) {
            $donation = Donation::with('program')->find($allocation->donation_id);
            if ($donation?->program_id) {
                $beneficiariesQuery->whereHas('programs', fn($q) =>
                    $q->where('programs.id', $donation->program_id)
                );
            }
        } elseif ($allocation->grant_id) {
            $grant = Grant::with('programs')->find($allocation->grant_id);
            $grantProgramIds = $grant?->programs->pluck('id');
            if ($grantProgramIds?->isNotEmpty()) {
                $beneficiariesQuery->whereHas('programs', fn($q) =>
                    $q->whereIn('programs.id', $grantProgramIds)
                );
            }
        }

        $beneficiaries = $beneficiariesQuery->get();

        $returnUrl = $request->query('return', url()->previous());
        session()->flash('return_url', $returnUrl);

        return view('funding.allocations.edit', compact('allocation', 'sourceSummary', 'beneficiaries'));
    }

    public function update(Request $request, Allocation $allocation)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        $validated = $request->validate([
            'beneficiary_id' => ['required', 'exists:beneficiaries,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable','string'],
            'date_allocated' => ['required','date']
        ]);

        [$donations, $grants] = $this->loadAllocationSources();
        $sourceType = $allocation->donation_id ? 'donation' : 'grant';
        $sourceSummary = $this->buildSourceSummary(
            $sourceType,
            $allocation->donation_id,
            $allocation->grant_id,
            $donations,
            $grants
        );

        $sourceSummary['editable_max_cents'] = ($sourceSummary['available_cents'] ?? 0) + $allocation->amount_cents;
        $newAmountCents = (int) round(((float) $validated['amount']) * 100);

        if ($newAmountCents > $sourceSummary['editable_max_cents']) {
            return back()
                ->withErrors(['amount' => 'Amount exceeds the allowable balance for this source.'])
                ->withInput();
        }

        $allocation->update([
            'beneficiary_id' => $validated['beneficiary_id'],
            'amount_cents' => $newAmountCents,
            'notes' => $validated['notes'] ?? null,
            'date_allocated' => !empty($validated['date_allocated'])
                ? Carbon::parse($validated['date_allocated'])->toDateString()
                : null,
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        $returnUrl = $request->input('return_url', session()->pull('return_url'));

        return redirect()->to($returnUrl ?: route('funding.allocations.show', $allocation))->with('success', 'Allocation updated');
    }

    public function destroy(Allocation $allocation)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        $allocation->delete();

        $backUrl = url()->previous();
        $showRoute = route('funding.allocations.show', $allocation);

        if (parse_url($backUrl, PHP_URL_PATH) === parse_url($showRoute, PHP_URL_PATH)) {
            $backUrl = route('funding.allocations.index');
        }

        return redirect()->to($backUrl)->with('success', 'Allocation moved to archive. Will be automatically deleted after 90 days.');
    }

    public function bulkDestroy(Request $request)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:allocations,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids): void {
            $allocations = Allocation::whereIn('id', $ids)->get();
            foreach ($allocations as $allocation) {
                $allocation->delete();
            }
        });

        $count = count($ids);

        return back()->with('success', $count . ' allocations moved to archive. Will be automatically deleted after 90 days.');
    }

    public function pollSource(Request $request): JsonResponse
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        $type = $request->query('type');
        $id = $request->query('id');

        if ($type === 'donation') {
            $source = Donation::with(['allocations.beneficiary','allocations.donation','allocations.grant'])->findOrFail($id);
            $totalCents = (int) round(((float) $source->amount) * 100);
        } else {
            $source = Grant::with(['allocations.beneficiary','allocations.donation','allocations.grant'])->findOrFail($id);
            $totalCents = (int) round(((float) $source->total_amount) * 100);
        }

        $totalAllocatedCents = (int) $source->allocations->sum('amount_cents');
        $balanceCents = $totalCents - $totalAllocatedCents;

        $sortByDate = $source->allocations->sortBy('date_allocated')->values();

        $rows = view('funding.allocations._row', ['allocations' => $sortByDate])->render();
        $footer = view('funding.allocations._footer', [
            'totalAllocatedCents' => $totalAllocatedCents,
            'balanceCents' => $balanceCents,
        ])->render();

        return response()->json([
            'rows' => $rows,
            'footer' => $footer,
        ]);
    }

    public function exportCsv(Request $request)
    {
        abort_unless(Gate::allows('manage-donors-and-grants'), 403);

        $baseQuery = $this->buildFilteredQuery($request);

        $donationIds = (clone $baseQuery)
            ->whereNotNull('donation_id')
            ->select('donation_id')->distinct()
            ->pluck('donation_id')->filter()->values()->toArray();

        $grantIds = (clone $baseQuery)
            ->whereNotNull('grant_id')
            ->select('grant_id')->distinct()
            ->pluck('grant_id')->filter()->values()->toArray();

        $donations = Donation::whereIn('id', $donationIds)
            ->with(['allocations' => fn($q) => $q->with(['beneficiary','donation','grant'])->orderBy('date_allocated')->orderBy('id')])
            ->get()
            ->map(fn($d) => [
                'type' => 'donation',
                'model' => $d,
                'sort_date' => $d->transaction_date ?? $d->created_at ?? now(),
            ]);

        $grants = Grant::whereIn('id', $grantIds)
            ->with(['allocations' => fn($q) => $q->with(['beneficiary','donation','grant'])->orderBy('date_allocated')->orderBy('id')])
            ->get()
            ->map(fn($g) => [
                'type' => 'grant',
                'model' => $g,
                'sort_date' => $g->created_at ?? now(),
            ]);

        $sources = $donations->toBase()->merge($grants->toBase())
            ->sortByDesc('sort_date')
            ->values();

        $filename = 'allocations-export-' . now()->format('Ymd-His') . '.csv';

        $callback = function () use ($sources): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            foreach ($sources as $source) {
                $model = $source['model'];
                $sourceLabel = $source['type'] === 'donation'
                    ? ($model->reference_number ?? 'Donation #' . $model->id)
                    : ($model->grant_name ?? 'Grant #' . $model->id);
                $sourceDate = $source['type'] === 'donation'
                    ? optional($model->transaction_date ?? $model->created_at)->format('Y-m-d')
                    : optional($model->created_at)->format('Y-m-d');

                $totalAllocCents = (int) $model->allocations->sum('amount_cents');
                $totalAllocated = $totalAllocCents / 100;
                $sourceAmount = $source['type'] === 'donation'
                    ? (float) $model->amount
                    : (float) ($model->total_amount ?? 0);
                $balance = $sourceAmount - $totalAllocated;

                // Source header row
                fputcsv($out, [strtoupper($sourceLabel)]);
                // Column headers row
                fputcsv($out, ['Beneficiary', 'Amount', 'Date Allocated']);
                // Allocation rows
                foreach ($model->allocations as $a) {
                    fputcsv($out, [
                        $a->beneficiary?->full_name ?? 'N/A',
                        number_format($a->amount_cents / 100, 2),
                        optional($a->date_allocated)->format('Y-m-d'),
                    ]);
                }
                // Summary rows
                fputcsv($out, ['Total Allocated:', number_format($totalAllocated, 2)]);
                fputcsv($out, ['Balance:', number_format(max(0, $balance), 2)]);
                // Blank separator row
                fputcsv($out, []);
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildFilteredQuery(Request $request, ?string &$type = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = Allocation::query();

        if ($search = $request->query('search')) {
            $searchTerm = $search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('beneficiary', fn($b) => $b->where('first_name', 'like', "%{$searchTerm}%")
                      ->orWhere('last_name', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('donation', fn($d) => $d->where('reference_number', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('grant', fn($g) => $g->where('grant_name', 'like', "%{$searchTerm}%"));
            });
        }

        $type = $request->query('type', 'donation');

        if ($request->filled('donor_id')) {
            $query->whereHas('donation', fn($d) => $d->where('donor_id', $request->query('donor_id')));
            $type = 'donation';
        } elseif ($request->filled('grant_id')) {
            $query->where('grant_id', $request->query('grant_id'));
            $type = 'grant';
        }

        if ($type === 'donation') {
            $query->whereNotNull('donation_id');
        } elseif ($type === 'grant') {
            $query->whereNotNull('grant_id');
        }

        if ($request->filled('program_id')) {
            $programId = $request->query('program_id');
            $query->where(function ($q) use ($programId) {
                $q->whereHas('donation', fn($d) => $d->where('program_id', $programId))
                  ->orWhereHas('grant.programs', fn($p) => $p->where('programs.id', $programId));
            });
        }

        return $query;
    }

    private function loadAllocationSources(): array
    {
        $donationTotals = Allocation::query()
            ->selectRaw('donation_id, SUM(amount_cents) as allocated_amount_cents')
            ->whereNotNull('donation_id')
            ->groupBy('donation_id')
            ->pluck('allocated_amount_cents', 'donation_id');

        $grantTotals = Allocation::query()
            ->selectRaw('grant_id, SUM(amount_cents) as allocated_amount_cents')
            ->whereNotNull('grant_id')
            ->groupBy('grant_id')
            ->pluck('allocated_amount_cents', 'grant_id');

        $donations = Donation::with(['donor', 'program'])->latest()->get()->map(function (Donation $donation) use ($donationTotals) {
            $amountCents = (int) round(((float) $donation->amount) * 100);
            $allocatedAmountCents = (int) ($donationTotals[$donation->id] ?? 0);

            $donation->amount_cents = $amountCents;
            $donation->allocated_amount_cents = $allocatedAmountCents;
            $donation->available_amount_cents = max(0, $amountCents - $allocatedAmountCents);

            return $donation;
        });

        $grants = Grant::with('programs')->latest()->get()->map(function (Grant $grant) use ($grantTotals) {
            $amountCents = (int) round(((float) $grant->total_amount) * 100);
            $allocatedAmountCents = (int) ($grantTotals[$grant->id] ?? 0);

            $grant->amount_cents = $amountCents;
            $grant->allocated_amount_cents = $allocatedAmountCents;
            $grant->available_amount_cents = max(0, $amountCents - $allocatedAmountCents);

            return $grant;
        });

        return [$donations, $grants];
    }

    private function buildSourceSummary(string $sourceType, mixed $donationId, mixed $grantId, Collection $donations, Collection $grants): ?array
    {
        if ($sourceType === 'grant' && $grantId) {
            $grant = $grants->firstWhere('id', (int) $grantId);

            if (! $grant) {
                return null;
            }

            return [
                'type' => 'grant',
                'label' => '#'.$grant->id.' - '.$grant->grant_name,
                'total_cents' => (int) $grant->amount_cents,
                'allocated_cents' => (int) $grant->allocated_amount_cents,
                'available_cents' => (int) $grant->available_amount_cents,
            ];
        }

        if ($donationId) {
            $donation = $donations->firstWhere('id', (int) $donationId);

            if (! $donation) {
                return null;
            }

            return [
                'type' => 'donation',
                'label' => '#'.$donation->id.' - '.($donation->donor?->display_name ?? 'No donor'),
                'total_cents' => (int) $donation->amount_cents,
                'allocated_cents' => (int) $donation->allocated_amount_cents,
                'available_cents' => (int) $donation->available_amount_cents,
            ];
        }

        return null;
    }
}
