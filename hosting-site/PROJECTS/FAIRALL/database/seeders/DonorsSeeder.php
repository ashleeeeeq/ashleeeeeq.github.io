<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DonorsSeeder extends Seeder
{
    public function run(): void
    {
        $adminStaffId = SeederSupport::CREATOR_STAFF_ID;
        $programIds = Program::query()->pluck('id', 'program_name')->all();
        $sportsProgramId = $programIds['Sports'];
        $educationProgramId = $programIds['Education'];
        $currentYear = now()->year;
        $hashedPassword = Hash::make('Password123!');
        $timestamp = now();

        // Clean slate: remove all donor-related data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('allocations')->delete();
        DB::table('receipts')->delete();
        DB::table('donations')->delete();
        DB::table('subscriptions')->delete();
        DB::table('checkout_sessions')->delete();
        $donorUserIds = User::where('user_type', 'donor')->pluck('id');
        Donor::whereIn('user_id', $donorUserIds)->delete();
        User::whereIn('id', $donorUserIds)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $allIndividualDonorIds = [];
        $allOrgDonorIds = [];
        $donationRows = [];
        $referenceDateCounter = 0;

        for ($year = 2020; $year <= $currentYear; $year++) {
            $isFirstYear = $year === 2020;
            $individualCount = $isFirstYear ? 10 : 10;
            $orgCount = $isFirstYear ? 5 : 2;

            $currentIndividualIds = [];

            for ($i = 1; $i <= $individualCount; $i++) {
                $email = sprintf('individual_donor_%d_%d@example.com', $year, $i);
                $user = User::create([
                    'login_id' => null,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'is_active' => true,
                    'user_type' => 'donor',
                    'email_verified_at' => $timestamp,
                    'password_changed_at' => $timestamp,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $donor = Donor::create([
                    'user_id' => $user->id,
                    'organization_name' => null,
                    'first_name' => 'Individual',
                    'middle_name' => 'D',
                    'last_name' => 'Donor ' . $year . '-' . $i,
                    'contact_number' => '918111' . str_pad((string) (100 + $year - 2020 + $i), 4, '0', STR_PAD_LEFT),
                    'dial_code' => '+63',
                    'donor_type' => 'individual',
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $user->update([
                    'login_id' => "DON-{$year}-{$donor->id}",
                ]);

                $currentIndividualIds[] = $donor->id;
            }

            $allIndividualDonorIds[$year] = $currentIndividualIds;

            $currentOrgIds = [];

            for ($i = 1; $i <= $orgCount; $i++) {
                $email = sprintf('org_donor_%d_%d@example.com', $year, $i);
                $user = User::create([
                    'login_id' => null,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'is_active' => true,
                    'user_type' => 'donor',
                    'email_verified_at' => $timestamp,
                    'password_changed_at' => $timestamp,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $donor = Donor::create([
                    'user_id' => $user->id,
                    'organization_name' => 'Organization Donor ' . $year . '-' . $i,
                    'first_name' => null,
                    'middle_name' => null,
                    'last_name' => null,
                    'contact_number' => '918222' . str_pad((string) (100 + $year - 2020 + $i), 4, '0', STR_PAD_LEFT),
                    'dial_code' => '+63',
                    'donor_type' => 'organization',
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $user->update([
                    'login_id' => "DON-{$year}-{$donor->id}",
                ]);

                $currentOrgIds[] = $donor->id;
            }

            $allOrgDonorIds[$year] = $currentOrgIds;
        }

        $programChoices = [$sportsProgramId, $educationProgramId, null];

        for ($year = 2020; $year <= $currentYear; $year++) {
            $activeIndividualIds = [];
            for ($y = 2020; $y <= $year; $y++) {
                $activeIndividualIds = array_merge($activeIndividualIds, $allIndividualDonorIds[$y] ?? []);
            }

            $activeOrgIds = [];
            for ($y = 2020; $y <= $year; $y++) {
                $activeOrgIds = array_merge($activeOrgIds, $allOrgDonorIds[$y] ?? []);
            }

            foreach ($activeIndividualIds as $donorId) {
                for ($d = 1; $d <= 5; $d++) {
                    $referenceDateCounter++;
                    $amount = fake()->randomFloat(2, 5000, 10000);
                    $latestAllowed = $year < now()->year
                        ? Carbon::create($year, 12, 31, 23, 59, 59)
                        : Carbon::now();
                    $transactionDate = Carbon::createFromTimestamp(
                        rand(Carbon::create($year, 1, 1)->timestamp, $latestAllowed->timestamp)
                    );
                    $programId = $programChoices[($donorId + $d) % 3];

                    $donationRows[] = [
                        'donor_id' => $donorId,
                        'program_id' => $programId,
                        'gateway' => 'manual',
                        'gateway_reference' => 'seed-donation-' . $donorId . '-' . $year . '-' . $d,
                        'reference_number' => 'DON-' . $year . sprintf('%06d', $referenceDateCounter),
                        'donation_type' => 'financial',
                        'amount' => $amount,
                        'currency' => 'PHP',
                        'transaction_date' => $transactionDate,
                        'status' => 'completed',
                        'description' => 'Seeded individual donation ' . $year,
                        'metadata' => json_encode(['seeded' => true, 'year' => $year]),
                        'created_by' => $adminStaffId,
                        'updated_by' => $adminStaffId,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }

            foreach ($activeOrgIds as $donorId) {
                for ($d = 1; $d <= 2; $d++) {
                    $referenceDateCounter++;
                    $amount = fake()->randomFloat(2, 25000, 100000);
                    $latestAllowed = $year < now()->year
                        ? Carbon::create($year, 12, 31, 23, 59, 59)
                        : Carbon::now();
                    $transactionDate = Carbon::createFromTimestamp(
                        rand(Carbon::create($year, 1, 1)->timestamp, $latestAllowed->timestamp)
                    );
                    $programId = $programChoices[($donorId + $d + 1) % 3];

                    $donationRows[] = [
                        'donor_id' => $donorId,
                        'program_id' => $programId,
                        'gateway' => 'manual',
                        'gateway_reference' => 'seed-donation-org-' . $donorId . '-' . $year . '-' . $d,
                        'reference_number' => 'DON-' . $year . sprintf('%06d', $referenceDateCounter),
                        'donation_type' => 'financial',
                        'amount' => $amount,
                        'currency' => 'PHP',
                        'transaction_date' => $transactionDate,
                        'status' => 'completed',
                        'description' => 'Seeded organization donation ' . $year,
                        'metadata' => json_encode(['seeded' => true, 'year' => $year]),
                        'created_by' => $adminStaffId,
                        'updated_by' => $adminStaffId,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }
        }

        foreach (array_chunk($donationRows, 500) as $chunk) {
            DB::table('donations')->insert($chunk);
        }
    }
}
