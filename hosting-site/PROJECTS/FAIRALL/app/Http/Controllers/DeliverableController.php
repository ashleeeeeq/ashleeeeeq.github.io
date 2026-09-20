<?php

namespace App\Http\Controllers;

use App\Models\Deliverable;
use App\Models\Donor;
use App\Models\Grant;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DeliverableController extends Controller
{
    private function ensureExactlyOneOwner(?int $donorId, ?int $grantId): void
    {
        if (($donorId === null && $grantId === null) || ($donorId !== null && $grantId !== null)) {
            abort(422, 'A deliverable must belong to either a donor or a grant.');
        }
    }

    private function deriveDeliverableState(int $progress, ?CarbonInterface $endDate = null): string
    {
        if ($progress >= 100) {
            return 'completed';
        }

        if ($endDate !== null && $endDate->isPast() && $progress < 100) {
            return 'overdue';
        }

        if ($progress <= 0) {
            return 'not_started';
        }

        return 'in_progress';
    }

    private function validatedPayload(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);
    }

    private function toChartTask(Deliverable $deliverable): array
    {
        $start = $deliverable->start_date?->copy()->startOfDay() ?? now()->startOfDay();
        $end = $deliverable->end_date?->copy()->startOfDay() ?? $start->copy()->addDay();

        if ($end->lessThan($start)) {
            $end = $start->copy()->addDay();
        }

        $progress = (int) ($deliverable->progress ?? 0);
        $derived = $this->deriveDeliverableState($progress, $deliverable->end_date);

        return [
            'id' => (string) $deliverable->id,
            'name' => $deliverable->title,
            'title' => $deliverable->title,
            'description' => $deliverable->description,
            'donor_id' => $deliverable->donor_id,
            'grant_id' => $deliverable->grant_id,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'progress' => $progress,
            'derived_status' => $derived,
            'custom_class' => 'deliverable-state-' . str_replace('_', '-', $derived),
        ];
    }

    public function createForDonor(Donor $donor): View
    {
        return view('deliverables.create', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'ownerType' => 'donor',
            'owner' => $donor->load(['user']),
        ]);
    }

    public function indexForDonor(Donor $donor): View
    {
        $deliverables = $donor->deliverables()->orderBy('start_date')->orderBy('end_date')->paginate(10)->withQueryString();

        $chartTasks = $donor->deliverables()
            ->orderBy('start_date')
            ->orderBy('end_date')
            ->get()
            ->map(fn (Deliverable $deliverable) => $this->toChartTask($deliverable))
            ->values()
            ->all();

        return view('deliverables.gantt', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'ownerType' => 'donor',
            'owner' => $donor->load('user'),
            'deliverables' => $deliverables,
            'chartTasks' => $chartTasks,
        ]);
    }

    public function showForDonor(Donor $donor, Deliverable $deliverable): View
    {
        $this->ensureDeliverableBelongsToDonor($donor, $deliverable);

        return view('deliverables.show', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'ownerType' => 'donor',
            'owner' => $donor->load('user'),
            'deliverable' => $deliverable,
        ]);
    }

    public function storeForDonor(Request $request, Donor $donor): RedirectResponse
    {
        $validated = $this->validatedPayload($request);
        $this->ensureExactlyOneOwner($donor->id, null);

        $progress = array_key_exists('progress', $validated)
            ? (int) $validated['progress']
            : 0;

        Deliverable::create([
            'donor_id' => $donor->id,
            'grant_id' => null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'progress' => $progress,
            'completed_at' => $progress >= 100 ? now() : null,
            'created_by' => Auth::user()?->staff?->id,
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        return redirect('/donors/' . $donor->id . '?tab=deliverables')->with('status', 'Deliverable created successfully.');
    }

    public function editForDonor(Donor $donor, Deliverable $deliverable): View
    {
        $this->ensureDeliverableBelongsToDonor($donor, $deliverable);

        return view('deliverables.edit', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'ownerType' => 'donor',
            'owner' => $donor->load(['user']),
            'deliverable' => $deliverable,
        ]);
    }

    public function updateForDonor(Request $request, Donor $donor, Deliverable $deliverable): Response
    {
        $this->ensureDeliverableBelongsToDonor($donor, $deliverable);
        $this->ensureExactlyOneOwner($deliverable->donor_id, $deliverable->grant_id);

        $validated = $this->validatedPayload($request);

        $hasProgress = array_key_exists('progress', $validated);
        $progress = $hasProgress
            ? (int) $validated['progress']
            : (int) ($deliverable->progress ?? 0);

        $update = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'progress' => $progress,
            'updated_by' => Auth::user()?->staff?->id,
        ];

        if ($hasProgress) {
            $update['completed_at'] = $progress >= 100 ? now() : null;
        }

        $deliverable->update($update);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Deliverable updated successfully.',
                'deliverable' => $this->toChartTask($deliverable),
            ]);
        }

        return redirect('/donors/' . $donor->id)->with('status', 'Deliverable updated successfully.');
    }

    public function destroyForDonor(Request $request, Donor $donor, Deliverable $deliverable): Response
    {
        $this->ensureDeliverableBelongsToDonor($donor, $deliverable);

        $deliverable->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Deliverable deleted successfully.',
            ]);
        }

        return redirect('/donors/' . $donor->id)->with('status', 'Deliverable deleted successfully.');
    }

    public function createForGrant(Grant $grant): View
    {
        return view('deliverables.create', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'ownerType' => 'grant',
            'owner' => $grant,
        ]);
    }

    public function indexForGrant(Grant $grant): View
    {
        $deliverables = $grant->deliverables()->orderBy('start_date')->orderBy('end_date')->paginate(10)->withQueryString();

        $chartTasks = $grant->deliverables()
            ->orderBy('start_date')
            ->orderBy('end_date')
            ->get()
            ->map(fn (Deliverable $deliverable) => $this->toChartTask($deliverable))
            ->values()
            ->all();

        return view('deliverables.gantt', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'ownerType' => 'grant',
            'owner' => $grant,
            'deliverables' => $deliverables,
            'chartTasks' => $chartTasks,
        ]);
    }

    public function showForGrant(Grant $grant, Deliverable $deliverable): View
    {
        $this->ensureDeliverableBelongsToGrant($grant, $deliverable);

        return view('deliverables.show', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'ownerType' => 'grant',
            'owner' => $grant,
            'deliverable' => $deliverable,
        ]);
    }

    public function storeForGrant(Request $request, Grant $grant): RedirectResponse
    {
        $validated = $this->validatedPayload($request);
        $this->ensureExactlyOneOwner(null, $grant->id);

        $progress = array_key_exists('progress', $validated)
            ? (int) $validated['progress']
            : 0;

        Deliverable::create([
            'donor_id' => null,
            'grant_id' => $grant->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'progress' => $progress,
            'completed_at' => $progress >= 100 ? now() : null,
            'created_by' => Auth::user()?->staff?->id,
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        return redirect('/grants/' . $grant->id . '?tab=deliverables')->with('status', 'Deliverable created successfully.');
    }

    public function editForGrant(Grant $grant, Deliverable $deliverable): View
    {
        $this->ensureDeliverableBelongsToGrant($grant, $deliverable);

        return view('deliverables.edit', [
            'name' => auth()->user()?->display_name ?? 'Staff',
            'ownerType' => 'grant',
            'owner' => $grant,
            'deliverable' => $deliverable,
        ]);
    }

    public function updateForGrant(Request $request, Grant $grant, Deliverable $deliverable): Response
    {
        $this->ensureDeliverableBelongsToGrant($grant, $deliverable);
        $this->ensureExactlyOneOwner($deliverable->donor_id, $deliverable->grant_id);

        $validated = $this->validatedPayload($request);

        $hasProgress = array_key_exists('progress', $validated);
        $progress = $hasProgress
            ? (int) $validated['progress']
            : (int) ($deliverable->progress ?? 0);

        $update = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'progress' => $progress,
            'updated_by' => Auth::user()?->staff?->id,
        ];

        if ($hasProgress) {
            $update['completed_at'] = $progress >= 100 ? now() : null;
        }

        $deliverable->update($update);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Deliverable updated successfully.',
                'deliverable' => $this->toChartTask($deliverable),
            ]);
        }

        return redirect('/grants/' . $grant->id)->with('status', 'Deliverable updated successfully.');
    }

    public function destroyForGrant(Request $request, Grant $grant, Deliverable $deliverable): Response
    {
        $this->ensureDeliverableBelongsToGrant($grant, $deliverable);

        $deliverable->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Deliverable deleted successfully.',
            ]);
        }

        return redirect('/grants/' . $grant->id)->with('status', 'Deliverable deleted successfully.');
    }

    private function ensureDeliverableBelongsToDonor(Donor $donor, Deliverable $deliverable): void
    {
        if ($deliverable->donor_id !== $donor->id || $deliverable->grant_id !== null) {
            abort(404);
        }
    }

    private function ensureDeliverableBelongsToGrant(Grant $grant, Deliverable $deliverable): void
    {
        if ($deliverable->grant_id !== $grant->id || $deliverable->donor_id !== null) {
            abort(404);
        }
    }
}