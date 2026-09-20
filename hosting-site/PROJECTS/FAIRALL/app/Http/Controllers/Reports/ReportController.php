<?php

namespace App\Http\Controllers\Reports;

use App\Enums\ReportPeriod;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Http\Middleware\ReportTypeAccess;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Models\Staff;
use App\Models\User;
use App\Services\Llm\GroqClient;
use App\Services\Reports\ReportGenerationService;
use App\Services\Reports\ReportRateLimiter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $allowedTypes = ReportTypeAccess::allowedTypesForStaff($user->staff);

        $reports = Report::with('user')
            ->whereIn('type', $allowedTypes)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reports.index', [
            'reports' => $reports,
            'allowedTypes' => $allowedTypes,
        ]);
    }

    public function generate(Request $request, ReportGenerationService $generationService): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', ReportType::values())],
            'period_type' => ['required', 'string', 'in:' . implode(',', ReportPeriod::values())],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'quarter' => ['nullable', 'integer', 'in:1,2,3,4'],
            'from_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'to_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $user = Auth::user();
        $type = ReportType::from($validated['type']);
        $periodType = ReportPeriod::from($validated['period_type']);

        $allowedTypes = ReportTypeAccess::allowedTypesForStaff($user->staff);
        if (!in_array($type->value, $allowedTypes, true)) {
            return response()->json(['error' => 'Not authorized for this report type.'], 403);
        }

        if ($periodType === ReportPeriod::MultiYear) {
            $fromYear = (int) ($validated['from_year'] ?? 0);
            $toYear   = (int) ($validated['to_year'] ?? 0);
            if ($fromYear >= $toYear) {
                return response()->json([
                    'error' => 'The "From Year" must be earlier than the "To Year".',
                ], 422);
            }
        }

        // Sliding 24h limit: 5 successful generations per user. Only counts rows actually created.
        if ($limitPayload = ReportRateLimiter::limitResponse($user)) {
            return response()->json($limitPayload, 429)
                ->header('Retry-After', (string) $limitPayload['retry_after'])
                ->header('X-RateLimit-Limit', (string) ReportRateLimiter::LIMIT)
                ->header('X-RateLimit-Remaining', '0');
        }

        $periodConfig = $this->buildPeriodConfig($periodType, $validated);
        $dates = $this->resolveDates($periodConfig);

        $report = Report::create([
            'user_id' => $user->id,
            'type' => $type->value,
            'period_type' => $periodType->value,
            'period_config' => $periodConfig,
            'date_from' => $dates['start'],
            'date_to' => $dates['end'],
            'program_id' => $this->resolveProgramId($type, $user),
            'status' => ReportStatus::Pending->value,
        ]);

        $bootPayload = $generationService->boot($type, $periodConfig, $user);

        $report->update(['boot_payload' => $bootPayload]);

        GenerateReportJob::dispatch($report->id);

        return response()->json([
            'id' => $report->id,
            'status' => $report->status,
            'boot_payload' => $bootPayload,
        ]);
    }

    public function status(int $id): JsonResponse
    {
        $report = Report::findOrFail($id);

        if ($report->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not authorized.'], 403);
        }

        return response()->json([
            'id' => $report->id,
            'status' => $report->status,
            'generation_phase' => $report->generation_phase,
            'narrative_cache' => $report->narrative_cache,
            'failed_sections' => $this->failedSections($report),
            'has_pdf' => $report->pdf_path !== null,
        ]);
    }

    public function retryFailed(Request $request, int $id): JsonResponse
    {
        $report = Report::findOrFail($id);

        if ($report->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not authorized.'], 403);
        }

        if (!$report->isCompleted()) {
            return response()->json(['error' => 'Report is not completed.'], 409);
        }

        $failedSections = $this->failedSections($report);

        if (empty($failedSections)) {
            return response()->json(['error' => 'No failed sections to retry.'], 409);
        }

        $report->update([
            'status' => ReportStatus::Pending->value,
            'generation_phase' => null,
            'error_message' => null,
        ]);

        GenerateReportJob::dispatch($report->id);

        return response()->json([
            'id' => $report->id,
            'status' => $report->status,
            'retrying_sections' => $failedSections,
        ]);
    }

    private function failedSections(Report $report): array
    {
        $narratives = $report->narrative_cache;

        if (!is_array($narratives)) {
            return [];
        }

        return array_values(array_filter(
            array_keys($narratives),
            fn (string|int $key) => ($narratives[$key] ?? null) === GroqClient::UNAVAILABLE_PLACEHOLDER,
        ));
    }

    public function downloadPdf(int $id): StreamedResponse
    {
        $report = Report::findOrFail($id);

        $allowedTypes = ReportTypeAccess::allowedTypesForStaff(Auth::user()->staff);
        if (!in_array($report->type, $allowedTypes, true)) {
            abort(403);
        }

        if (!$report->pdf_path || !Storage::disk('s3')->exists($report->pdf_path)) {
            abort(404, 'PDF not found.');
        }

        return Storage::disk('s3')->download($report->pdf_path, 'report-' . $report->id . '.pdf');
    }

    public function previewPdf(int $id): StreamedResponse
    {
        $report = Report::findOrFail($id);

        $allowedTypes = ReportTypeAccess::allowedTypesForStaff(Auth::user()->staff);
        if (!in_array($report->type, $allowedTypes, true)) {
            abort(403);
        }

        if (!$report->pdf_path || !Storage::disk('s3')->exists($report->pdf_path)) {
            abort(404, 'PDF not found.');
        }

        $content = Storage::disk('s3')->get($report->pdf_path);

        return response()->stream(function () use ($content) {
            echo $content;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="report-' . $report->id . '.pdf"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }

    private function buildPeriodConfig(ReportPeriod $periodType, array $validated): array
    {
        return match ($periodType) {
            ReportPeriod::Quarterly => [
                'period_type' => 'quarterly',
                'year' => (int) ($validated['year'] ?? date('Y')),
                'quarter' => (int) ($validated['quarter'] ?? 1),
            ],
            ReportPeriod::Annual => [
                'period_type' => 'annual',
                'year' => (int) ($validated['year'] ?? date('Y')),
            ],
            ReportPeriod::MultiYear => [
                'period_type' => 'multi_year',
                'from_year' => (int) ($validated['from_year'] ?? date('Y') - 1),
                'to_year' => (int) ($validated['to_year'] ?? date('Y')),
            ],
        };
    }

    private function resolveDates(array $periodConfig): array
    {
        return match ($periodConfig['period_type']) {
            'quarterly' => $this->quarterlyDates($periodConfig['year'], $periodConfig['quarter']),
            'annual' => [
                'start' => Carbon::create($periodConfig['year'], 1, 1)->startOfDay()->toDateString(),
                'end' => Carbon::create($periodConfig['year'], 12, 31)->endOfDay()->toDateString(),
            ],
            'multi_year' => [
                'start' => Carbon::create($periodConfig['from_year'], 1, 1)->startOfDay()->toDateString(),
                'end' => Carbon::create($periodConfig['to_year'], 12, 31)->endOfDay()->toDateString(),
            ],
        };
    }

    private function quarterlyDates(int $year, int $quarter): array
    {
        $month = ($quarter - 1) * 3 + 1;

        return [
            'start' => Carbon::create($year, $month, 1)->startOfDay()->toDateString(),
            'end' => Carbon::create($year, $month + 2, 1)->endOfMonth()->endOfDay()->toDateString(),
        ];
    }

    public function destroy(Report $report): RedirectResponse
    {
        if (!ReportRateLimiter::isExempt(Auth::user())) {
            abort(403, 'Only admin can delete reports.');
        }

        $report->delete();

        return back()->with('status', 'Report moved to archive. Will be automatically deleted after 90 days.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        if (!ReportRateLimiter::isExempt(Auth::user())) {
            abort(403, 'Only admin can delete reports.');
        }

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:reports,id'],
        ]);

        $ids = $validated['ids'];

        // Ensure all requested reports are within allowed types for the user (same check as index)
        $allowedTypes = ReportTypeAccess::allowedTypesForStaff(Auth::user()->staff);
        $reports = Report::whereIn('id', $ids)->get();
        foreach ($reports as $report) {
            if (!in_array($report->type, $allowedTypes, true)) {
                abort(403, 'Not authorized for this report type.');
            }
        }

        DB::transaction(function () use ($ids): void {
            $reports = Report::whereIn('id', $ids)->get();
            foreach ($reports as $report) {
                $report->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' reports moved to archive. Will be automatically deleted after 90 days.');
    }

    public function templatePreview(): View
    {
        $payload = [
            'education' => [
                'scholars' => 125,
                'graduatesTotal' => 18,
                'scholarsExited' => 7,
                'averageGradesByStage' => ['elementary' => '87.3', 'high_school' => '82.6', 'shs' => '84.1', 'college' => '79.8'],
                'transitionRatesByAcademicYear' => [
                    'labels' => ['2022-23', '2023-24', '2024-25', '2025-26'],
                    'datasets' => [
                        ['label' => 'Elem→HS', 'data' => [88, 91, 93, 90], 'fill' => false],
                        ['label' => 'HS→SHS', 'data' => [82, 85, 87, 84]],
                        ['label' => 'SHS→College', 'data' => [74, 78, 80, 82]],
                    ],
                ],
                'completionRatesByAcademicYear' => [
                    'labels' => ['2022-23', '2023-24', '2024-25', '2025-26'],
                    'datasets' => [
                        ['label' => 'Completion Rate', 'data' => [72, 76, 81, 79], 'fill' => false],
                    ],
                ],
                'graduatesByStageByYear' => [
                    'labels' => ['2022', '2023', '2024', '2025'],
                    'datasets' => [
                        ['label' => 'Elementary', 'data' => [12, 14, 16, 15], 'fill' => false],
                        ['label' => 'High School', 'data' => [18, 20, 22, 19]],
                        ['label' => 'SHS', 'data' => [8, 10, 12, 14]],
                        ['label' => 'College', 'data' => [4, 5, 8, 6]],
                    ],
                ],
                'scholarsByEducationalStageByYear' => [
                    'labels' => ['2022', '2023', '2024', '2025'],
                    'datasets' => [
                        ['label' => 'Elementary', 'data' => [45, 42, 38, 35], 'fill' => false],
                        ['label' => 'High School', 'data' => [38, 40, 42, 45]],
                        ['label' => 'SHS', 'data' => [20, 22, 24, 28]],
                        ['label' => 'College', 'data' => [15, 17, 19, 22]],
                    ],
                ],
            ],
            'educationActivity' => [
                'eqSessionAttendance' => 87,
                'tutorialSessionAttendance' => 74,
                'trends' => [
                    'eqSessionAttendance' => [
                        'labels' => ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        'data' => [82, 85, 79, 88, 91, 87],
                    ],
                    'tutorialSessionAttendance' => [
                        'labels' => ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        'data' => [68, 72, 70, 76, 78, 74],
                    ],
                ],
            ],
            'sports' => [
                'registeredPlayers' => 87,
                'competitionParticipation' => 5,
                'newPlayersBySportsTypeByYear' => [
                    'labels' => ['2022', '2023', '2024', '2025'],
                    'datasets' => [
                        ['label' => 'Basketball', 'data' => [8, 10, 12, 9], 'fill' => true],
                        ['label' => 'Volleyball', 'data' => [6, 8, 10, 7]],
                        ['label' => 'Football', 'data' => [4, 5, 6, 8]],
                        ['label' => 'Swimming', 'data' => [2, 3, 4, 5]],
                    ],
                ],
                'activePlayersBySportsTypeByYear' => [
                    'labels' => ['2022', '2023', '2024', '2025'],
                    'datasets' => [
                        ['label' => 'Basketball', 'data' => [22, 24, 25, 22], 'fill' => true],
                        ['label' => 'Volleyball', 'data' => [18, 19, 20, 18]],
                        ['label' => 'Football', 'data' => [12, 13, 14, 15]],
                        ['label' => 'Swimming', 'data' => [8, 9, 10, 11]],
                    ],
                ],
            ],
            'sportsActivity' => [
                'trainingSessionAttendance' => 81,
                'uniqueVisits' => 312,
                'trends' => [
                    'trainingSessionAttendance' => [
                        'labels' => ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        'data' => [76, 80, 78, 84, 87, 81],
                    ],
                    'uniqueVisits' => [
                        'labels' => ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        'data' => [45, 52, 48, 56, 62, 49],
                    ],
                ],
            ],
            'funding' => [
                'newDonors' => 12,
                'fundsReceived' => 185000.00,
                'fundsAllocated' => 142000.00,
                'fundingTarget' => 250000.00,
                'receivedProgressPercent' => 74,
                'allocatedVsReceivedPercent' => 76.8,
            ],
        ];

        $report = new \App\Models\Report();
        $report->type = 'multi_section';
        $report->period_type = 'annual';
        $report->date_from = \Carbon\Carbon::parse('2025-01-01');
        $report->date_to = \Carbon\Carbon::parse('2025-12-31');

        $narratives = [
            'executive_summary' => 'FAIRALL Foundation demonstrated continued growth across both Education and Sports programs this reporting period. Education maintained strong scholar retention with 125 active scholars, while Sports expanded participation with 87 registered players. Funding reached 74% of target, reflecting sustained donor confidence.',
            'education' => 'The Education program served 125 scholars across elementary through college levels. Graduation trends remain positive with 18 graduates this period. Academic performance remains strong, with targeted support interventions in place for growth areas.',
            'academic_performance' => 'Academic performance remained strong across all educational stages. Elementary scholars led with an average grade of 87.3%, followed by SHS at 84.1%. The college cohort showed room for improvement at 79.8%, prompting enhanced tutorial support interventions.',
            'transition_rates' => 'Transition rates showed steady improvement across all levels. The Elementary to High School transition remained the strongest at 90%. SHS to College transitions improved to 82%, reflecting the effectiveness of college readiness initiatives.',
            'completion_rates' => 'Completion rates improved to 79% this period, up from 72% in 2022-23. This demonstrates the positive impact of enhanced academic support and monitoring systems implemented over the past two years.',
            'graduates_by_stage' => 'Graduate distribution remained diverse across stages, with High School producing the largest cohort at 19 graduates. College graduates increased to 6, reflecting successful retention through to tertiary completion.',
            'scholars_by_stage' => 'Scholar distribution is shifting toward higher educational levels, with College enrollment growing from 15 to 22 scholars over four years. This trend indicates improving retention and academic progression.',
            'education_participation' => 'Program participation remained robust with EQ session attendance at 87% and tutorial attendance at 74%. The gap between the two suggests opportunities for increased tutorial engagement through scheduling adjustments.',
            'sports' => 'The Sports program expanded its reach with 87 registered players across four sports disciplines. Competition participation increased with 5 events this period, providing athletes with valuable competitive experience.',
            'new_players' => 'New player recruitment showed consistent growth across all sports types, with Basketball leading at 9 new players. The Football program saw the strongest growth trajectory, doubling from 4 to 8 new players over four years.',
            'active_players' => 'Active player participation remained stable with Basketball maintaining the largest contingent at 22 players. The Swimming program showed steady growth, increasing from 8 to 11 active participants.',
            'sports_participation_cards' => 'Training session attendance averaged 81%, while program facilities recorded 312 unique visits. The consistent engagement reflects strong program interest and effective scheduling.',
            'sports_participation_trends' => 'Monthly training attendance showed gradual improvement from 76% in July to 81% by December, with a peak of 87% in November. Unique visits followed a similar upward trend, rising from 45 in July to 62 in November before settling at 49 in December.',
            'funding' => 'Funding performance reached 74% of the annual target with ₱185,000 received. Twelve new donors contributed this period, expanding the foundation\'s support base.',
            'funding_target_allocation_analysis' => 'Of the ₱185,000 received, ₱142,000 (76.8%) has been allocated to programs. The remaining balance provides operational flexibility for emerging needs and program expansion in the coming quarters.',
            'funding_trend_analysis' => 'The comparison of funds received versus allocated across months shows a consistent allocation rate, with the Foundation maintaining prudent financial management throughout the reporting period.',
        ];

        return view('reports.pdf', [
            'report' => $report,
            'payload' => $payload,
            'narratives' => $narratives,
        ]);
    }

    private function resolveProgramId(ReportType $type, \App\Models\User $user): ?int
    {
        if ($type === ReportType::OrganizationalOverview || $type === ReportType::Funding) {
            return null;
        }

        $staff = $user->staff;

        if (!$staff) {
            return null;
        }

        if ($staff->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR])) {
            return null;
        }

        return $staff->staffProgram()?->id;
    }
}
