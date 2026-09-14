<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Program;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    private const COMPETITION_TYPES = ['sports & athletic', 'academic', 'technical & professional', 'creative & leisure'];
    private const COMPETITION_SCALES = ['local', 'regional', 'national', 'international'];

    /**
     * Display a listing of competitions.
     */
    public function index(): View
    {
        $user = Auth::user();
        $staff = $user?->staff;
        $isAdminOrDirector = $staff?->hasAnyRole(['administrator', 'executive_director']);

        $query = Competition::with(['program', 'creator', 'updater'])->withCount('competitionResults');

        $filter = request('filter', 'upcoming');

        if ($filter === 'completed') {
            $query->where('end', '<', now());
        } else {
            $query->where(function ($q) {
                $q->whereNull('end')
                    ->orWhere('end', '>=', now());
            });
        }

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%")
                  ->orWhereHas('program', fn($p) => $p->where('program_name', 'like', "%{$search}%"));
            });
        }

        // If not admin/director, scope to user's program
        if (!$isAdminOrDirector && $staff) {
            $staffProgram = $this->getStaffProgram($staff);
            if ($staffProgram) {
                $query->where('program_id', $staffProgram->id);
            }
        }

        $competitions = $query->latest('start')->paginate(12, ['*'], 'competitions_page')->withQueryString();

        return view('competitions.index', [
            'name' => $user?->display_name ?? 'Staff',
            'competitions' => $competitions,
            'currentFilter' => $filter,
        ]);
    }

    /**
     * Show the form for creating a new competition.
     */
    public function create(): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);
        $programs = Program::orderBy('program_name')->get();

        $userProgram = null;
        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = $programs->first(function ($program) use ($deptName) {
                return Str::lower(trim($program->program_name)) === $deptName;
            });
        }

        return view('competitions.create', [
            'name' => $user?->display_name ?? 'Staff',
            'competition' => new Competition(),
            'programs' => $programs,
            'showProgramSelector' => $isAdminOrDirector || !$userProgram,
            'userProgram' => $userProgram,
            'competitionTypes' => self::COMPETITION_TYPES,
            'competitionScales' => self::COMPETITION_SCALES,
        ]);
    }

    /**
     * Store a newly created competition.
     */
    public function store(Request $request): RedirectResponse
    {
        // Merge start date and time
        $startDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->input('start_date') . ' ' . $request->input('start_time')
        );

        // Merge end date and time if provided
        $endDateTime = null;
        if ($request->filled('end_date') && $request->filled('end_time')) {
            $endDateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->input('end_date') . ' ' . $request->input('end_time')
            );
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'type' => ['required', Rule::in(self::COMPETITION_TYPES)],
            'scale' => ['required', Rule::in(self::COMPETITION_SCALES)],
            'organizer' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        // For non-admin, enforce program scoping
        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = Program::query()
                ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                ->first();

            if ($userProgram) {
                $validated['program_id'] = $userProgram->id;
            }
        }

        Competition::create([
            'name' => $validated['name'],
            'program_id' => $validated['program_id'],
            'type' => $validated['type'],
            'scale' => $validated['scale'],
            'organizer' => $validated['organizer'],
            'description' => $validated['description'] ?? null,
            'venue' => $validated['venue'],
            'start' => $startDateTime,
            'end' => $endDateTime,
            'created_by' => $user?->staff?->id,
            'updated_by' => $user?->staff?->id,
        ]);

        return redirect()->route('competitions.index')->with('status', 'Competition created.');
    }

    /**
     * Display the specified competition.
     */
    public function show(Competition $competition): View
    {
        $competition->load(['program', 'competitionResults.beneficiary', 'creator', 'updater']);

        return view('competitions.show', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'competition' => $competition,
        ]);
    }

    /**
     * Show the form for editing the specified competition.
     */
    public function edit(Competition $competition): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);
        $programs = Program::orderBy('program_name')->get();

        $userProgram = null;
        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = $programs->first(function ($program) use ($deptName) {
                return Str::lower(trim($program->program_name)) === $deptName;
            });
        }

        return view('competitions.edit', [
            'name' => $user?->display_name ?? 'Staff',
            'competition' => $competition->load('program'),
            'programs' => $programs,
            'showProgramSelector' => $isAdminOrDirector || !$userProgram,
            'userProgram' => $userProgram,
            'competitionTypes' => self::COMPETITION_TYPES,
            'competitionScales' => self::COMPETITION_SCALES,
        ]);
    }

    /**
     * Update the specified competition.
     */
    public function update(Request $request, Competition $competition): RedirectResponse
    {
        // Merge start date and time
        $startDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->input('start_date') . ' ' . $request->input('start_time')
        );

        // Merge end date and time if provided
        $endDateTime = null;
        if ($request->filled('end_date') && $request->filled('end_time')) {
            $endDateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->input('end_date') . ' ' . $request->input('end_time')
            );
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'type' => ['required', Rule::in(self::COMPETITION_TYPES)],
            'scale' => ['required', Rule::in(self::COMPETITION_SCALES)],
            'organizer' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        // For non-admin, enforce program scoping
        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = Program::query()
                ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                ->first();

            if ($userProgram) {
                $validated['program_id'] = $userProgram->id;
            }
        }

        $competition->update([
            'name' => $validated['name'],
            'program_id' => $validated['program_id'],
            'type' => $validated['type'],
            'scale' => $validated['scale'],
            'organizer' => $validated['organizer'],
            'description' => $validated['description'] ?? null,
            'venue' => $validated['venue'],
            'start' => $startDateTime,
            'end' => $endDateTime,
            'updated_by' => $user?->staff?->id,
        ]);

        return redirect()->route('competitions.show', $competition)->with('status', 'Competition updated.');
    }

    /**
     * Delete the specified competition.
     */
    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();

        return redirect()->route('competitions.index')->with('status', 'Competition deleted.');
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
}
