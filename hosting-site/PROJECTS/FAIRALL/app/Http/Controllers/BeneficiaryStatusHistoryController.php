<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BeneficiaryStatusHistoryController extends Controller
{
    public function index(Beneficiary $beneficiary): View
    {
        $beneficiary->load(['statuses.statusType']);

        $statusRecords = $beneficiary->statuses
            ->sortByDesc(fn($record) => [$record->start_date, $record->id])
            ->values();

        return view('beneficiaries.status_history.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'statusRecords' => $statusRecords,
        ]);
    }

    public function create(Beneficiary $beneficiary): View
    {
        $beneficiary->loadMissing('programs');
        $programIds = $beneficiary->programs->pluck('id');
        $statusTypes = DB::table('beneficiary_status_types')
            ->join('beneficiary_status_type_program', 'beneficiary_status_types.id', '=', 'beneficiary_status_type_program.beneficiary_status_type_id')
            ->join('programs', 'beneficiary_status_type_program.program_id', '=', 'programs.id')
            ->whereIn('beneficiary_status_type_program.program_id', $programIds)
            ->select('beneficiary_status_types.id', 'beneficiary_status_types.status_name', 'programs.program_name')
            ->orderBy('beneficiary_status_types.status_name')
            ->get()
            ->groupBy('id')
            ->mapWithKeys(fn($items) => [
                $items->first()->id => $items->first()->status_name . ' (' . $items->pluck('program_name')->filter()->implode(', ') . ')',
            ]);

        return view('beneficiaries.status_history.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'statusTypes' => $statusTypes,
        ]);
    }

    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $beneficiary->loadMissing('programs');
        $programIds = $beneficiary->programs->pluck('id');

        $validated = $request->validate([
            'beneficiary_status_type_id' => [
                'required',
                'integer',
                Rule::exists('beneficiary_status_types', 'id')
                    ->where(fn($query) => $query->whereIn('id', fn($q) => $q
                        ->select('beneficiary_status_type_id')
                        ->from('beneficiary_status_type_program')
                        ->whereIn('program_id', $programIds)
                    )),
                Rule::unique('beneficiary_statuses', 'beneficiary_status_type_id')
                    ->where('beneficiary_id', $beneficiary->id)
                    ->where('start_date', $request->input('start_date')),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        BeneficiaryStatus::create(array_merge($validated, [
            'beneficiary_id' => $beneficiary->id,
            'created_by' => Auth::user()?->staff?->id,
        ]));

        return redirect("/beneficiaries/{$beneficiary->id}/status-history")->with('status', 'Status record added.');
    }

    public function edit(Beneficiary $beneficiary, $statusRecord): View
    {
        $beneficiary->loadMissing('programs');
        $record = BeneficiaryStatus::where('beneficiary_id', $beneficiary->id)->findOrFail($statusRecord);
        $statusTypes = DB::table('beneficiary_status_types')
            ->join('beneficiary_status_type_program', 'beneficiary_status_types.id', '=', 'beneficiary_status_type_program.beneficiary_status_type_id')
            ->join('programs', 'beneficiary_status_type_program.program_id', '=', 'programs.id')
            ->whereIn('beneficiary_status_type_program.program_id', $beneficiary->programs->pluck('id'))
            ->select('beneficiary_status_types.id', 'beneficiary_status_types.status_name', 'programs.program_name')
            ->orderBy('beneficiary_status_types.status_name')
            ->get()
            ->groupBy('id')
            ->mapWithKeys(fn($items) => [
                $items->first()->id => $items->first()->status_name . ' (' . $items->pluck('program_name')->filter()->implode(', ') . ')',
            ]);

        return view('beneficiaries.status_history.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'record' => $record,
            'statusTypes' => $statusTypes,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary, $statusRecord): RedirectResponse
    {
        $beneficiary->loadMissing('programs');
        $programIds = $beneficiary->programs->pluck('id');

        $validated = $request->validate([
            'beneficiary_status_type_id' => [
                'required',
                'integer',
                Rule::exists('beneficiary_status_types', 'id')
                    ->where(fn($query) => $query->whereIn('id', fn($q) => $q
                        ->select('beneficiary_status_type_id')
                        ->from('beneficiary_status_type_program')
                        ->whereIn('program_id', $programIds)
                    )),
                Rule::unique('beneficiary_statuses', 'beneficiary_status_type_id')
                    ->where('beneficiary_id', $beneficiary->id)
                    ->where('start_date', $request->input('start_date'))
                    ->ignore($statusRecord),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        DB::table('beneficiary_statuses')
            ->where('id', $statusRecord)
            ->where('beneficiary_id', $beneficiary->id)
            ->update(array_merge($validated, [
                'updated_at' => now(),
                'updated_by' => Auth::user()?->staff?->id,
            ]));

        return redirect("/beneficiaries/{$beneficiary->id}/status-history")->with('status', 'Status record updated.');
    }

    public function destroy(Beneficiary $beneficiary, $statusRecord): RedirectResponse
    {
        DB::table('beneficiary_statuses')
            ->where('id', $statusRecord)
            ->where('beneficiary_id', $beneficiary->id)
            ->delete();

        return redirect("/beneficiaries/{$beneficiary->id}/status-history")->with('status', 'Status record deleted.');
    }
}