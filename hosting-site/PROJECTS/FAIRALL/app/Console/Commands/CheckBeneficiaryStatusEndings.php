<?php

namespace App\Console\Commands;

use App\Models\BeneficiaryStatus;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\BeneficiaryStatusEndingNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckBeneficiaryStatusEndings extends Command
{
    protected $signature = 'app:check-beneficiary-status-endings
                            {--days=7 : Number of days ahead to check for ending statuses}';

    protected $description = 'Send notifications when beneficiary statuses are nearing their end date';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Checking beneficiary statuses ending within {$days} days...");

        $endingStatuses = BeneficiaryStatus::query()
            ->with(['beneficiary.programs', 'statusType'])
            ->whereNotNull('end_date')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->whereDate('end_date', '<=', now()->addDays($days)->toDateString())
            ->get();

        if ($endingStatuses->isEmpty()) {
            $this->info('No ending statuses found.');
            return self::SUCCESS;
        }

        $this->line("Found {$endingStatuses->count()} ending status(es).");

        foreach ($endingStatuses as $status) {
            $this->processStatus($status);
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    protected function processStatus(BeneficiaryStatus $status): void
    {
        $beneficiary = $status->beneficiary;

        if (! $beneficiary) {
            return;
        }

        if ($this->hasRecentNotification($status->id)) {
            return;
        }

        $programs = $beneficiary->activePrograms->isNotEmpty()
            ? $beneficiary->activePrograms
            : $beneficiary->programs;

        $recipients = collect();

        foreach ($programs as $program) {
            $staff = Staff::whereHas('department', function ($q) use ($program) {
                $q->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($program->program_name))]);
            })->get();

            foreach ($staff as $s) {
                if ($s->user) {
                    $recipients->push($s->user);
                }
            }
        }

        $recipients = $recipients->merge(User::getAdminAndEdUsers())->unique('id')->values();

        $daysLeft = now()->startOfDay()->diffInDays($status->end_date);
        $beneficiaryName = $beneficiary->getDisplayNameAttribute() ?? "Beneficiary #{$beneficiary->id}";
        $statusName = $status->statusType?->status_name ?? 'Status';
        $actionUrl = url('/beneficiaries/' . $beneficiary->id . '/status-history');

        foreach ($recipients as $user) {
            try {
                $user->notify(new BeneficiaryStatusEndingNotification([
                    'beneficiary_id' => $beneficiary->id,
                    'status_id' => $status->id,
                    'message' => "{$beneficiaryName}'s \"{$statusName}\" status ends in {$daysLeft} day(s) on {$status->end_date->format('M d, Y')}",
                    'action_url' => $actionUrl,
                    'meta' => [
                        'days_left' => $daysLeft,
                        'end_date' => $status->end_date->toDateString(),
                        'status_type' => $statusName,
                        'programs' => $programs->pluck('program_name')->toArray(),
                    ],
                ]));

                $this->line("  Notified user #{$user->id} about status #{$status->id} ({$beneficiaryName} - {$statusName})");
            } catch (\Throwable $e) {
                Log::error('Failed to send beneficiary status ending notification', [
                    'status_id' => $status->id,
                    'beneficiary_id' => $beneficiary->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function hasRecentNotification(int $statusId, int $hours = 24): bool
    {
        return DB::table('notifications')
            ->where('type', 'like', '%BeneficiaryStatusEndingNotification%')
            ->where('data->status_id', $statusId)
            ->where('created_at', '>=', now()->subHours($hours))
            ->exists();
    }
}
