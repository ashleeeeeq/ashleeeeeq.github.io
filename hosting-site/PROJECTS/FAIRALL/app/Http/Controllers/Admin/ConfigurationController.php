<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use App\Models\AssessmentCategory;
use App\Models\BeneficiaryStatusType;
use App\Models\EventType;
use App\Models\MonitoringConfiguration;
use App\Models\Program;
use App\Models\SportType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function index(): View
    {
        $statusTypes = BeneficiaryStatusType::with('programs')
            ->orderBy('status_name')
            ->get()
            ->map(function ($statusType) {
                $statusType->program_names = $statusType->programs->pluck('program_name')->implode(', ');
                $statusType->program_id_list = $statusType->programs->pluck('id')->toArray();
                unset($statusType->programs);
                return $statusType;
            });

        $activityTypes = ActivityType::with('program')
            ->get()
            ->sortBy(function ($type) {
                return [$type->program?->program_name ?? '', $type->name];
            })
            ->values()
            ->map(function ($type) {
                $type->program_name = $type->program?->program_name;
                unset($type->program);
                return $type;
            });

        return view('admin.configuration.index', [
            'name' => Auth::user()?->display_name ?? 'Admin',
            'statusTypes' => $statusTypes,
            'assessmentCategories' => AssessmentCategory::orderBy('assessment_name')->get(),
            'monitoringConfigurations' => MonitoringConfiguration::orderBy('name')->get(),
            'sportTypes' => SportType::orderBy('name')->get(),
            'programs' => Program::orderBy('program_name')->get(),
            'activityTypes' => $activityTypes,
            'eventTypes' => EventType::orderBy('name')->get(),
        ]);
    }

    public function storeStatusType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_ids' => ['required', 'array', 'min:1'],
            'program_ids.*' => ['integer', 'exists:programs,id'],
            'status_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('beneficiary_status_types', 'status_name'),
            ],
        ]);

        $statusType = BeneficiaryStatusType::create([
            'status_name' => $validated['status_name'],
        ]);

        $statusType->programs()->attach($validated['program_ids']);

        return redirect('/configuration')->with('status', 'Status type added.');
    }

    public function updateStatusType(Request $request, $statusType): RedirectResponse
    {
        $validated = $request->validate([
            'program_ids' => ['required', 'array', 'min:1'],
            'program_ids.*' => ['integer', 'exists:programs,id'],
            'status_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('beneficiary_status_types', 'status_name')->ignore($statusType),
            ],
        ]);

        $statusTypeModel = BeneficiaryStatusType::findOrFail($statusType);
        $statusTypeModel->update([
            'status_name' => $validated['status_name'],
        ]);

        $statusTypeModel->programs()->sync($validated['program_ids']);

        return redirect('/configuration')->with('status', 'Status type updated.');
    }

    public function destroyStatusType($statusType): RedirectResponse
    {
        BeneficiaryStatusType::findOrFail($statusType)->delete();

        return back()->with('status', 'Status type moved to archive. Will be automatically deleted after 90 days.');
    }

    public function storeAssessmentCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assessment_name' => ['required', 'string', 'max:255'],
        ]);

        AssessmentCategory::create([
            'assessment_name' => $validated['assessment_name'],
        ]);

        return redirect('/configuration')->with('status', 'FFA assessment category added.');
    }

    public function updateAssessmentCategory(Request $request, $assessmentCategory): RedirectResponse
    {
        $validated = $request->validate([
            'assessment_name' => ['required', 'string', 'max:255'],
        ]);

        AssessmentCategory::findOrFail($assessmentCategory)->update([
            'assessment_name' => $validated['assessment_name'],
        ]);

        return redirect('/configuration')->with('status', 'FFA assessment category updated.');
    }

    public function destroyAssessmentCategory($assessmentCategory): RedirectResponse
    {
        AssessmentCategory::findOrFail($assessmentCategory)->delete();

        return back()->with('status', 'Assessment category moved to archive. Will be automatically deleted after 90 days.');
    }

    public function storeSportType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        SportType::create([
            'name' => $validated['name'],
        ]);

        return redirect('/configuration')->with('status', 'Sport type added.');
    }

    public function updateSportType(Request $request, $sportType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        SportType::findOrFail($sportType)->update([
            'name' => $validated['name'],
        ]);

        return redirect('/configuration')->with('status', 'Sport type updated.');
    }

    public function destroySportType($sportType): RedirectResponse
    {
        SportType::findOrFail($sportType)->delete();

        return back()->with('status', 'Sport type moved to archive. Will be automatically deleted after 90 days.');
    }

    public function storeActivityType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
        ]);

        ActivityType::create([
            'name' => $validated['name'],
            'program_id' => $validated['program_id'] ?? null,
        ]);

        return redirect('/configuration')->with('status', 'Activity type added.');
    }

    public function updateActivityType(Request $request, $activityType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
        ]);

        ActivityType::findOrFail($activityType)->update([
            'name' => $validated['name'],
            'program_id' => $validated['program_id'] ?? null,
        ]);

        return redirect('/configuration')->with('status', 'Activity type updated.');
    }

    public function destroyActivityType($activityType): RedirectResponse
    {
        ActivityType::findOrFail($activityType)->delete();

        return back()->with('status', 'Activity type moved to archive. Will be automatically deleted after 90 days.');
    }

    public function storeEventType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        EventType::create([
            'name' => $validated['name'],
        ]);

        return redirect('/configuration')->with('status', 'Event type added.');
    }

    public function updateEventType(Request $request, $eventType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        EventType::findOrFail($eventType)->update([
            'name' => $validated['name'],
        ]);

        return redirect('/configuration')->with('status', 'Event type updated.');
    }

    public function destroyEventType($eventType): RedirectResponse
    {
        EventType::findOrFail($eventType)->delete();

        return back()->with('status', 'Event type moved to archive. Will be automatically deleted after 90 days.');
    }

    public function updateMonitoringConfiguration(Request $request, $monitoringConfiguration): RedirectResponse
    {
        $validated = $request->validate([
            'threshold' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'in:0,1'],
        ]);

        $update = [];

        if (array_key_exists('threshold', $validated) && $validated['threshold'] !== null) {
            $update['threshold'] = $validated['threshold'];
        }

        if (array_key_exists('is_active', $validated)) {
            $update['is_active'] = (bool) $validated['is_active'];
        }

        MonitoringConfiguration::findOrFail($monitoringConfiguration)->update($update);

        return redirect('/configuration')->with('status', 'Monitoring configuration updated.');
    }
}
