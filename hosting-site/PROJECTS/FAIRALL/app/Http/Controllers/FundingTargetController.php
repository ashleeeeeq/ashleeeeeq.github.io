<?php

namespace App\Http\Controllers;

use App\Models\FundingTarget;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FundingTargetController extends Controller
{
    public function index(Request $request)
    {
        $query = FundingTarget::with('program');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('year', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('program', fn($p) => $p->where('program_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('program_id')) {
            if ($request->query('program_id') === 'no_programs') {
                $query->whereNull('program_id');
            } else {
                $query->where('program_id', $request->query('program_id'));
            }
        }

        $targets = $query->orderByDesc('year')->orderBy('program_id')->paginate(25)->withQueryString();
        $programs = Program::orderBy('program_name')->get();
        $currentYear = (int) now()->format('Y');

        return view('funding.targets', compact('targets', 'programs', 'currentYear'));
    }

    public function create()
    {
        $programs = Program::orderBy('program_name')->get();
        $currentYear = (int) now()->format('Y');

        return view('funding.targets.create', compact('programs', 'currentYear'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTarget($request);

        $data['target_amount_cents'] = $this->toCents($data['target_amount']);
        unset($data['target_amount']);
        $data['created_by'] = Auth::user()?->staff?->id;
        $data['updated_by'] = Auth::user()?->staff?->id;

        FundingTarget::create($data);

        return redirect()->route('funding.targets')->with('status', 'Funding target created successfully.');
    }

    public function update(Request $request, FundingTarget $fundingTarget)
    {
        $data = $this->validateTarget($request, $fundingTarget->id);

        $data['target_amount_cents'] = $this->toCents($data['target_amount']);
        unset($data['target_amount']);
        $data['updated_by'] = Auth::user()?->staff?->id;

        $fundingTarget->update($data);

        return redirect()->route('funding.targets')->with('status', 'Funding target updated successfully.');
    }

    public function destroy(FundingTarget $fundingTarget)
    {
        $fundingTarget->delete();

        return redirect()->route('funding.targets')->with('status', 'Funding target deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:funding_targets,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids): void {
            $targets = FundingTarget::whereIn('id', $ids)->get();
            foreach ($targets as $target) {
                $target->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' funding targets deleted successfully.');
    }

    private function validateTarget(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'program_id' => ['nullable', 'exists:programs,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', Rule::in(['PHP'])],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function toCents(string|int|float $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }
}
