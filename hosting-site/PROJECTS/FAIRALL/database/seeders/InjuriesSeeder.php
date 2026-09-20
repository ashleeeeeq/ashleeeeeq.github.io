<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InjuriesSeeder extends Seeder
{
    public function run(): void
    {
        $adminStaffId = $this->adminStaffId();

        $sportsBeneficiaryIds = Beneficiary::query()
            ->whereHas('programs', function ($query): void {
                $query->where('program_name', 'Sports');
            })
            ->pluck('beneficiaries.id')
            ->all();

        if ($sportsBeneficiaryIds === []) {
            return;
        }

        DB::table('injury_records')->whereIn('beneficiary_id', $sportsBeneficiaryIds)->delete();

        $templates = [
            ['injury_type' => 'Ankle sprain', 'severity' => 'minor', 'body_part' => 'left ankle', 'status' => 'recovered', 'remarks' => 'Recovered after rest and mobility work'],
            ['injury_type' => 'Hamstring strain', 'severity' => 'moderate', 'body_part' => 'right hamstring', 'status' => 'recovering', 'remarks' => 'In rehab with gradual return-to-play plan'],
            ['injury_type' => 'Wrist contusion', 'severity' => 'minor', 'body_part' => 'left wrist', 'status' => 'recovered', 'remarks' => 'Treated with ice and bracing'],
            ['injury_type' => 'Knee overuse pain', 'severity' => 'moderate', 'body_part' => 'both knees', 'status' => 'chronic', 'remarks' => 'Managed with training load adjustments'],
            ['injury_type' => 'Shoulder strain', 'severity' => 'serious', 'body_part' => 'right shoulder', 'status' => 'recovering', 'remarks' => 'Physiotherapy and strength program ongoing'],
        ];

        $rows = [];
        foreach ($sportsBeneficiaryIds as $index => $beneficiaryId) {
            $injuryCount = ($beneficiaryId % 3) === 0 ? 2 : 1;

            for ($slot = 0; $slot < $injuryCount; $slot++) {
                $template = $templates[($beneficiaryId + $slot) % count($templates)];
                $startDate = Carbon::now()->subMonths(18 - (($beneficiaryId + $slot) % 10))->subDays(($index + $slot) % 21);
                $endDate = $template['status'] === 'recovering'
                    ? null
                    : $startDate->copy()->addWeeks(2 + (($beneficiaryId + $slot) % 4))->toDateString();

                $rows[] = [
                    'beneficiary_id' => $beneficiaryId,
                    'injury_type' => $template['injury_type'],
                    'severity' => $template['severity'],
                    'body_part' => $template['body_part'],
                    'status' => $template['status'],
                    'remarks' => $template['remarks'],
                    'recovery_start_date' => $startDate->toDateString(),
                    'recovery_end_date' => $endDate,
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('injury_records')->insert($chunk);
        }
    }

    private function adminStaffId(): int
    {
        return SeederSupport::CREATOR_STAFF_ID;
    }
}