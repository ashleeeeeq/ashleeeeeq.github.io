<?php
namespace App\Services;

use App\Models\MonitoringConfiguration;
use App\Models\Beneficiary;
use App\Models\Staff;
use App\Models\User;
use App\Models\AcademicRecord;
use App\Models\Attendance;
use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Notifications\BeneficiaryAttendanceAlert;
use App\Notifications\BeneficiaryGwaAlert;
use App\Notifications\BeneficiarySchoolAttendanceAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringService
{
    public function getThreshold(string $name)
    {
        $config = MonitoringConfiguration::where('name', $name)->where('is_active', true)->first();
        return $config ? $config->threshold : null;
    }

    public function getNotifiableStaffForBeneficiary(Beneficiary $beneficiary)
    {
        $programs = $beneficiary->activePrograms->isNotEmpty() ? $beneficiary->activePrograms : $beneficiary->programs;

        $staffUsers = collect();

        foreach ($programs as $program) {
            $staff = Staff::whereHas('department', function ($q) use ($program) {
                $q->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($program->program_name))]);
            })->get();

            foreach ($staff as $s) {
                if ($s->user) {
                    $staffUsers->push($s->user);
                }
            }
        }

        $adminAndEd = User::getAdminAndEdUsers();

        return $staffUsers->merge($adminAndEd)->unique('id')->values();
    }

    public function evaluateAttendanceForBeneficiary(Beneficiary $beneficiary): void
    {
        $threshold = $this->getThreshold('attendance_rate');
        if (is_null($threshold)) {
            return;
        }

        $programs = $beneficiary->activePrograms->isNotEmpty() ? $beneficiary->activePrograms : $beneficiary->programs;

        foreach ($programs as $program) {
            $total = 0;
            $present = 0;
            $activityNames = [];

            $activities = Activity::where('program_id', $program->id)->get();

            foreach ($activities as $activity) {
                $participant = ActivityParticipant::where('activity_id', $activity->id)
                    ->where('beneficiary_id', $beneficiary->id)
                    ->whereNull('exited_at')
                    ->first();

                if (!$participant) {
                    continue;
                }

                $joinedAt = $participant->joined_at;

                $q = Attendance::query()
                    ->where('beneficiary_id', $beneficiary->id)
                    ->whereHas('activitySession', function ($q2) use ($activity, $joinedAt) {
                        $q2->where('activity_id', $activity->id);
                        if ($joinedAt) {
                            $q2->where('schedule', '>=', $joinedAt);
                        }
                    });

                $activityTotal = $q->count();
                if ($activityTotal === 0) {
                    continue;
                }

                $activityNames[] = $activity->name;

                $activityPresent = (clone $q)->where('attendance_status', 'present')->count();

                $total += $activityTotal;
                $present += $activityPresent;
            }

            if ($total === 0) {
                continue;
            }

            $rate = ($present / $total) * 100;

            if ($rate < (float) $threshold) {
                if ($this->hasRecentNotification(BeneficiaryAttendanceAlert::class, $beneficiary->id)) {
                    continue;
                }

                $activityList = count($activityNames) ? implode(', ', array_unique($activityNames)) : 'activity';
                $actionUrl = url('/beneficiaries/' . $beneficiary->id . '/profile');
                $notifiables = $this->getNotifiableStaffForBeneficiary($beneficiary);
                foreach ($notifiables as $user) {
                    try {
                        $user->notify(new BeneficiaryAttendanceAlert([
                            'beneficiary_id' => $beneficiary->id,
                            'activity_id' => $program->id,
                            'message' => sprintf("%s's attendance (%.1f%%) is below threshold (%.1f%%) for %s under %s", $beneficiary->getDisplayNameAttribute() ?? $beneficiary->id, $rate, $threshold, $activityList, $program->program_name),
                            'action_url' => $actionUrl,
                            'meta' => ['attendance_rate' => $rate, 'threshold' => $threshold, 'program_id' => $program->id],
                        ]));
                    } catch (\Throwable $e) {
                        Log::error('Failed to notify user for beneficiary attendance', ['error' => $e->getMessage()]);
                    }
                }
            }
        }
    }

    public function evaluateGwaForBeneficiary(Beneficiary $beneficiary): void
    {
        $threshold = $this->getThreshold('grade_increase');
        if (is_null($threshold)) {
            return;
        }

        $enrollments = $beneficiary->educationEnrollments()
            ->with('academicRecords')
            ->orderByDesc('academic_year_end_date')
            ->take(2)
            ->get();

        if ($enrollments->count() < 2) {
            return;
        }

        $latestEnrollment = $enrollments->get(0);
        $previousEnrollment = $enrollments->get(1);

        $latestAvg = $latestEnrollment->academicRecords->avg('gwa');
        $prevAvg = $previousEnrollment->academicRecords->avg('gwa');

        if (is_null($latestAvg) || is_null($prevAvg)) {
            return;
        }

        $delta = (float) $latestAvg - (float) $prevAvg;
        if ($delta < (float) $threshold) {
            if ($this->hasRecentNotification(BeneficiaryGwaAlert::class, $beneficiary->id)) {
                return;
            }

            $actionUrl = url('/beneficiaries/' . $beneficiary->id . '/academic-records');
            $notifiables = $this->getNotifiableStaffForBeneficiary($beneficiary);
            foreach ($notifiables as $user) {
                try {
                    $user->notify(new BeneficiaryGwaAlert([
                        'beneficiary_id' => $beneficiary->id,
                        'message' => sprintf("%s's average GWA (%.1f%%) for their recent enrollment did not meet required increase of %d", $beneficiary->getDisplayNameAttribute() ?? $beneficiary->id, $latestAvg, $threshold),
                        'action_url' => $actionUrl,
                        'meta' => ['latestAvg' => $latestAvg, 'threshold' => $threshold, 'latest_avg_gwa' => $latestAvg, 'previous_avg_gwa' => $prevAvg],
                    ]));
                } catch (\Throwable $e) {
                    Log::error('Failed to notify user for beneficiary GWA', ['error' => $e->getMessage()]);
                }
            }
        }
    }

    public function evaluateSchoolAttendanceForBeneficiary(Beneficiary $beneficiary, ?AcademicRecord $record = null): void
    {
        $threshold = $this->getThreshold('school_attendance_rate');
        if (is_null($threshold)) {
            return;
        }

        $record ??= $beneficiary->academicRecords()->latest('created_at')->first();
        if (!$record || (float) $record->school_attendance >= (float) $threshold) {
            return;
        }

        if ($this->hasRecentNotification(BeneficiarySchoolAttendanceAlert::class, $beneficiary->id)) {
            return;
        }

        $actionUrl = url('/beneficiaries/' . $beneficiary->id . '/academic-records');
        $notifiables = $this->getNotifiableStaffForBeneficiary($beneficiary);
        foreach ($notifiables as $user) {
            try {
                $user->notify(new BeneficiarySchoolAttendanceAlert([
                    'beneficiary_id' => $beneficiary->id,
                    'academic_record_id' => $record->id,
                    'message' => sprintf(
                        "%s's school attendance (%.1f%%) is below the required rate (%.1f%%)",
                        $beneficiary->getDisplayNameAttribute() ?? $beneficiary->id,
                        $record->school_attendance,
                        $threshold
                    ),
                    'action_url' => $actionUrl,
                    'meta' => [
                        'school_attendance' => $record->school_attendance,
                        'threshold' => $threshold,
                    ],
                ]));
            } catch (\Throwable $e) {
                Log::error('Failed to notify user for beneficiary school attendance', ['error' => $e->getMessage()]);
            }
        }
    }

    protected function hasRecentNotification(string $notificationClass, int $beneficiaryId, int $hours = 1): bool
    {
        return DB::table('notifications')
            ->where('type', $notificationClass)
            ->where('data->beneficiary_id', $beneficiaryId)
            ->where('created_at', '>=', now()->subHours($hours))
            ->exists();
    }
}
