<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryStatusType;
use App\Models\Beneficiary;
use App\Models\ActivityType;
use App\Models\EventType;
use App\Models\EducationIntakeSheet;
use App\Models\HomeVisit;
use App\Models\MonitoringConfiguration;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\Position;
use App\Models\Staff;
use App\Models\User;
use App\Models\SportType;
use App\Models\Program;
use App\Services\GlobalAddressService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function __construct(private GlobalAddressService $addressService) {}

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $academicYear = '2025-2026';

        $departments = ['Sports', 'Education', 'Fundraising & Donor Management', 'Administration & Finance', 'Executive'];
        $positions = [
            'System Admin',
            'Admin',
            'Finance',
            'Tutor',
            'Social Worker',
            'Community Development Worker',
            'Researcher',
            'Coach',
            'Program Manager',
            'Donor Manager',
            'Executive Director',
        ];

        foreach ($departments as $departmentName) {
            Department::firstOrCreate(['name' => $departmentName]);
        }

        Department::whereNotIn('name', $departments)->delete();

        $departmentIds = Department::whereIn('name', $departments)
            ->pluck('id', 'name')
            ->all();

        foreach ($positions as $positionName) {
            Position::firstOrCreate(['name' => $positionName]);
        }

        Position::whereNotIn('name', $positions)->delete();

        $positionIds = Position::whereIn('name', $positions)
            ->pluck('id', 'name')
            ->all();

        $programNames = ['Sports', 'Education'];

        foreach ($programNames as $programName) {
            Program::firstOrCreate(['program_name' => $programName]);
        }

        Program::whereNotIn('program_name', $programNames)->delete();

        $programIds = Program::whereIn('program_name', $programNames)
            ->pluck('id', 'program_name')
            ->all();

        $activityTypes = [
            ['name' => 'EQ Session', 'program_id' => $programIds['Education'] ?? null],
            ['name' => 'Tutorial Session', 'program_id' => $programIds['Education'] ?? null],
            ['name' => 'Training Session', 'program_id' => $programIds['Sports'] ?? null],
        ];

        foreach ($activityTypes as $activityTypeData) {
            ActivityType::updateOrCreate(['name' => $activityTypeData['name']], $activityTypeData);
        }

        ActivityType::whereNotIn('name', array_column($activityTypes, 'name'))->delete();

        // $activityTypeIds = ActivityType::whereIn('name', array_column($activityTypes, 'name'))
        //     ->pluck('id', 'name')
        //     ->all();

        $eventTypes = [
            'Sports and Life Skills',
            'Social',
            'Outreach',
        ];

        foreach ($eventTypes as $eventTypeName) {
            EventType::firstOrCreate(['name' => $eventTypeName]);
        }

        EventType::whereNotIn('name', $eventTypes)->delete();

        $sportTypes = ['Futsal', 'Badminton', 'Basketball', 'Volleyball'];

        foreach ($sportTypes as $sportTypeName) {
            SportType::firstOrCreate(['name' => $sportTypeName]);
        }

        SportType::whereNotIn('name', $sportTypes)->delete();

        // $sportTypeIds = SportType::whereIn('name', $sportTypes)
        //     ->pluck('id', 'name')
        //     ->all();

        $assessmentCategories = ['Quiz', 'Research', 'Presentation', 'Socio-Emotional'];

        foreach ($assessmentCategories as $assessmentCategoryName) {
            DB::table('assessment_categories')->updateOrInsert(
                ['assessment_name' => $assessmentCategoryName],
                []
            );
        }

        DB::table('assessment_categories')->whereNotIn('assessment_name', $assessmentCategories)->delete();

        $assessmentCategoryIds = DB::table('assessment_categories')
            ->whereIn('assessment_name', $assessmentCategories)
            ->pluck('id', 'assessment_name')
            ->all();

        $statusTypes = [
            [
                'status_name' => 'Exited',
                'programs' => ['Sports', 'Education'],
            ],
            [
                'status_name' => 'Probation Scholar',
                'programs' => ['Education'],
            ],
            [
                'status_name' => 'Full Scholar',
                'programs' => ['Education'],
            ],
            [
                'status_name' => 'Varsity Scholar',
                'programs' => ['Sports', 'Education'],
            ],
            [
                'status_name' => 'Unregistered Player',
                'programs' => ['Sports'],
            ],
            [
                'status_name' => 'Registered Player',
                'programs' => ['Sports'],
            ],
        ];

        $seededIds = [];

        foreach ($statusTypes as $statusType) {
            $type = BeneficiaryStatusType::firstOrCreate([
                'status_name' => $statusType['status_name'],
            ]);

            $seededIds[] = $type->id;

            $programIdsToAttach = collect($statusType['programs'])
                ->map(fn($name) => $programIds[$name] ?? null)
                ->filter()
                ->values()
                ->all();

            if (! empty($programIdsToAttach)) {
                $type->programs()->syncWithoutDetaching($programIdsToAttach);
            }
        }

        // Delete any status types not in the seeded list
        BeneficiaryStatusType::whereNotIn('id', $seededIds)->delete();

        $adminDepartmentId = $departmentIds['Administration & Finance'] ?? null;
        $executiveDepartmentId = $departmentIds['Executive'] ?? null;
        $sportsDepartmentId = $departmentIds['Sports'] ?? null;
        $educationDepartmentId = $departmentIds['Education'] ?? null;
        $fundraisingDepartmentId = $departmentIds['Fundraising & Donor Management'] ?? null;

        $systemAdminPositionId = $positionIds['System Admin'] ?? null;
        $executiveDirectorPositionId = $positionIds['Executive Director'] ?? null;
        $programManagerPositionId = $positionIds['Program Manager'] ?? null;
        $donorManagerPositionId = $positionIds['Donor Manager'] ?? null;
        $coachPositionId = $positionIds['Coach'] ?? null;
        $socialWorkerPositionId = $positionIds['Social Worker'] ?? null;
        $tutorPositionId = $positionIds['Tutor'] ?? null;
        $adminPositionId = $positionIds['Admin'] ?? null;

        $staffUsers = [
            [
                'email' => 'admin@example.com',
                'login_id' => 'STAFF-2026-1',
                'first_name' => 'Administrator',
                'middle_name' => null,
                'last_name' => 'User',
                'department_id' => $adminDepartmentId,
                'position_id' => $systemAdminPositionId,
                'contact_number' => '9170000001',
                'dial_code' => '+63',
                'address' => '123 Administration Avenue, Quezon City, Metro Manila, 1100, Philippines',
                'role' => 'administrator',
            ],
            [
                'email' => 'executive_director@example.com',
                'login_id' => 'STAFF-2026-2',
                'first_name' => 'Elena',
                'middle_name' => 'M.',
                'last_name' => 'Cruz',
                'department_id' => $executiveDepartmentId,
                'position_id' => $executiveDirectorPositionId,
                'contact_number' => '9170000002',
                'dial_code' => '+63',
                'address' => '45 Leadership Street, Quezon City, Metro Manila, 1102, Philippines',
                'role' => 'executive_director',
            ],
            [
                'email' => 'program_manager_sports@example.com',
                'login_id' => 'STAFF-2026-3',
                'first_name' => 'Marco',
                'middle_name' => 'R.',
                'last_name' => 'Dela Cruz',
                'department_id' => $sportsDepartmentId,
                'position_id' => $programManagerPositionId,
                'contact_number' => '9170000003',
                'dial_code' => '+63',
                'address' => '18 Stadium Drive, Pasig City, Metro Manila, 1600, Philippines',
                'role' => 'program_manager',
            ],
            [
                'email' => 'program_manager_education@example.com',
                'login_id' => 'STAFF-2026-4',
                'first_name' => 'Angela',
                'middle_name' => 'S.',
                'last_name' => 'Navarro',
                'department_id' => $educationDepartmentId,
                'position_id' => $programManagerPositionId,
                'contact_number' => '9170000004',
                'dial_code' => '+63',
                'address' => '22 Scholar Lane, Quezon City, Metro Manila, 1103, Philippines',
                'role' => 'program_manager',
            ],
            [
                'email' => 'donor_manager@example.com',
                'login_id' => 'STAFF-2026-5',
                'first_name' => 'Miguel',
                'middle_name' => 'A.',
                'last_name' => 'Soriano',
                'department_id' => $fundraisingDepartmentId,
                'position_id' => $donorManagerPositionId,
                'contact_number' => '9170000005',
                'dial_code' => '+63',
                'address' => '9 Giving Road, Makati City, Metro Manila, 1200, Philippines',
                'role' => 'donor_manager',
            ],
            [
                'email' => 'program_staff_coach@example.com',
                'login_id' => 'STAFF-2026-6',
                'first_name' => 'Paolo',
                'middle_name' => 'J.',
                'last_name' => 'Ramos',
                'department_id' => $sportsDepartmentId,
                'position_id' => $coachPositionId,
                'contact_number' => '9170000006',
                'dial_code' => '+63',
                'address' => '77 Training Court, Pasig City, Metro Manila, 1605, Philippines',
                'role' => 'program_staff',
            ],
            [
                'email' => 'program_staff_social_worker@example.com',
                'login_id' => 'STAFF-2026-7',
                'first_name' => 'Beatriz',
                'middle_name' => 'L.',
                'last_name' => 'Mendoza',
                'department_id' => $educationDepartmentId,
                'position_id' => $socialWorkerPositionId,
                'contact_number' => '9170000007',
                'dial_code' => '+63',
                'address' => '101 Care Avenue, Quezon City, Metro Manila, 1104, Philippines',
                'role' => 'program_staff',
            ],
            [
                'email' => 'program_staff_tutor@example.com',
                'login_id' => 'STAFF-2026-8',
                'first_name' => 'Rene',
                'middle_name' => 'T.',
                'last_name' => 'Flores',
                'department_id' => $educationDepartmentId,
                'position_id' => $tutorPositionId,
                'contact_number' => '9170000008',
                'dial_code' => '+63',
                'address' => '14 Learning Way, Manila, Metro Manila, 1000, Philippines',
                'role' => 'program_staff',
            ],
            [
                'email' => 'program_staff_admin@example.com',
                'login_id' => 'STAFF-2026-9',
                'first_name' => 'Irene',
                'middle_name' => 'K.',
                'last_name' => 'Lopez',
                'department_id' => $adminDepartmentId,
                'position_id' => $adminPositionId,
                'contact_number' => '9170000009',
                'dial_code' => '+63',
                'address' => '5 Finance Park, Quezon City, Metro Manila, 1105, Philippines',
                'role' => 'admin_finance_staff',
            ],
        ];

        $staffByEmail = [];

        foreach ($staffUsers as $staffUserData) {
            $addressParts = $this->addressService->parseAddress($staffUserData['address']);

            $user = User::updateOrCreate(
                ['email' => $staffUserData['email']],
                [
                    'login_id' => $staffUserData['login_id'],
                    'password' => 'Password123!',
                    'is_active' => true,
                    'user_type' => 'staff',
                    'email_verified_at' => now(),
                    'password_changed_at' => now(),
                ]
            );

            $staffByEmail[$staffUserData['email']] = Staff::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $staffUserData['first_name'],
                    'middle_name' => $staffUserData['middle_name'],
                    'last_name' => $staffUserData['last_name'],
                    'department_id' => $staffUserData['department_id'],
                    'position_id' => $staffUserData['position_id'],
                    'contact_number' => $staffUserData['contact_number'],
                    'dial_code' => $staffUserData['dial_code'] ?? '+63',
                    'role' => $staffUserData['role'],
                ]
            );

            $staffByEmail[$staffUserData['email']]->address()->updateOrCreate([], $addressParts);
        }

        // $adminStaff = $staffByEmail['admin@example.com'];

        // Run automated and activity seeders if relevant data is missing
        if (\App\Models\Beneficiary::count() < 150) {
            $this->call(BeneficiariesSeeder::class);
        }

        if (\App\Models\FfaAssessmentRecord::count() === 0) {
            $this->call(EducationSeeder::class);
        }

        if (\App\Models\Activity::count() === 0) {
            $this->call(ActivitiesSeeder::class);
        }

        if (\App\Models\ActivityParticipant::count() === 0) {
            $this->call(ActivityParticipantsSeeder::class);
        }

        if (\App\Models\ActivitySession::count() === 0) {
            $this->call(ActivitySessionsSeeder::class);
        }

        if (\App\Models\Attendance::count() === 0) {
            $this->call(AttendanceSeeder::class);
        }

        if (\App\Models\Event::count() === 0) {
            $this->call(EventsSeeder::class);
        }

        $this->call(CompetitionsSeeder::class);
        $this->call(CompetitionResultsSeeder::class);

        $programIds = [
            'Sports' => Program::where('program_name', 'Sports')->value('id'),
            'Education' => Program::where('program_name', 'Education')->value('id'),
        ];

        $this->call(InjuriesSeeder::class);
        $this->call(DonorsSeeder::class);
        $this->call(FundingSeeder::class);

        $educationStaff = $staffByEmail['program_staff_social_worker@example.com'];
        $educationBeneficiaryIds = Beneficiary::whereHas('programs', function ($query): void {
            $query->where('program_name', 'Education');
        })->pluck('id')->all();

        HomeVisit::where('visit_type', 'routine')
            ->where('purpose', 'initial home visit')
            ->delete();

        $homeVisitRows = [];
        $timestamp = now();

        foreach ($educationBeneficiaryIds as $index => $beneficiaryId) {
            $intakeSheet = EducationIntakeSheet::where('beneficiary_id', $beneficiaryId)->first(['created_at']);
            $scheduleDate = $intakeSheet
                ? Carbon::parse($intakeSheet->created_at)->addDay()
                : $timestamp->copy()->addDays($index + 1);

            $homeVisitRows[] = [
                'beneficiary_id' => $beneficiaryId,
                'visit_type' => 'routine',
                'purpose' => 'initial home visit',
                'notes' => 'Small rented home with irregular income. Family depends on daily wage work and has limited access to stable resources.',
                'schedule' => $scheduleDate->setTime(9, 0),
                'assigned_staff_id' => $educationStaff->id,
                'created_by' => SeederSupport::CREATOR_STAFF_ID,
                'updated_by' => SeederSupport::CREATOR_STAFF_ID,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        foreach (array_chunk($homeVisitRows, 500) as $chunk) {
            DB::table('home_visits')->insert($chunk);
        }

        $monitoringConfigs = [
            ['name' => 'session_attendance_rate', 'threshold' => 75],
            ['name' => 'grade_increase', 'threshold' => 2],
            ['name' => 'school_attendance_rate', 'threshold' => 85],
        ];

        foreach ($monitoringConfigs as $config) {
            MonitoringConfiguration::updateOrCreate(
                ['name' => $config['name']],
                [
                    'threshold' => $config['threshold'],
                    'is_active' => true,
                    'created_by' => SeederSupport::CREATOR_STAFF_ID,
                    'updated_by' => SeederSupport::CREATOR_STAFF_ID,
                ]
            );
        }
    }
}
