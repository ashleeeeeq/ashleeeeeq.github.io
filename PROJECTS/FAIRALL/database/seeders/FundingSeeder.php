<?php

namespace Database\Seeders;

use App\Models\Allocation;
use App\Models\Beneficiary;
use App\Models\Grant;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FundingSeeder extends Seeder
{
    public function run(): void
    {
        $adminStaffId = SeederSupport::CREATOR_STAFF_ID;
        $programIds = Program::query()->pluck('id', 'program_name')->all();
        $sportsProgramId = $programIds['Sports'];
        $educationProgramId = $programIds['Education'];
        $currentYear = now()->year;
        $timestamp = now();

        // Clean slate
        DB::table('allocations')->delete();
        DB::table('deliverables')->delete();
        DB::table('grant_program')->delete();
        DB::table('grants')->delete();
        DB::table('funding_targets')->delete();

        $grantIds = [];
        $allocationRows = [];

        for ($year = 2020; $year <= $currentYear; $year++) {
            foreach ([
                ['program_id' => $sportsProgramId, 'program_name' => 'Sports'],
                ['program_id' => $educationProgramId, 'program_name' => 'Education'],
            ] as $program) {
                if (! $program['program_id']) {
                    continue;
                }

                $amount = fake()->numberBetween(150000, 200000);
                $startDate = Carbon::create($year, 1, 1);
                $endDate = $year < now()->year
                    ? Carbon::create($year, 12, 31)
                    : Carbon::now();

                $grant = Grant::create([
                    'grant_name' => $program['program_name'] . ' Program Grant ' . $year,
                    'organization_name' => 'Seed Grantor ' . $program['program_name'],
                    'email' => strtolower($program['program_name']) . '.grants@example.com',
                    'contact_number' => '91833300' . str_pad((string) ($year - 2020 + 1), 2, '0', STR_PAD_LEFT),
                    'dial_code' => '+63',
                    'description' => 'Seeded grant for ' . $program['program_name'] . ' program - ' . $year,
                    'total_amount' => $amount,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                DB::table('grant_program')->insert([
                    'grant_id' => $grant->id,
                    'program_id' => $program['program_id'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $grantIds[] = $grant->id;

                // Deliverables
                $deliverableRows = [];
                for ($i = 1; $i <= 15; $i++) {
                    $base = Carbon::create($year, 6, 1);
                    $start = $base->copy()->addDays($i * 5);

                    if ($start->gt(now())) {
                        continue;
                    }

                    $end = $base->copy()->addDays(($i * 5) + 14);

                    $hash = md5($grant->id . '-' . $i);
                    $timelineTotal = max(1, $start->diffInDays($end));
                    $daysElapsed = $start->lte($timestamp) ? $start->diffInDays($timestamp) : 0;
                    $timelinePercent = (int) floor(($daysElapsed / $timelineTotal) * 100);
                    $timelinePercent = max(0, min(100, $timelinePercent));

                    $forceComplete = $year < $currentYear || (hexdec(substr($hash, 4, 2)) % 3) === 0;
                    $variance = hexdec(substr($hash, 6, 2)) % 30;

                    $completedAt = null;
                    $progress = 0;

                    if ($timestamp->gt($end)) {
                        if ($forceComplete) {
                            $progress = 100;
                            $daysAfterEnd = max(0, $timestamp->diffInDays($end));
                            $completedOffset = $daysAfterEnd > 0 ? (hexdec(substr($hash, 8, 2)) % ($daysAfterEnd + 1)) : 0;
                            $completedAt = $end->copy()->addDays($completedOffset)->toDateTimeString();
                        } else {
                            $progress = max(0, min(99, $timelinePercent - ($variance % 20)));
                        }
                    } else {
                        $progress = max(0, min(100, $timelinePercent - ($variance % 5)));
                    }

                    $deliverableRows[] = [
                        'donor_id' => null,
                        'grant_id' => $grant->id,
                        'title' => $program['program_name'] . ' Deliverable ' . $i . ' (' . $year . ')',
                        'description' => 'Seeded grant deliverable for ' . $year,
                        'start_date' => $start->toDateString(),
                        'end_date' => $end->toDateString(),
                        'completed_at' => $completedAt,
                        'progress' => $progress,
                        'created_by' => $adminStaffId,
                        'updated_by' => $adminStaffId,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                foreach (array_chunk($deliverableRows, 500) as $chunk) {
                    DB::table('deliverables')->insert($chunk);
                }
            }
        }

        // Allocations from donations
        $donations = DB::table('donations')
            ->where('status', 'completed')
            ->orderBy('id')
            ->get(['id', 'amount', 'transaction_date', 'program_id']);

        foreach ($donations as $donation) {
            $eligibleIds = match ($donation->program_id) {
                null => Beneficiary::query()->pluck('id')->all(),
                default => DB::table('beneficiary_program_memberships')
                    ->where('program_id', $donation->program_id)
                    ->pluck('beneficiary_id')
                    ->all(),
            };

            if ($eligibleIds === []) {
                continue;
            }

            $allocatable = (int) round(((float) $donation->amount * 100) * 0.6);
            $slice = array_slice($eligibleIds, 0, 3);
            $perBeneficiary = (int) floor($allocatable / max(1, count($slice)));

            if ($perBeneficiary <= 0) {
                continue;
            }

            foreach ($slice as $beneficiaryId) {
                $allocationRows[] = [
                    'donation_id' => $donation->id,
                    'grant_id' => null,
                    'beneficiary_id' => $beneficiaryId,
                    'amount_cents' => $perBeneficiary,
                    'date_allocated' => $donation->transaction_date ? Carbon::parse($donation->transaction_date)->toDateString() : now()->toDateString(),
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'notes' => 'Seeded donation allocation',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        // Allocations from grants
        $grants = Grant::with('programs')->orderBy('id')->get(['id', 'total_amount', 'start_date']);
        foreach ($grants as $grant) {
            $grantProgramIds = $grant->programs->pluck('id')->all();

            $eligibleIds = $grantProgramIds === []
                ? Beneficiary::query()->pluck('id')->all()
                : DB::table('beneficiary_program_memberships')
                    ->whereIn('program_id', $grantProgramIds)
                    ->pluck('beneficiary_id')
                    ->unique()
                    ->values()
                    ->all();

            if ($eligibleIds === []) {
                continue;
            }

            $allocatable = (int) round(((float) $grant->total_amount * 100) * 0.7);
            $slice = array_slice(array_reverse($eligibleIds), 0, 4);
            $perBeneficiary = (int) floor($allocatable / max(1, count($slice)));

            if ($perBeneficiary <= 0) {
                continue;
            }

            foreach ($slice as $beneficiaryId) {
                $allocationRows[] = [
                    'donation_id' => null,
                    'grant_id' => $grant->id,
                    'beneficiary_id' => $beneficiaryId,
                    'amount_cents' => $perBeneficiary,
                    'date_allocated' => $grant->start_date ?? now()->toDateString(),
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'notes' => 'Seeded grant allocation',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        foreach (array_chunk($allocationRows, 500) as $chunk) {
            DB::table('allocations')->insert($chunk);
        }

        // Funding targets per year
        for ($year = 2020; $year <= $currentYear; $year++) {
            DB::table('funding_targets')->insert([
                [
                    'program_id' => $sportsProgramId,
                    'year' => $year,
                    'target_amount_cents' => 75000000,
                    'currency' => 'PHP',
                    'notes' => 'Seeded annual funding target',
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'program_id' => $educationProgramId,
                    'year' => $year,
                    'target_amount_cents' => 75000000,
                    'currency' => 'PHP',
                    'notes' => 'Seeded annual funding target',
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'program_id' => null,
                    'year' => $year,
                    'target_amount_cents' => 75000000,
                    'currency' => 'PHP',
                    'notes' => 'Seeded annual funding target',
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
            ]);
        }
    }
}
