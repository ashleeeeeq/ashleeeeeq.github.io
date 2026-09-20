<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivitySession;
use App\Models\Attendance;
use App\Models\Beneficiary;
use App\Models\Department;
use App\Models\Event;
use App\Models\Program;
use App\Models\Staff;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private const ATTENDANCE_STATUSES = ['present', 'absent', 'late', 'excused'];
    private const DEFAULT_ATTENDANCE_METHOD = 'Staff';

    public function createSession(Request $request, Activity $activity, ActivitySession $session): View
    {
        abort_unless($session->activity_id === $activity->id, 404);

        $activity->load('program');
        $program = $activity->program;
        $search = $request->query('search');

        $beneficiaries = $activity->participantRecords()
            ->with('beneficiary')
            ->whereNotNull('beneficiary_id')
            ->whereNull('exited_at')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('beneficiary', function ($b) use ($search) {
                    $b->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('joined_at')
            ->paginate(10, ['*'], 'beneficiary_page');

        $staffMembers = $activity->participantRecords()
            ->with('staff')
            ->whereNotNull('staff_id')
            ->whereNull('exited_at')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('staff', function ($s) use ($search) {
                    $s->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('joined_at')
            ->paginate(10, ['*'], 'staff_page');

        return $this->renderAttendanceView(
            title: 'Record Activity Attendance',
            program: $program,
            beneficiaries: $beneficiaries,
            staffMembers: $staffMembers,
            existingAttendances: Attendance::query()
                ->with('updater')
                ->where('activity_session_id', $session->id)
                ->get()
                ->keyBy(fn (Attendance $attendance) => $this->attendanceKey($attendance->beneficiary_id, $attendance->staff_id)),
            actionUrl: route('activities.sessions.attendance.store', [$activity, $session]),
            contextLabel: $activity->name,
            contextSubtitle: $session->schedule?->format('M d, Y g:i A') ?? '',
            extra: [
                'pollUrl' => route('activities.sessions.attendance.poll', [$activity, $session]),
                'search' => $search,
            ],
        );
    }

    public function storeSession(Request $request, Activity $activity, ActivitySession $session): RedirectResponse
    {
        abort_unless($session->activity_id === $activity->id, 404);

        $activity->load('program');

        $validated = $request->validate([
            'beneficiary_statuses' => ['nullable', 'array'],
            'beneficiary_statuses.*' => ['nullable', Rule::in(self::ATTENDANCE_STATUSES)],
            'beneficiary_remarks' => ['nullable', 'array'],
            'beneficiary_remarks.*' => ['nullable', 'string', 'max:255'],
            'staff_statuses' => ['nullable', 'array'],
            'staff_statuses.*' => ['nullable', Rule::in(self::ATTENDANCE_STATUSES)],
            'staff_remarks' => ['nullable', 'array'],
            'staff_remarks.*' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated, $session): void {
                $this->saveAttendanceRows(
                    beneficiaryStatuses: $validated['beneficiary_statuses'] ?? [],
                    beneficiaryRemarks: $validated['beneficiary_remarks'] ?? [],
                    staffStatuses: $validated['staff_statuses'] ?? [],
                    staffRemarks: $validated['staff_remarks'] ?? [],
                    activitySessionId: $session->id,
                    eventId: null,
                );
            });
        } catch (QueryException $exception) {
            if ($this->isDuplicateAttendanceException($exception)) {
                return back()->withErrors(['attendance' => 'This person is already recorded as attending.'])->withInput();
            }

            throw $exception;
        }

        return redirect()->route('activities.show', $activity)->with('status', 'Attendance recorded.');
    }

    public function createEvent(Request $request, Event $event): View
    {
        $event->load('program');

        $selectedBeneficiaryProgramId = $request->integer('beneficiary_program_id') ?: null;
        $selectedStaffDepartmentId = $request->integer('staff_department_id') ?: null;
        $search = $request->query('search');

        $beneficiaryQuery = Beneficiary::query()
            ->with('activePrograms')
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($selectedBeneficiaryProgramId) {
            $beneficiaryQuery->whereHas('activePrograms', function ($query) use ($selectedBeneficiaryProgramId): void {
                $query->whereKey($selectedBeneficiaryProgramId);
            });
        }

        $beneficiaryQuery->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('email', 'like', "%{$search}%");
                  });
            });
        });

        $staffQuery = Staff::with('department')
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($selectedStaffDepartmentId) {
            $staffQuery->where('department_id', $selectedStaffDepartmentId);
        }

        $staffQuery->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($d) use ($search) {
                      $d->where('name', 'like', "%{$search}%");
                  });
            });
        });

        $programs = Program::orderBy('program_name')->get();
        $departments = Department::orderBy('name')->get();
        $beneficiaries = $beneficiaryQuery->paginate(10, ['*'], 'beneficiary_page')->withQueryString();
        $staffMembers = $staffQuery->paginate(10, ['*'], 'staff_page')->withQueryString();

        $queryString = array_filter([
            'beneficiary_program_id' => $selectedBeneficiaryProgramId,
            'staff_department_id' => $selectedStaffDepartmentId,
            'search' => $search,
        ], fn ($value) => $value !== null && $value !== '');

        return $this->renderAttendanceView(
            title: 'Record Event Attendance',
            program: $event->program,
            beneficiaries: $beneficiaries,
            staffMembers: $staffMembers,
            existingAttendances: Attendance::query()
                ->with('updater')
                ->where('event_id', $event->id)
                ->get()
                ->keyBy(fn (Attendance $attendance) => $this->attendanceKey($attendance->beneficiary_id, $attendance->staff_id)),
            actionUrl: route('events.attendance.store', $event) . (count($queryString) ? ('?' . http_build_query($queryString)) : ''),
            contextLabel: $event->name,
            contextSubtitle: $event->start?->format('M d, Y g:i A') ?? '',
            extra: [
                'filterUrl' => route('events.attendance.create', $event),
                'pollUrl' => route('events.attendance.poll', $event),
                'programOptions' => $programs,
                'departmentOptions' => $departments,
                'selectedBeneficiaryProgramId' => $selectedBeneficiaryProgramId,
                'selectedStaffDepartmentId' => $selectedStaffDepartmentId,
                'search' => $search,
            ],
        );
    }

    public function storeEvent(Request $request, Event $event): RedirectResponse
    {
        $event->load('program');

        $validated = $request->validate([
            'beneficiary_statuses' => ['nullable', 'array'],
            'beneficiary_statuses.*' => ['nullable', Rule::in(self::ATTENDANCE_STATUSES)],
            'beneficiary_remarks' => ['nullable', 'array'],
            'beneficiary_remarks.*' => ['nullable', 'string', 'max:255'],
            'staff_statuses' => ['nullable', 'array'],
            'staff_statuses.*' => ['nullable', Rule::in(self::ATTENDANCE_STATUSES)],
            'staff_remarks' => ['nullable', 'array'],
            'staff_remarks.*' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($event, $validated): void {
                $this->saveAttendanceRows(
                    beneficiaryStatuses: $validated['beneficiary_statuses'] ?? [],
                    beneficiaryRemarks: $validated['beneficiary_remarks'] ?? [],
                    staffStatuses: $validated['staff_statuses'] ?? [],
                    staffRemarks: $validated['staff_remarks'] ?? [],
                    activitySessionId: null,
                    eventId: $event->id,
                );
            });
        } catch (QueryException $exception) {
            if ($this->isDuplicateAttendanceException($exception)) {
                return back()->withErrors(['attendance' => 'This person is already recorded as attending.'])->withInput();
            }

            throw $exception;
        }

        return redirect()->route('events.show', $event)->with('status', 'Attendance recorded.');
    }

    public function pollEvent(Request $request, Event $event): JsonResponse
    {
        $since = $request->query('since', now()->subYear()->toISOString());

        $attendances = $event->attendances()
            ->with(['beneficiary.user', 'staff.department', 'updater'])
            ->where('updated_at', '>', $since)
            ->orderBy('id')
            ->get();

        return response()->json([
            'attendances' => $attendances->map(function ($a) {
                return [
                    'id' => $a->id,
                    'beneficiary_id' => $a->beneficiary_id,
                    'staff_id' => $a->staff_id,
                    'name' => $a->beneficiary?->display_name ?? $a->staff?->display_name ?? 'Unknown',
                    'user_type' => $a->beneficiary ? 'Beneficiary' : 'Staff',
                    'attendance_status' => $a->attendance_status,
                    'remarks' => $a->remarks ?: '-',
                    'updated_by' => $a->updater?->display_name ?? '-',
                ];
            }),
            'total' => $event->attendances()->count(),
            'polled_at' => now()->toISOString(),
        ]);
    }

    public function pollSession(Request $request, Activity $activity, ActivitySession $session): JsonResponse
    {
        abort_unless($session->activity_id === $activity->id, 404);

        $since = $request->query('since', now()->subYear()->toISOString());

        $attendances = $session->attendances()
            ->with(['beneficiary.user', 'staff.department', 'updater'])
            ->where('updated_at', '>', $since)
            ->orderBy('id')
            ->get();

        return response()->json([
            'attendances' => $attendances->map(function ($a) {
                return [
                    'id' => $a->id,
                    'beneficiary_id' => $a->beneficiary_id,
                    'staff_id' => $a->staff_id,
                    'name' => $a->beneficiary?->display_name ?? $a->staff?->display_name ?? 'Unknown',
                    'user_type' => $a->beneficiary ? 'Beneficiary' : 'Staff',
                    'attendance_status' => $a->attendance_status,
                    'remarks' => $a->remarks ?: '-',
                    'updated_by' => $a->updater?->display_name ?? '-',
                ];
            }),
            'total' => $session->attendances()->count(),
            'polled_at' => now()->toISOString(),
        ]);
    }

    private function renderAttendanceView(
        string $title,
        ?Program $program,
        $beneficiaries,
        $staffMembers,
        $existingAttendances,
        string $actionUrl,
        string $contextLabel,
        string $contextSubtitle,
        array $extra = [],
    ): View {
        return view('attendance.record', array_merge([
            'name' => Auth::user()?->display_name ?? 'Staff',
            'title' => $title,
            'program' => $program,
            'beneficiaries' => $beneficiaries,
            'staffMembers' => $staffMembers,
            'existingAttendances' => $existingAttendances,
            'actionUrl' => $actionUrl,
            'contextLabel' => $contextLabel,
            'contextSubtitle' => $contextSubtitle,
            'attendanceStatuses' => self::ATTENDANCE_STATUSES,
        ], $extra));
    }

    private function saveAttendanceRows(
        array $beneficiaryStatuses,
        array $beneficiaryRemarks,
        array $staffStatuses,
        array $staffRemarks,
        ?int $activitySessionId,
        ?int $eventId
    ): void {
        foreach ($beneficiaryStatuses as $beneficiaryId => $status) {
            if (!in_array($status, self::ATTENDANCE_STATUSES, true)) {
                Attendance::where('beneficiary_id', (int) $beneficiaryId)
                    ->where('activity_session_id', $activitySessionId)
                    ->where('event_id', $eventId)
                    ->delete();
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'beneficiary_id' => (int) $beneficiaryId,
                    'activity_session_id' => $activitySessionId,
                    'event_id' => $eventId,
                ],
                [
                    'staff_id' => null,
                    'attendance_status' => $status,
                    'attendance_method' => self::DEFAULT_ATTENDANCE_METHOD,
                    'remarks' => $beneficiaryRemarks[$beneficiaryId] ?? null,
                    'created_by' => Auth::user()?->staff?->id,
                    'updated_by' => Auth::user()?->staff?->id,
                ]
            );
        }

        foreach ($staffStatuses as $staffId => $status) {
            if (!in_array($status, self::ATTENDANCE_STATUSES, true)) {
                Attendance::where('staff_id', (int) $staffId)
                    ->where('activity_session_id', $activitySessionId)
                    ->where('event_id', $eventId)
                    ->delete();
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'staff_id' => (int) $staffId,
                    'activity_session_id' => $activitySessionId,
                    'event_id' => $eventId,
                ],
                [
                    'beneficiary_id' => null,
                    'attendance_status' => $status,
                    'attendance_method' => self::DEFAULT_ATTENDANCE_METHOD,
                    'remarks' => $staffRemarks[$staffId] ?? null,
                    'created_by' => Auth::user()?->staff?->id,
                    'updated_by' => Auth::user()?->staff?->id,
                ]
            );
        }
    }

    private function programStaff(Program $program)
    {
        return Staff::with('department')
            ->whereHas('department', function ($query) use ($program) {
                $query->whereRaw('LOWER(TRIM(name)) = ?', [Str::lower(trim($program->program_name))]);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    private function staffBelongsToProgram(int $staffId, Program $program): bool
    {
        $staff = Staff::with('department')->find($staffId);
        return $staff ? $staff->matchesProgramModel($program) : false;
    }

    private function staffProgramName(?Staff $staff): ?string
    {
        return $staff?->department?->name ? Str::lower(trim($staff->department->name)) : null;
    }

    private function attendanceKey(?int $beneficiaryId, ?int $staffId): string
    {
        return $beneficiaryId ? 'beneficiary-'.$beneficiaryId : 'staff-'.$staffId;
    }

    private function isDuplicateAttendanceException(QueryException $exception): bool
    {
        return $exception->getCode() === '23000' || Str::contains($exception->getMessage(), ['Duplicate entry', 'unique']);
    }
}
