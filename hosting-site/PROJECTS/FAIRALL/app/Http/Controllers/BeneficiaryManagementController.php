<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryStatus;
use App\Models\BeneficiaryStatusType;
use App\Models\EducationEnrollment;
use App\Models\Program;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\BeneficiaryAttendanceAlert;
use App\Notifications\BeneficiaryGwaAlert;
use App\Notifications\BeneficiarySchoolAttendanceAlert;
use App\Services\BeneficiaryAnalyticsService;
use App\Services\FileUploadService;
use App\Services\GlobalAddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class BeneficiaryManagementController extends Controller
{
    private const PROGRAM_SPORTS = 'Sports';
    private const PROGRAM_EDUCATION = 'Education';
    private const PROGRAM_NAMES = [self::PROGRAM_SPORTS, self::PROGRAM_EDUCATION];

    public function __construct(private FileUploadService $fileUploadService) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $isAdmin = $user->staff?->hasAnyRole(['administrator', 'executive_director']) ?? false;

        $query = Beneficiary::with(['user', 'activePrograms']);

        if (!$isAdmin) {
            $program = $user->staff?->staffProgram();

            if ($program) {
                $query->whereHas('activePrograms', function ($q) use ($program) {
                    $q->where('programs.id', $program->id);
                });
            }
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('login_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($isAdmin && $request->filled('program_id')) {
            $programId = $request->query('program_id');
            if ($programId === 'no_programs') {
                $query->whereDoesntHave('activePrograms');
            } else {
                $query->whereHas('activePrograms', fn($q) => $q->where('programs.id', $programId));
            }
        }

        $beneficiaries = $query->latest('id')->paginate(10)->withQueryString();

        return view('beneficiaries.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiaries' => $beneficiaries,
            'programs' => Program::orderBy('program_name')->get(),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();
        $isAdmin = $user?->staff?->hasAnyRole(['administrator', 'executive_director']) ?? false;

        $query = Beneficiary::query()->with('user:id,login_id,email');

        if (!$isAdmin) {
            $program = $user?->staff?->staffProgram();

            if ($program) {
                $query->whereHas('activePrograms', function ($sub) use ($program) {
                    $sub->where('programs.id', $program->id);
                });
            }
        }

        $query->where(function ($sub) use ($q) {
            $sub->where('first_name', 'like', "%{$q}%")
                ->orWhere('middle_name', 'like', "%{$q}%")
                ->orWhere('last_name', 'like', "%{$q}%")
                ->orWhereHas('user', fn($u) => $u->where('login_id', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%"));
        });

        $results = $query->latest('id')->limit(15)->get(['id', 'first_name', 'middle_name', 'last_name', 'user_id']);

        return response()->json($results->map(fn(Beneficiary $b) => [
            'id' => $b->id,
            'label' => $b->display_name,
            'login_id' => $b->user->login_id ?? null,
            'email' => $b->user->email ?? null,
        ])->values());
    }

    public function create(Request $request): View
    {
        $staff = Auth::user()?->staff;
        $isAdministrator = $staff?->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR]) ?? false;

        if ($isAdministrator && !$request->filled('program_id')) {
            $programs = Program::query()
                ->whereIn('program_name', self::PROGRAM_NAMES)
                ->orderBy('program_name')
                ->get();

            return view('beneficiaries.select-program', [
                'name' => Auth::user()?->display_name ?? 'Staff',
                'programs' => $programs,
            ]);
        }

        $programId = (int) $request->input('program_id');
        $selectedProgram = $this->resolveProgramForCreate($programId, $staff, $isAdministrator);

        $status_types = DB::table('beneficiary_status_types')
            ->join('beneficiary_status_type_program', 'beneficiary_status_types.id', '=', 'beneficiary_status_type_program.beneficiary_status_type_id')
            ->join('programs', 'beneficiary_status_type_program.program_id', '=', 'programs.id')
            ->where('beneficiary_status_type_program.program_id', $selectedProgram->id)
            ->select('beneficiary_status_types.id', 'beneficiary_status_types.status_name', 'programs.program_name')
            ->orderBy('beneficiary_status_types.status_name')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->id => $item->status_name . ' (' . $item->program_name . ')',
            ]);

        return view('beneficiaries.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'isAdministrator' => $isAdministrator,
            'selectedProgram' => $selectedProgram,
            'programId' => $selectedProgram->id,
            'isEducation' => $selectedProgram->program_name === self::PROGRAM_EDUCATION,
            'status_types' => $status_types,
        ]);
    }

    public function profile(Request $request, Beneficiary $beneficiary): View
    {
        $staff = Auth::user()?->staff;
        $beneficiary->load(['user', 'programs', 'activePrograms', 'statuses.statusType', 'academicRecords.subjectGrades', 'educationEnrollments', 'ffaAssessmentRecords.assessmentCategory', 'address', 'guardians', 'intakeSheet']);

        $latestAcademicRecord = $beneficiary->academicRecords()
            ->with('educationEnrollment')
            ->orderByDesc(
                EducationEnrollment::select('academic_year_end_date')
                    ->whereColumn('education_enrollments.id', 'academic_records.education_enrollment_id')
            )
            ->first();

        $latestEnrollment = $beneficiary->educationEnrollments
            ->sortByDesc(fn($en) => [$en->academic_year_start_date, $en->academic_year_end_date])
            ->first();

        // Determine beneficiary program type
        $activePrograms = $beneficiary->activePrograms;
        $programCount = $activePrograms->count();
        $programType = match (true) {
            $programCount === 0 => 'none',
            $programCount >= 2 => 'mixed',
            default => strtolower(trim((string) $activePrograms->first()->program_name)),
        };

        $educationProgramId = Program::query()->where('program_name', self::PROGRAM_EDUCATION)->value('id');
        $sportsProgramId = Program::query()->where('program_name', self::PROGRAM_SPORTS)->value('id');

        $analyticsService = app(BeneficiaryAnalyticsService::class);
        $filters = $analyticsService->resolveFilters($request->all());

        $selectedProgramId = match ($programType) {
            'mixed' => (int) $request->input('program_id', $educationProgramId),
            'education' => $educationProgramId,
            'sports' => $sportsProgramId,
            default => (int) $request->input('program_id', $educationProgramId),
        };

        $analyticsData = $analyticsService->compute($beneficiary, $filters, $selectedProgramId);

        $programs = Program::query()
            ->whereIn('program_name', self::PROGRAM_NAMES)
            ->orderBy('program_name')
            ->get(['id', 'program_name']);

        $addressDisplay = collect([
            $beneficiary->address?->address_line,
            $beneficiary->address?->city,
            $beneficiary->address?->province,
            $beneficiary->address?->zip,
            $beneficiary->address?->country,
        ])->filter()->implode(', ');

        $activeStatuses = $beneficiary->statuses
            ->filter(fn(BeneficiaryStatus $status) => is_null($status->end_date) || $status->end_date->isToday() || $status->end_date->isFuture())
            ->values();

        // Check if staff is an admin or director
        $hasRoleAccess = $staff?->hasAnyRole(['administrator', 'executive_director']);

        // Check if staff's department matches any of the beneficiary's program names
        $hasProgramAccess = $staff ? $staff->matchesProgram($beneficiary) : false;

        // If either condition is true, grant access to the profile view
        abort_unless($hasProgramAccess || $hasRoleAccess, 403, 'You are unauthorized to view this beneficiary.');

        return view('beneficiaries.profile', [
            'staff' => $staff,
            'name' => $staff->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'activeStatuses' => $activeStatuses,
            'latestAcademicRecord' => $latestAcademicRecord,
            'latestEnrollment' => $latestEnrollment ?? null,
            'addressDisplay' => $addressDisplay,
            'analytics' => $analyticsData,
            'filters' => $filters,
            'programType' => $programType,
            'programs' => $programs,
            'activePrograms' => $activePrograms,
            'selectedProgramId' => $selectedProgramId,
            'educationProgramId' => $educationProgramId,
            'sportsProgramId' => $sportsProgramId,
        ]);
    }

    public function alerts(Request $request, Beneficiary $beneficiary): View
    {
        $staff = Auth::user()?->staff;

        $hasRoleAccess = $staff?->hasAnyRole(['administrator', 'executive_director']);
        $hasProgramAccess = $staff ? $staff->matchesProgram($beneficiary) : false;
        abort_unless($hasProgramAccess || $hasRoleAccess, 403, 'You are unauthorized to view this beneficiary.');

        $alertTypes = [
            BeneficiaryAttendanceAlert::class,
            BeneficiaryGwaAlert::class,
            BeneficiarySchoolAttendanceAlert::class,
        ];

        $alerts = DB::table('notifications')
            ->whereIn('id', function ($query) use ($beneficiary, $alertTypes) {
                $query->select(DB::raw('MIN(id)'))
                    ->from('notifications')
                    ->where('data->beneficiary_id', $beneficiary->id)
                    ->whereIn('type', $alertTypes)
                    ->groupBy('type', 'data');
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('beneficiaries.alerts', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'alerts' => $alerts,
        ]);
    }

    // enrollment-related controllers

    public function enrollmentIndex(Request $request): View
    {
        $user = Auth::user();
        $staff = $user?->staff;
        $isAdmin = $staff?->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR]) ?? false;
        $manageableProgramIds = $this->manageableProgramIds($user);

        // Admin/executive director: show all programs; program staff: show only manageable programs
        $programs = Program::query()
            ->when(!$isAdmin, fn($q) => $q->whereIn('id', $manageableProgramIds))
            ->orderBy('program_name')
            ->get();

        // Search across beneficiary name, login ID, or email
        $search = $request->input('search');

        // Always show the list of beneficiaries (staff may enroll any beneficiary into their manageable program)
        $beneficiaries = Beneficiary::query()
            ->with('activePrograms')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQ) use ($search) {
                            $userQ->where('login_id', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $beneficiariesByProgram = $programs->mapWithKeys(function (Program $program) use ($beneficiaries) {
            $items = $beneficiaries->map(function (Beneficiary $beneficiary) use ($program) {
                $isActive = $beneficiary->activePrograms->contains('id', $program->id);

                return [
                    'beneficiary' => $beneficiary,
                    'is_active' => $isActive,
                ];
            })->values();

            return [$program->id => $items];
        });

        return view('beneficiaries.enrollment', [
            'name' => $user?->display_name ?? 'Staff',
            'programs' => $programs,
            'beneficiariesByProgram' => $beneficiariesByProgram,
            'manageableProgramIds' => $manageableProgramIds,
            'beneficiaries' => $beneficiaries,
            'totalBeneficiaries' => $beneficiaries->total(),
        ]);
    }

    public function enrollToProgram(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'beneficiary_id' => ['required', 'integer', 'exists:beneficiaries,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $user = Auth::user();
        $manageableProgramIds = $this->manageableProgramIds($user);

        if (!in_array((int) $validated['program_id'], $manageableProgramIds, true)) {
            abort(403, 'You are not allowed to enroll beneficiaries in this program.');
        }

        $beneficiaryId = (int) $validated['beneficiary_id'];
        $programId = (int) $validated['program_id'];
        $staffId = $user?->staff?->id;

        $activeExists = DB::table('beneficiary_program_memberships')
            ->where('beneficiary_id', $beneficiaryId)
            ->where('program_id', $programId)
            ->whereNull('exited_at')
            ->exists();

        if ($activeExists) {
            return redirect()->route('beneficiaries.enrollment.index')
                ->with('status', 'Beneficiary is already actively enrolled in that program.');
        }

        $programName = Program::query()->whereKey($programId)->value('program_name');

        // For Education program, require intake form completion before enrollment
        if ($programName === self::PROGRAM_EDUCATION) {
            return redirect()->route('beneficiaries.education-intake.create', ['beneficiary' => $beneficiaryId])
                ->with('status', 'Please complete the education intake form to finish enrollment.');
        }

        // For other programs, create membership immediately
        DB::table('beneficiary_program_memberships')->insert([
            'beneficiary_id' => $beneficiaryId,
            'program_id' => $programId,
            'enrolled_at' => now(),
            'created_by' => $staffId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('beneficiaries.enrollment.index')
            ->with('status', 'Beneficiary enrolled successfully.');
    }

    public function unenrollFromProgram(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'beneficiary_id' => ['required', 'integer', 'exists:beneficiaries,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $user = Auth::user();
        $manageableProgramIds = $this->manageableProgramIds($user);

        if (!in_array((int) $validated['program_id'], $manageableProgramIds, true)) {
            abort(403, 'You are not allowed to unenroll beneficiaries from this program.');
        }

        $beneficiaryId = (int) $validated['beneficiary_id'];
        $programId = (int) $validated['program_id'];
        $staffId = $user?->staff?->id;

        try {
            DB::transaction(function () use ($beneficiaryId, $programId, $staffId): void {
                $exitedAt = now();

                // 1. Set exited_at on the program membership
                $updated = DB::table('beneficiary_program_memberships')
                    ->where('beneficiary_id', $beneficiaryId)
                    ->where('program_id', $programId)
                    ->whereNull('exited_at')
                    ->update([
                        'exited_at' => $exitedAt,
                        'updated_at' => $exitedAt,
                    ]);

                if ($updated === 0) {
                    throw new \RuntimeException('Beneficiary is not actively enrolled in that program.');
                }

                // 2. End all currently active statuses for this beneficiary
                BeneficiaryStatus::where('beneficiary_id', $beneficiaryId)
                    ->whereNull('end_date')
                    ->update(['end_date' => $exitedAt->toDateString()]);

                // 3. Add "Exited" status record
                $exitedStatusTypeId = BeneficiaryStatusType::where('status_name', 'Exited')->value('id');

                if ($exitedStatusTypeId) {
                    BeneficiaryStatus::create([
                        'beneficiary_id' => $beneficiaryId,
                        'beneficiary_status_type_id' => $exitedStatusTypeId,
                        'start_date' => $exitedAt->toDateString(),
                        'end_date' => null,
                        'created_by' => $staffId,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('beneficiaries.enrollment.index')
                ->with('status', $e->getMessage());
        }

        return redirect()->route('beneficiaries.enrollment.index')
            ->with('status', 'Beneficiary unenrolled successfully.');
    }

    public function store(Request $request, GlobalAddressService $addressService): RedirectResponse|JsonResponse
    {
        $staff = Auth::user()?->staff;
        $staffId = $staff?->id;
        $isAdministrator = $staff?->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR]) ?? false;
        $programId = (int) $request->input('program_id');
        $selectedProgram = $this->resolveProgramForCreate($programId, $staff, $isAdministrator);
        $isEducation = $selectedProgram->program_name === self::PROGRAM_EDUCATION;

        $validated = $request->validate(array_merge([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'first_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'last_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'name_extension' => ['nullable', 'string', 'max:10', 'not_regex:/\d/'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'sex' => ['required', Rule::in(Beneficiary::SEX_OPTIONS)],
            'address_line' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'status' => [
                'required',
                Rule::exists('beneficiary_status_types', 'id')
                    ->where(fn($query) => $query->whereIn('id', fn($q) => $q
                        ->select('beneficiary_status_type_id')
                        ->from('beneficiary_status_type_program')
                        ->where('program_id', $selectedProgram->id)
                    )),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'form_given' => ['sometimes', 'boolean'],
            'with_disability' => ['sometimes', 'boolean'],
            'document_file' => ['nullable', 'array'],
            'document_file.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ], $this->guardianRules(), $this->intakeRules($isEducation)), [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
            'name_extension.not_regex' => 'The name extension must not contain numbers.',
            'guardians.*.first_name.not_regex' => 'The guardian first name must not contain numbers.',
            'guardians.*.middle_name.not_regex' => 'The guardian middle name must not contain numbers.',
            'guardians.*.last_name.not_regex' => 'The guardian last name must not contain numbers.',
        ]);

        $defaultPassword = 'Password123!';
        $currentYear = now()->year;
        $location = $addressService->validateLocation($validated);

        $beneficiary = null;

        DB::transaction(function () use ($validated, $staffId, $defaultPassword, $currentYear, $location, $request, $selectedProgram, $isEducation, $addressService, &$beneficiary): void {
            $user = User::create([
                'login_id' => null,  // Will be set after beneficiary is created
                'email' => $validated['email'],
                'password' => $defaultPassword,
                'is_active' => true,
                'user_type' => 'beneficiary',
            ]);

            $beneficiary = Beneficiary::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'birth_date' => $validated['birth_date'],
                'sex' => $validated['sex'],
                'contact_number' => $validated['contact_number'],
                'dial_code' => $validated['contact_number_code'],
                'form_given' => (bool) ($validated['form_given'] ?? false),
                'with_disability' => (bool) ($validated['with_disability'] ?? false),
                'created_by' => $staffId,
            ]);

            $beneficiary->address()->create($location);

            // Generate and set login_id now that we have the beneficiary id
            $loginId = "BEN-{$currentYear}-{$beneficiary->id}";
            $user->update(['login_id' => $loginId]);

            // insert program enrollment with history metadata
            $beneficiary->programs()->attach($selectedProgram->id, [
                'enrolled_at' => now(),
                'created_by' => $staffId,
            ]);

            BeneficiaryStatus::create([
                'beneficiary_id' => $beneficiary->id,
                'beneficiary_status_type_id' => $validated['status'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'created_by' => $staffId,
            ]);

            if ($request->hasFile('document_file')) {
                $files = $request->file('document_file');
                $files = is_array($files) ? $files : [$files];

                foreach ($files as $file) {
                    $this->fileUploadService->uploadFile(
                        file: $file,
                        beneficiary_id: $beneficiary->id,
                        source_module: 'intake',
                        uploaded_by: Auth::id()
                    );
                }
            }

            $this->syncGuardians($beneficiary, $validated['guardians'] ?? [], $staffId, $addressService);

            if ($isEducation) {
                $this->upsertIntakeSheet($beneficiary, $validated['intake'] ?? [], $staffId);
            }
        });

        if ($request->wantsJson()) {
            session()->flash('status', 'Beneficiary created successfully.');
            return response()->json([
                'message' => 'Beneficiary created successfully.',
                'beneficiary_id' => $beneficiary->id,
                'redirect' => url('/beneficiaries'),
            ]);
        }

        return redirect('/beneficiaries')->with('status', 'Beneficiary created successfully.');
    }

    public function edit(Request $request, Beneficiary $beneficiary): View
    {
        $beneficiary->load(['user', 'programs', 'activePrograms', 'address']);
        $addressParts = $beneficiary->address?->toFormArray() ?? [];
        $staff = Auth::user()?->staff;
        $isAdministrator = $staff?->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR]) ?? false;
        $selectedProgram = $this->resolveProgramForEdit($request, $beneficiary, $staff, $isAdministrator);

        $availablePrograms = $this->beneficiaryPrograms($beneficiary)
            ->filter(fn(Program $program) => in_array($program->program_name, self::PROGRAM_NAMES, true))
            ->values();

        return view('beneficiaries.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'addressParts' => $addressParts,

            'isAdministrator' => $isAdministrator,
            'availablePrograms' => $availablePrograms,
            'selectedProgram' => $selectedProgram,
            'programId' => $selectedProgram->id,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary, GlobalAddressService $addressService): RedirectResponse
    {
        $beneficiary->load('user', 'programs', 'activePrograms');

        $staff = Auth::user()?->staff;
        $staffId = $staff?->id;
        $isAdministrator = $staff?->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR]) ?? false;
        $selectedProgram = $this->resolveProgramForEdit($request, $beneficiary, $staff, $isAdministrator);

        $validated = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($beneficiary->user_id)],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'first_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'last_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'name_extension' => ['nullable', 'string', 'max:10', 'not_regex:/\d/'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'sex' => ['required', Rule::in(Beneficiary::SEX_OPTIONS)],
            'address_line' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'form_given' => ['sometimes', 'boolean'],
            'with_disability' => ['sometimes', 'boolean'],
            'document_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ], [
            'email.unique' => 'This email is already in use. If the account was archived, restore or permanently delete it from the Archive before reusing this email.',
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
            'name_extension.not_regex' => 'The name extension must not contain numbers.',
        ]);

        $location = $addressService->validateLocation($validated);

        DB::transaction(function () use ($validated, $beneficiary, $staffId, $location): void {
            // Update user only if data changed
            $userData = [
                'email' => $validated['email'],
                'is_active' => true,
                'user_type' => 'beneficiary',
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = $validated['password'];
            }

            $userChanges = array_diff_assoc($userData, $beneficiary->user->only(array_keys($userData)));
            if (!empty($userChanges)) {
                $beneficiary->user?->update($userChanges);
            }

            // Update beneficiary only if data changed
            $beneficiaryData = [
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'birth_date' => $validated['birth_date'],
                'sex' => $validated['sex'],
                'contact_number' => $validated['contact_number'],
                'dial_code' => $validated['contact_number_code'],
                'form_given' => (bool) ($validated['form_given'] ?? false),
                'with_disability' => (bool) ($validated['with_disability'] ?? false),
                'updated_by' => $staffId,
            ];

            $beneficiaryChanges = array_diff_assoc($beneficiaryData, $beneficiary->only(array_keys($beneficiaryData)));
            if (!empty($beneficiaryChanges)) {
                $beneficiary->update($beneficiaryChanges);
            }

            // Update address only if data changed
            if ($beneficiary->address) {
                $addressChanges = array_diff_assoc($location, $beneficiary->address->only(array_keys($location)));
                if (!empty($addressChanges)) {
                    $beneficiary->address()->update($addressChanges);
                }
            } else {
                $beneficiary->address()->create($location);
            }
        });

        return redirect('/beneficiaries')->with('status', 'Beneficiary updated successfully.');
    }

    public function destroy(Beneficiary $beneficiary): RedirectResponse
    {
        $beneficiary->load('user');

        if ($beneficiary->user) {
            $beneficiary->user->delete();
        }

        $beneficiary->delete();

        return back()->with('status', 'Beneficiary moved to archive. Will be automatically deleted after 90 days.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:beneficiaries,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids): void {
            $beneficiaries = Beneficiary::whereIn('id', $ids)->get();
            foreach ($beneficiaries as $beneficiary) {
                $beneficiary->load('user');
                if ($beneficiary->user) {
                    $beneficiary->user->delete();
                }
                $beneficiary->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' beneficiaries moved to archive. Will be automatically deleted after 90 days.');
    }

    /**
     * Determine which programs the current user can manage for enrollment actions.
     * Admin/Executive Director can manage all; program roles are limited to department-matching program.
     *
     * @return int[]
     */
    private function manageableProgramIds(?User $user): array
    {
        $staff = $user?->staff;

        if (!$staff) {
            return [];
        }

        if ($staff->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR])) {
            return Program::query()->pluck('id')->map(fn($id) => (int) $id)->all();
        }

        if ($staff->hasAnyRole([Staff::ROLE_PROGRAM_MANAGER, Staff::ROLE_PROGRAM_STAFF])) {
            $program = $staff->staffProgram();
            return $program ? [(int) $program->id] : [];
        }

        return [];
    }

    private function resolveProgramForCreate(int $programId, ?Staff $staff, bool $isAdministrator): Program
    {
        if ($isAdministrator) {
            $program = Program::query()
                ->whereIn('program_name', self::PROGRAM_NAMES)
                ->whereKey($programId)
                ->first();

            if (!$program) {
                abort(404, 'Program not found.');
            }

            return $program;
        }

        $program = $staff?->staffProgram();

        if (!$program) {
            abort(403, 'No program available for your department.');
        }

        if ($programId !== 0 && $program->id !== $programId) {
            abort(403, 'Program selection is not allowed.');
        }

        return $program;
    }

    private function beneficiaryPrograms(Beneficiary $beneficiary)
    {
        $active = $beneficiary->activePrograms;

        return $active->isNotEmpty() ? $active : $beneficiary->programs;
    }

    private function resolveProgramForEdit(Request $request, Beneficiary $beneficiary, ?Staff $staff, bool $isAdministrator): Program
    {
        $programs = $this->beneficiaryPrograms($beneficiary)
            ->filter(fn(Program $program) => in_array($program->program_name, self::PROGRAM_NAMES, true))
            ->values();

        if ($programs->isEmpty()) {
            abort(404, 'No program context available.');
        }

        if ($isAdministrator) {
            $programId = (int) $request->input('program_id', $programs->first()->id);
            $selected = $programs->first(fn(Program $program) => (int) $program->id === $programId);

            if (!$selected) {
                abort(403, 'Program access is not allowed.');
            }

            return $selected;
        }

        $program = $staff?->staffProgram();

        if (!$program || !$programs->contains('id', $program->id)) {
            abort(403, 'You are not allowed to edit this beneficiary.');
        }

        return $program;
    }

    private function guardianRules(bool $includeIds = false): array
    {
        $rules = [
            'guardians' => ['required', 'array', 'min:1'],
            'guardians.*.guardian_type' => ['required', Rule::in(['mother', 'father', 'guardian'])],
            'guardians.*.first_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'guardians.*.middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'guardians.*.last_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'guardians.*.civil_status' => ['nullable', 'string', 'max:50'],
            'guardians.*.birth_date' => ['nullable', 'date'],
            'guardians.*.place_of_birth' => ['nullable', 'string', 'max:80'],
            'guardians.*.sex' => ['nullable', Rule::in(['male', 'female'])],
            'guardians.*.dial_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'guardians.*.contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'guardians.*.highest_education' => ['nullable', Rule::in(['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad'])],
            'guardians.*.job' => ['nullable', 'string', 'max:50'],
            'guardians.*.estimated_salary' => ['nullable', 'integer'],
            'guardians.*.deceased' => ['sometimes', 'boolean'],
            'guardians.*.address_line' => ['nullable', 'string', 'max:255'],
            'guardians.*.country' => ['nullable', 'string', 'max:100'],
            'guardians.*.province' => ['nullable', 'string', 'max:100'],
            'guardians.*.city' => ['nullable', 'string', 'max:100'],
            'guardians.*.zip' => ['nullable', 'string', 'max:20'],
        ];

        if ($includeIds) {
            $rules['guardians.*.id'] = ['nullable', 'integer', 'exists:beneficiary_guardians,id'];
        }

        return $rules;
    }

    private function intakeRules(bool $required): array
    {
        if (!$required) {
            return [];
        }

        $educationLevels = ['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad'];
        $gradeLevels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

        return [
            'intake.age' => ['required', 'integer', 'min:1'],
            'intake.place_of_birth' => ['required', 'string', 'max:80'],
            'intake.number_of_siblings' => ['required', 'integer', 'min:0'],
            'intake.older_sibling_age' => ['nullable', 'integer', 'min:0'],
            'intake.younger_sibling_age' => ['nullable', 'integer', 'min:0'],
            'intake.civil_status' => ['required', 'string', 'max:50'],
            'intake.highest_education' => ['required', Rule::in($educationLevels)],
            'intake.school_name' => ['required'],
            'intake.grade_level' => ['required', Rule::in($gradeLevels)],
            'intake.other_scholarship' => ['sometimes', 'boolean'],
            'intake.scholarship_org_question' => [
                Rule::requiredIf(fn() => (bool) request('intake.other_scholarship')),
                'nullable',
                'string',
                'max:5000',
            ],
            'intake.hardworking_question' => ['required', 'string', 'max:5000'],
            'intake.dream_question' => ['required', 'string', 'max:5000'],
            'intake.scholarship_question' => ['required', 'string', 'max:5000'],
        ];
    }

    private function syncGuardians(Beneficiary $beneficiary, array $guardians, ?int $staffId, GlobalAddressService $addressService): void
    {
        $existing = $beneficiary->guardians()->get();
        $existingIds = $existing->pluck('id')->map(fn($id) => (int) $id)->all();
        $incomingIds = collect($guardians)
            ->pluck('id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->all();

        foreach ($incomingIds as $incomingId) {
            if (!in_array($incomingId, $existingIds, true)) {
                abort(403, 'Guardian access is not allowed.');
            }
        }

        foreach ($guardians as $guardianData) {
            $payload = [
                'guardian_type' => $guardianData['guardian_type'],
                'first_name' => $guardianData['first_name'],
                'middle_name' => $guardianData['middle_name'] ?? null,
                'last_name' => $guardianData['last_name'],
                'civil_status' => $guardianData['civil_status'] ?? null,
                'birth_date' => $guardianData['birth_date'] ?? null,
                'place_of_birth' => $guardianData['place_of_birth'] ?? null,
                'sex' => $guardianData['sex'] ?? null,
                'contact_number' => $guardianData['contact_number'],
                'dial_code' => $guardianData['dial_code'] ?? '+63',
                'highest_education' => $guardianData['highest_education'] ?? null,
                'job' => $guardianData['job'] ?? null,
                'estimated_salary' => $guardianData['estimated_salary'] ?? null,
                'deceased' => (bool) ($guardianData['deceased'] ?? false),
                'updated_by' => $staffId,
            ];

            $addressFields = array_filter([
                'address_line' => $guardianData['address_line'] ?? '',
                'country' => $guardianData['country'] ?? '',
                'province' => $guardianData['province'] ?? '',
                'city' => $guardianData['city'] ?? '',
                'zip' => $guardianData['zip'] ?? '',
            ], fn($v) => $v !== '');

            if (!empty($guardianData['id'])) {
                $guardianModel = $beneficiary->guardians()->where('id', (int) $guardianData['id'])->first();
                $guardianModel->update($payload);
            } else {
                $payload['created_by'] = $staffId;
                $guardianModel = $beneficiary->guardians()->create($payload);
            }

            $this->saveGuardianAddress($guardianModel, $addressFields, $addressService);
        }

        $deleteIds = array_diff($existingIds, $incomingIds);

        if (!empty($deleteIds)) {
            $beneficiary->guardians()->whereIn('id', $deleteIds)->delete();
        }
    }

    private function saveGuardianAddress($guardian, array $addressFields, GlobalAddressService $addressService): void
    {
        if (empty($addressFields)) {
            return;
        }

        if (!empty($addressFields['country'])) {
            $addressFields['country'] = $addressService->resolveCountry($addressFields['country']);
        }
        if (!empty($addressFields['province'])) {
            $addressFields['province'] = $addressService->resolveProvince(
                $addressFields['province'],
                $addressFields['country'] ?? ''
            );
        }
        if (!empty($addressFields['city'])) {
            $addressFields['city'] = $addressService->resolveCity(
                $addressFields['city'],
                $addressFields['province'] ?? '',
                $addressFields['country'] ?? ''
            );
        }

        if ($guardian->address) {
            $guardian->address()->update($addressFields);
        } else {
            $guardian->address()->create($addressFields);
        }
    }

    private function upsertIntakeSheet(Beneficiary $beneficiary, array $intake, ?int $staffId): void
    {
        $payload = [
            'age' => $intake['age'],
            'place_of_birth' => $intake['place_of_birth'],
            'number_of_siblings' => $intake['number_of_siblings'],
            'older_sibling_age' => $intake['older_sibling_age'] ?? null,
            'younger_sibling_age' => $intake['younger_sibling_age'] ?? null,
            'civil_status' => $intake['civil_status'],
            'highest_education' => $intake['highest_education'],
            'school_name' => $intake['school_name'],
            'grade_level' => $intake['grade_level'],
            'other_scholarship' => (bool) ($intake['other_scholarship'] ?? false),
            'scholarship_org_question' => $intake['scholarship_org_question'] ?? null,
            'hardworking_question' => $intake['hardworking_question'],
            'dream_question' => $intake['dream_question'],
            'scholarship_question' => $intake['scholarship_question'],
            'updated_by' => $staffId,
        ];

        if ($beneficiary->intakeSheet) {
            $beneficiary->intakeSheet()->update($payload);
            return;
        }

        $payload['created_by'] = $staffId;
        $beneficiary->intakeSheet()->create($payload);
    }
}
