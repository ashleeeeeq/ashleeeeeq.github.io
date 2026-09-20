<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompetitionResultController extends Controller
{
    /**
     * Show the form for creating a new competition result.
     */
    public function create(Competition $competition): View
    {
        $user = Auth::user();
        $staff = $user?->staff;
        $isAdminOrDirector = $staff?->hasAnyRole(['administrator', 'executive_director']);

        // Get beneficiaries from competition's program
        $beneficiaries = $competition->program
            ->beneficiaries()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('competition_results.create', [
            'name' => $user?->display_name ?? 'Staff',
            'competition' => $competition,
            'beneficiaries' => $beneficiaries,
        ]);
    }

    /**
     * Store a newly created competition result in storage.
     */
    public function store(Request $request, Competition $competition): RedirectResponse
    {
        $validated = $request->validate([
            'beneficiary_id' => ['required', 'integer', 'exists:beneficiaries,id'],
            'placement' => ['required', 'string', 'max:100'],
            'date_given' => ['nullable', 'date'],
        ]);

        $user = Auth::user();
        $staff = $user?->staff;

        CompetitionResult::create([
            'competition_id' => $competition->id,
            'beneficiary_id' => $validated['beneficiary_id'],
            'placement' => $validated['placement'],
            'date_given' => $validated['date_given'] ?? now()->toDateString(),
            'created_by' => $staff?->id,
            'updated_by' => $staff?->id,
        ]);

        return redirect()->route('competitions.show', $competition)->with('status', 'Competition result added successfully.');
    }

    /**
     * Update the specified competition result in storage.
     */
    public function update(Request $request, Competition $competition, CompetitionResult $competitionResult): RedirectResponse
    {
        $validated = $request->validate([
            'beneficiary_id' => ['required', 'integer', 'exists:beneficiaries,id'],
            'placement' => ['required', 'string', 'max:100'],
            'date_given' => ['nullable', 'date'],
        ]);

        $user = Auth::user();
        $staff = $user?->staff;

        $competitionResult->update([
            'beneficiary_id' => $validated['beneficiary_id'],
            'placement' => $validated['placement'],
            'date_given' => $validated['date_given'] ?? now()->toDateString(),
            'updated_by' => $staff?->id,
        ]);

        return redirect()->route('competitions.show', $competition)->with('status', 'Competition result updated successfully.');
    }

    /**
     * Remove the specified competition result from storage.
     */
    public function destroy(Competition $competition, CompetitionResult $competitionResult): RedirectResponse
    {
        $competitionResult->delete();

        return redirect()->route('competitions.show', $competition)->with('status', 'Competition result deleted successfully.');
    }
}
