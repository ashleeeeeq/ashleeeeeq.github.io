<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Department;
use App\Models\HomeVisit;
use App\Models\Program;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeVisitController extends Controller
{
    private const VISIT_TYPES = ['routine', 'intervention', 'follow-up'];

    /**
     * Display a listing of home visits.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $staff = $user?->staff;
        $isAdminOrDirector = $staff?->hasAnyRole(['administrator', 'executive_director']);
        $isSocialWorker = $staff?->hasAnyPosition(['Social Worker']);

        $query = HomeVisit::with(['beneficiary', 'assignedStaff', 'creator', 'updater']);

        // If not admin/director, only show visits explicitly assigned to this staff.
        if (!$isAdminOrDirector && !$isSocialWorker && $staff) {
            $query->where('assigned_staff_id', $staff->id);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                  ->orWhere('visit_type', 'like', "%{$search}%")
                  ->orWhereHas('beneficiary', fn($b) => $b->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%"))
                  ->orWhereHas('assignedStaff', fn($s) => $s->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        if ($isAdminOrDirector && $request->filled('program_id')) {
            $programId = $request->query('program_id');
            if ($programId === 'no_programs') {
                $query->whereDoesntHave('beneficiary.activePrograms');
            } else {
                $query->whereHas('beneficiary.activePrograms', fn($q) => $q->where('programs.id', $programId));
            }
        }

        $homeVisits = $query->latest('schedule')->paginate(10)->withQueryString();

        return view('home_visits.index', [
            'name' => $user?->display_name ?? 'Staff',
            'homeVisits' => $homeVisits,
            'programs' => Program::orderBy('program_name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new home visit.
     */
    public function create(): View
    {
        $user = Auth::user();
        $staff = $user?->staff;
        $isAdminOrDirector = $staff?->hasAnyRole(['administrator', 'executive_director']);

        // Get available beneficiaries
        $beneficiaryQuery = Beneficiary::with('activePrograms')->orderBy('last_name')->orderBy('first_name');
        if (!$isAdminOrDirector && $staff) {
            $staffProgram = $this->getStaffProgram($staff);
            if ($staffProgram) {
                $beneficiaryQuery->whereHas('activePrograms', fn($q) => $q->where('program_id', $staffProgram->id));
            }
        }
        $beneficiaries = $beneficiaryQuery->get();

        // Get available staff for assignment
        $availableStaff = $this->getAvailableStaffForAssignment($staff, $isAdminOrDirector);

        return view('home_visits.create', [
            'name' => $user?->display_name ?? 'Staff',
            'homeVisit' => new HomeVisit(),
            'beneficiaries' => $beneficiaries,
            'visitTypes' => self::VISIT_TYPES,
            'availableStaff' => $availableStaff,
            'currentStaffId' => $staff?->id,
        ]);
    }

    /**
     * Store a newly created home visit.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $staff = $user?->staff;
        $isAdminOrDirector = $staff?->hasAnyRole(['administrator', 'executive_director']);

        // Merge date and time into datetime
        $scheduleDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->input('schedule_date') . ' ' . $request->input('schedule_time')
        );

        $validated = $request->validate([
            'beneficiary_id' => ['required', 'integer', 'exists:beneficiaries,id'],
            'visit_type' => ['required', 'in:routine,intervention,follow-up'],
            'purpose' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'schedule_date' => ['required', 'date'],
            'schedule_time' => ['required', 'date_format:H:i'],
            'assigned_staff_id' => ['nullable', 'integer', 'exists:staff,id'],
        ]);

        // Check beneficiary access
        $beneficiary = Beneficiary::findOrFail($validated['beneficiary_id']);
        if (!$isAdminOrDirector && $staff) {
            $staffProgram = $this->getStaffProgram($staff);
            $hasBeneficiaryAccess = $staffProgram && $beneficiary->activePrograms->contains('id', $staffProgram->id);
            abort_unless($hasBeneficiaryAccess, 403);
        }

        // Validate staff assignment
        if ($validated['assigned_staff_id']) {
            $this->validateStaffAssignment($staff, $validated['assigned_staff_id'], $isAdminOrDirector);
        }

        HomeVisit::create([
            'beneficiary_id' => $validated['beneficiary_id'],
            'visit_type' => $validated['visit_type'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'],
            'schedule' => $scheduleDateTime,
            'assigned_staff_id' => $validated['assigned_staff_id'],
            'created_by' => $staff?->id,
            'updated_by' => $staff?->id,
        ]);

        return redirect()->route('home-visits.index')->with('status', 'Home visit scheduled.');
    }

    /**
     * Display the specified home visit.
     */
    public function show(HomeVisit $homeVisit): View
    {
        Gate::authorize('manage-home-visits', $homeVisit);

        $homeVisit->load(['beneficiary', 'assignedStaff', 'creator', 'updater']);

        return view('home_visits.show', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'homeVisit' => $homeVisit,
        ]);
    }

    /**
     * Show the form for editing the specified home visit.
     */
    public function edit(HomeVisit $homeVisit): View
    {
        Gate::authorize('manage-home-visits', $homeVisit);

        $user = Auth::user();
        $staff = $user?->staff;
        $canManageAll = Gate::allows('manage-home-visits');
        $isAdminOrDirector = $staff?->hasAnyRole(['administrator', 'executive_director']);

        $beneficiaries = collect();
        $availableStaff = [];

        if ($canManageAll) {
            // Get available beneficiaries
            $beneficiaryQuery = Beneficiary::with('activePrograms')->orderBy('last_name')->orderBy('first_name');
            if (!$isAdminOrDirector && $staff) {
                $staffProgram = $this->getStaffProgram($staff);
                if ($staffProgram) {
                    $beneficiaryQuery->whereHas('activePrograms', fn($q) => $q->where('program_id', $staffProgram->id));
                }
            }
            $beneficiaries = $beneficiaryQuery->get();

            // Get available staff for assignment
            $availableStaff = $this->getAvailableStaffForAssignment($staff, $isAdminOrDirector);
        }

        return view('home_visits.edit', [
            'name' => $user?->display_name ?? 'Staff',
            'homeVisit' => $homeVisit->load('beneficiary'),
            'beneficiaries' => $beneficiaries,
            'visitTypes' => self::VISIT_TYPES,
            'availableStaff' => $availableStaff,
            'currentStaffId' => $staff?->id,
            'canManageAllHomeVisits' => $canManageAll,
        ]);
    }

    /**
     * Update the specified home visit.
     */
    public function update(Request $request, HomeVisit $homeVisit): RedirectResponse
    {
        Gate::authorize('manage-home-visits', $homeVisit);

        $user = Auth::user();
        $staff = $user?->staff;
        $canManageAll = Gate::allows('manage-home-visits');

        if ($canManageAll) {
            $isAdminOrDirector = $staff?->hasAnyRole(['administrator', 'executive_director']);

            // Merge date and time into datetime
            $scheduleDateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->input('schedule_date') . ' ' . $request->input('schedule_time')
            );

            $validated = $request->validate([
                'beneficiary_id' => ['required', 'integer', 'exists:beneficiaries,id'],
                'visit_type' => ['required', 'in:routine,intervention,follow-up'],
                'purpose' => ['required', 'string', 'max:100'],
                'notes' => ['nullable', 'string', 'max:500'],
                'schedule_date' => ['required', 'date'],
                'schedule_time' => ['required', 'date_format:H:i'],
                'assigned_staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            ]);

            // Check beneficiary access
            $beneficiary = Beneficiary::findOrFail($validated['beneficiary_id']);
            if (!$isAdminOrDirector && $staff) {
                $staffProgram = $this->getStaffProgram($staff);
                $hasBeneficiaryAccess = $staffProgram && $beneficiary->activePrograms->contains('id', $staffProgram->id);
                abort_unless($hasBeneficiaryAccess, 403);
            }

            // Validate staff assignment
            if ($validated['assigned_staff_id']) {
                $this->validateStaffAssignment($staff, $validated['assigned_staff_id'], $isAdminOrDirector);
            }

            $homeVisit->update([
                'beneficiary_id' => $validated['beneficiary_id'],
                'visit_type' => $validated['visit_type'],
                'purpose' => $validated['purpose'],
                'notes' => $validated['notes'],
                'schedule' => $scheduleDateTime,
                'assigned_staff_id' => $validated['assigned_staff_id'],
                'updated_by' => $staff?->id,
            ]);

            return redirect()->route('home-visits.show', $homeVisit)->with('status', 'Home visit updated.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $homeVisit->update([
            'notes' => $validated['notes'],
            'updated_by' => $staff?->id,
        ]);

        return redirect()->route('home-visits.show', $homeVisit)->with('status', 'Home visit updated.');
    }

    /**
     * Delete the specified home visit.
     */
    public function destroy(HomeVisit $homeVisit): RedirectResponse
    {
        Gate::authorize('manage-home-visits');

        $homeVisit->delete();

        return redirect()->route('home-visits.index')->with('status', 'Home visit deleted.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        Gate::authorize('manage-home-visits');

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:home_visits,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids): void {
            $visits = HomeVisit::whereIn('id', $ids)->get();
            foreach ($visits as $visit) {
                $visit->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' home visits deleted.');
    }

    /**
     * Get staff's program based on their department.
     */
    private function getStaffProgram(Staff $staff): ?Program
    {
        if (!$staff->department) {
            return null;
        }

        $deptName = Str::lower(trim($staff->department->name));
        return Program::query()
            ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
            ->first();
    }

    /**
     * Get available staff for assignment based on role.
     * Admin/Exec: all staff
     * Program Manager: staff in same department
     */
    private function getAvailableStaffForAssignment(?Staff $staff, bool $isAdminOrDirector): array
    {
        if ($isAdminOrDirector) {
            return Staff::with(['department', 'position'])->orderBy('last_name')->orderBy('first_name')->get()->toArray();
        }

        if ($staff && $staff->department_id) {
            return Staff::where('department_id', $staff->department_id)
                ->with(['department', 'position'])
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
                ->toArray();
        }

        return [];
    }

    /**
     * Validate that the assigned staff is appropriate for the user's role.
     */
    private function validateStaffAssignment(?Staff $user, int $assignedStaffId, bool $isAdminOrDirector): void
    {
        // Admin/Exec can assign any staff
        if ($isAdminOrDirector) {
            return;
        }

        // For program managers and others, validate staff is in same department
        if ($user && $user->department_id) {
            $assignedStaff = Staff::find($assignedStaffId);
            abort_unless(
                $assignedStaff && $assignedStaff->department_id === $user->department_id,
                403,
                'You can only assign staff from your department.'
            );
        } else {
            abort(403, 'You do not have permission to assign staff.');
        }
    }
}
