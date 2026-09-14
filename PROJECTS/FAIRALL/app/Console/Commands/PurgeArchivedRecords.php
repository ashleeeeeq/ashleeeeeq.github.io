<?php

namespace App\Console\Commands;

use App\Models\ActivityType;
use App\Models\AssessmentCategory;
use App\Models\Beneficiary;
use App\Models\BeneficiaryStatusType;
use App\Models\Donor;
use App\Models\EventType;
use App\Models\SportType;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Console\Command;

class PurgeArchivedRecords extends Command
{
    protected $signature = 'archive:purge';
    protected $description = 'Permanently delete records soft-deleted over 90 days ago';

    public function handle(): int
    {
        $cutoff = now()->subDays(90);

        $models = [
            User::class,
            Beneficiary::class,
            Staff::class,
            Donor::class,
            BeneficiaryStatusType::class,
            AssessmentCategory::class,
            SportType::class,
            ActivityType::class,
            EventType::class,
        ];

        $totalPurged = 0;

        foreach ($models as $modelClass) {
            $count = $modelClass::onlyTrashed()
                ->where('deleted_at', '<', $cutoff)
                ->forceDelete();

            $totalPurged += $count;

            if ($count > 0) {
                $this->info("Purged {$count} records from {$modelClass}");
            }
        }

        $this->info("Total purged: {$totalPurged} records.");

        return Command::SUCCESS;
    }
}
