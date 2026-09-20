<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\ActivityType;
use App\Models\Beneficiary;
use App\Models\Program;
use App\Models\SportType;
use App\Models\Staff;
use App\Models\ActivitySession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        $query = Activity::with(['program', 'activityType', 'sportType'])
            ->withCount('activitySessions');

        $filter = request('filter', 'active');

        if ($filter === 'active') {
            $query->where('is_active', 1);
        } elseif ($filter === 'inactive') {
            $query->where('is_active', 0);
        }

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('program', fn($p) => $p->where('program_name', 'like', "%{$search}%"))
                  ->orWhereHas('activityType', fn($t) => $t->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('sportType', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        // If not admin/director, scope to user's program or where they are a participant
        if (!$isAdminOrDirector && $user?->staff) {
            $staffId = $user->staff->id;
            $staffProgram = null;

            // Get staff's program from department
            if ($user->staff->department) {
                $deptName = Str::lower(trim($user->staff->department->name));
                $staffProgram = Program::query()
                    ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                    ->first();
            }

            $query->where(function ($q) use ($staffId, $staffProgram) {
                // Show activities tied to staff's program
                if ($staffProgram) {
                    $q->where('program_id', $staffProgram->id);
                }
                // OR activities where staff is a participant
                $q->orWhereHas('participantRecords', function ($q) use ($staffId) {
                    $q->where('staff_id', $staffId)
                      ->whereNull('exited_at');
                });
            });
        }

        $activities = $query->latest('id')->paginate(12, ['*'], 'activities_page')->withQueryString();

        return view('activities.index', [
            'name' => $user?->display_name ?? 'Staff',
            'activities' => $activities,
            'currentFilter' => $filter,
        ]);
    }

    public function show(Activity $activity): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        // Check if staff has permission to view this activity
        if (!$isAdminOrDirector && $user?->staff) {
            $staffId = $user->staff->id;
            $staffProgram = null;

            // Get staff's program from department
            if ($user->staff->department) {
                $deptName = Str::lower(trim($user->staff->department->name));
                $staffProgram = Program::query()
                    ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                    ->first();
            }

            // Allow if activity is in staff's program or staff is a participant
            $hasAccess = false;
            if ($staffProgram && $activity->program_id === $staffProgram->id) {
                $hasAccess = true;
            } else {
                $isParticipant = ActivityParticipant::where('activity_id', $activity->id)
                    ->where('staff_id', $staffId)
                    ->whereNull('exited_at')
                    ->exists();
                if ($isParticipant) {
                    $hasAccess = true;
                }
            }

            abort_unless($hasAccess, 403);
        }

        $activity->load([
            'program',
            'activityType',
            'sportType',
        ]);

        // Paginate activity sessions
        $sessions = ActivitySession::where('activity_id', $activity->id)
            ->with(['creator', 'updater'])
            ->orderBy('schedule', 'desc')
            ->paginate(10, ['*'], 'sessions_page');

        return view('activities.show', [
            'name' => $user?->display_name ?? 'Staff',
            'activity' => $activity,
            'sessions' => $sessions,
        ]);
    }

    public function participants(Activity $activity): View
    {
        $activity->load([
            'program',
            'activityType',
            'sportType',
        ]);

        $activityProgramName = Str::lower(trim($activity->program?->program_name ?? ''));
        
        // Get IDs of beneficiaries already in this activity
        $activeBeneficiaryIds = ActivityParticipant::query()
            ->where('activity_id', $activity->id)
            ->whereNotNull('beneficiary_id')
            ->whereNull('exited_at')
            ->pluck('beneficiary_id')
            ->toArray();

        $availableBeneficiaries = Beneficiary::query()
            ->whereHas('activePrograms', function ($query) use ($activity) {
                $query->whereKey($activity->program_id);
            })
            ->whereNotIn('id', $activeBeneficiaryIds)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Get IDs of staff already in this activity
        $activeStaffIds = ActivityParticipant::query()
            ->where('activity_id', $activity->id)
            ->whereNotNull('staff_id')
            ->whereNull('exited_at')
            ->pluck('staff_id')
            ->toArray();

        $availableStaff = Staff::with('department')
            ->whereNotIn('id', $activeStaffIds)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Paginate active beneficiaries
        $activeBeneficiaries = ActivityParticipant::query()
            ->where('activity_id', $activity->id)
            ->whereNotNull('beneficiary_id')
            ->whereNull('exited_at')
            ->with('beneficiary')
            ->paginate(10, ['*'], 'beneficiaries_page');

        // Paginate active staff
        $activeStaff = ActivityParticipant::query()
            ->where('activity_id', $activity->id)
            ->whereNotNull('staff_id')
            ->whereNull('exited_at')
            ->with('staff')
            ->paginate(10, ['*'], 'staff_page');

        return view('activities.participants.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'activity' => $activity,
            'availableBeneficiaries' => $availableBeneficiaries,
            'availableStaff' => $availableStaff,
            'activeBeneficiaries' => $activeBeneficiaries,
            'activeStaff' => $activeStaff,
        ]);
    }

    public function history(Activity $activity): View
    {
        $activity->load([
            'program',
            'activityType',
            'sportType',
        ]);

        // Paginate participant records
        $participantRecords = ActivityParticipant::where('activity_id', $activity->id)
            ->with(['beneficiary', 'staff'])
            ->orderByDesc('joined_at')
            ->paginate(10, ['*'], 'history_page');

        return view('activities.history.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'activity' => $activity,
            'participantRecords' => $participantRecords,
        ]);
    }

    public function addParticipant(Request $request, Activity $activity): RedirectResponse
    {
        $activity->load('program');

        $validated = $request->validate([
            'participant_type' => ['required', 'in:beneficiary,staff'],
            'participant_id' => ['required', 'integer'],
        ]);

        if ($validated['participant_type'] === 'beneficiary') {
            $beneficiary = Beneficiary::with('activePrograms')->findOrFail($validated['participant_id']);

            abort_unless($beneficiary->activePrograms->contains('id', $activity->program_id), 403);

            $existing = ActivityParticipant::query()
                ->where('activity_id', $activity->id)
                ->where('beneficiary_id', $beneficiary->id)
                ->whereNull('exited_at')
                ->exists();

            if ($existing) {
                return back()->with('status', 'Beneficiary is already active in this activity.');
            }

            ActivityParticipant::create([
                'activity_id' => $activity->id,
                'beneficiary_id' => $beneficiary->id,
                'staff_id' => null,
                'joined_at' => now(),
                'exited_at' => null,
                'created_by' => Auth::user()?->staff?->id,
                'updated_by' => Auth::user()?->staff?->id,
            ]);
        } else {
            $staff = Staff::with('department')->findOrFail($validated['participant_id']);

            $existing = ActivityParticipant::query()
                ->where('activity_id', $activity->id)
                ->where('staff_id', $staff->id)
                ->whereNull('exited_at')
                ->exists();

            if ($existing) {
                return back()->with('status', 'Staff member is already active in this activity.');
            }

            ActivityParticipant::create([
                'activity_id' => $activity->id,
                'beneficiary_id' => null,
                'staff_id' => $staff->id,
                'joined_at' => now(),
                'exited_at' => null,
                'created_by' => Auth::user()?->staff?->id,
                'updated_by' => Auth::user()?->staff?->id,
            ]);
        }

        return back()->with('status', 'Participant added.');
    }

    public function removeParticipant(Activity $activity, ActivityParticipant $participant): RedirectResponse
    {
        abort_unless($participant->activity_id === $activity->id, 404);

        if ($participant->exited_at) {
            return back()->with('status', 'Participant already exited.');
        }

        $participant->update([
            'exited_at' => now(),
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        return back()->with('status', 'Participant exited from activity.');
    }

    public function create(): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);
        $programs = Program::orderBy('program_name')->get();
        
        // If not admin/director, determine user's program from department
        $userProgram = null;
        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = $programs->first(function ($p) use ($deptName) {
                return Str::lower(trim($p->program_name)) === $deptName;
            });
        }

        return view('activities.create', [
            'name' => $user?->display_name ?? 'Staff',
            'activityTypes' => ActivityType::orderBy('name')->get(),
            'sportTypes' => SportType::orderBy('name')->get(),
            'programs' => $programs,
            'showProgramSelector' => $isAdminOrDirector,
            'userProgram' => $userProgram,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'sport_type_id' => ['nullable', 'integer', 'exists:sport_types,id'],
            'activity_type_id' => ['required', 'integer', 'exists:activity_types,id'],
            'description' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['program_id'] = (int) $validated['program_id'];
        $validated['activity_type_id'] = (int) $validated['activity_type_id'];

        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        // If not admin/director, enforce program assignment from user's department
        if (!$isAdminOrDirector) {
            if ($user?->staff?->department) {
                $deptName = Str::lower(trim($user->staff->department->name));
                $program = Program::query()
                    ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                    ->first();

                if ($program) {
                    $validated['program_id'] = $program->id;
                }
            }
        }

        $program = Program::find($validated['program_id']);
        if (!$program || !Str::contains(Str::lower(trim($program->program_name)), 'sports')) {
            $validated['sport_type_id'] = null;
        }

        // Validate that the activity type is allowed for the program
        $activityType = ActivityType::findOrFail($validated['activity_type_id']);
        if ($activityType->program_id && $activityType->program_id != $validated['program_id']) {
            return back()->withErrors(['activity_type_id' => 'This activity type is not available for the selected program.']);
        }

        DB::table('activities')->insert([
            'name' => $validated['name'],
            'program_id' => $validated['program_id'],
            'sport_type_id' => $validated['sport_type_id'] ?? null,
            'activity_type_id' => $validated['activity_type_id'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'created_by' => Auth::user()?->staff?->id,
            'updated_by' => Auth::user()?->staff?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/activities')->with('status', 'Activity added.');
    }

    public function edit(Activity $activity): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);
        $programs = Program::orderBy('program_name')->get();
        
        // If not admin/director, determine user's program from department
        $userProgram = null;
        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = $programs->first(function ($p) use ($deptName) {
                return Str::lower(trim($p->program_name)) === $deptName;
            });
        }

        return view('activities.edit', [
            'name' => $user?->display_name ?? 'Staff',
            'activity' => $activity->load(['program', 'activityType', 'sportType']),
            'activityTypes' => ActivityType::orderBy('name')->get(),
            'sportTypes' => SportType::orderBy('name')->get(),
            'programs' => $programs,
            'showProgramSelector' => $isAdminOrDirector,
            'userProgram' => $userProgram,
        ]);
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'sport_type_id' => ['nullable', 'integer', 'exists:sport_types,id'],
            'activity_type_id' => ['required', 'integer', 'exists:activity_types,id'],
            'description' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['program_id'] = (int) $validated['program_id'];
        $validated['activity_type_id'] = (int) $validated['activity_type_id'];

        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        // If not admin/director, enforce program assignment from user's department
        if (!$isAdminOrDirector) {
            if ($user?->staff?->department) {
                $deptName = Str::lower(trim($user->staff->department->name));
                $program = Program::query()
                    ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                    ->first();

                if ($program) {
                    $validated['program_id'] = $program->id;
                }
            }
        }

        $program = Program::find($validated['program_id']);
        if (!$program || !Str::contains(Str::lower(trim($program->program_name)), 'sports')) {
            $validated['sport_type_id'] = null;
        }

        // Validate that the activity type is allowed for the program
        $activityType = ActivityType::findOrFail($validated['activity_type_id']);
        if ($activityType->program_id !== null && (int) $activityType->program_id !== (int) $validated['program_id']) {
            return back()->withErrors(['activity_type_id' => 'This activity type is not available for the selected program.']);
        }

        $activity->update([
            'name' => $validated['name'],
            'program_id' => $validated['program_id'],
            'sport_type_id' => $validated['sport_type_id'] ?? null,
            'activity_type_id' => $validated['activity_type_id'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        return redirect('/activities/' . $activity->id)
            ->with('status', 'Activity updated.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect('/activities')->with('status', 'Activity deleted.');
    }
}
