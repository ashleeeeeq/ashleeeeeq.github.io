<?php

namespace App\Providers;

use App\Policies\DonationPolicy;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Contracts\LlmClientInterface;
use App\Contracts\VisionClientInterface;
use App\Models\ActivityParticipant;
use App\Models\Attendance;
use App\Models\HomeVisit;
use App\Models\AcademicRecord;
use App\Models\FfaAssessmentRecord;
use App\Models\InjuryRecord;
use App\Models\EducationEnrollment;
use App\Models\CompetitionResult;
use App\Models\Event as EventModel;
use App\Observers\ActivityParticipantObserver;
use App\Observers\AttendanceObserver;
use App\Observers\HomeVisitObserver;
use App\Observers\AcademicRecordObserver;
use App\Observers\FfaAssessmentRecordObserver;
use App\Observers\InjuryRecordObserver;
use App\Observers\EducationEnrollmentObserver;
use App\Observers\CompetitionResultObserver;
use App\Services\Llm\GroqClient;
use App\Services\Reports\ReportRateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LlmClientInterface::class, GroqClient::class);
        $this->app->bind(VisionClientInterface::class, \App\Services\Vision\GoogleCloudVisionClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        Validator::replacer('starts_with', function ($message, $attribute) {
            if (str_ends_with($attribute, 'contact_number_code') || str_ends_with($attribute, 'dial_code')) {
                return 'Please select a valid country code.';
            }
            return $message;
        });

        Validator::replacer('regex', function ($message, $attribute) {
            if (str_ends_with($attribute, 'contact_number')) {
                return 'Please enter a valid phone number (6-15 digits).';
            }
            return $message;
        });

        // reusable roles
        $adminOrExec = ['administrator', 'executive_director'];
        $programMgmt = ['program_manager', 'program_staff'];

        Gate::define('manage-users', fn(User $user) => $user->staff?->hasAnyRole($adminOrExec));

        Gate::define('manage-configurations', fn(User $user) => $user->staff?->hasAnyRole($adminOrExec));

        Gate::define('work-on-beneficiaries', function (User $user) {
            $staff = $user->staff;
            return $staff?->hasAnyRole(['administrator', 'executive_director', 'program_manager', 'program_staff']);
        });

        Gate::define('create-beneficiaries', function (User $user) {
            $staff = $user->staff;
            return (
                $staff?->hasAnyRole(['administrator', 'executive_director', 'program_manager'])
                || $staff?->department?->name === 'Sports'
                || $staff?->position?->name === 'Social Worker'
            );
        });

        Gate::define('manage-education-records', function (User $user) use ($adminOrExec) {
            $staff = $user->staff;
            return (
                $staff?->hasAnyRole($adminOrExec)
                || $staff?->department?->name === 'Education'
            );
        });

        Gate::define('manage-sports-records', function (User $user) use ($adminOrExec) {
            $staff = $user->staff;
            return (
                $staff?->hasAnyRole($adminOrExec)
                || $staff?->department?->name === 'Sports'
            );
        });

        Gate::define('work-on-activities', fn(User $user) => $user->staff?->hasAnyRole(array_merge($adminOrExec, $programMgmt)));

        Gate::define('create-activities', function (User $user) use ($adminOrExec) {
            $staff = $user->staff;
            return (
                $staff?->hasAnyRole(array_merge($adminOrExec, ['program_manager']))
                || $staff?->department?->name === 'Sports'
                || $staff?->position?->name == 'Tutor'
                || $staff?->position?->name == 'Researcher'
            );
        });

        Gate::define('manage-events', function (User $user) {
            return $user->user_type === 'staff';
        });

        Gate::define('work-on-competitions', function (User $user) use ($adminOrExec, $programMgmt) {
            $staff = $user->staff;
            return $staff?->hasAnyRole(array_merge($adminOrExec, $programMgmt));
        });

        Gate::define('create-competitions', function (User $user) use ($adminOrExec) {
            $staff = $user->staff;
            return (
                $staff?->hasAnyRole(array_merge($adminOrExec, ['program_manager']))
                || $staff?->department?->name === 'Sports'
            );
        });

        Gate::define('manage-home-visits', function (User $user, ?HomeVisit $homeVisit = null) use ($adminOrExec) {
            $staff = $user->staff;

            if (!$staff) {
                return false;
            }

            $hasAssignedHomeVisits = HomeVisit::where('assigned_staff_id', $staff->id)->exists();

            return (
                $staff?->hasAnyRole($adminOrExec)
                || $staff?->position?->name === 'Social Worker'
                || ($staff?->department?->name === 'Sports' && $staff?->hasAnyRole(['program_manager']))
                || $hasAssignedHomeVisits
                || ($homeVisit && (int) $homeVisit->assigned_staff_id === (int) $staff->id)
            );
        });

        Gate::define('manage-donors-and-grants', function (User $user) use ($adminOrExec) {
            $staff = $user->staff;
            return $staff?->hasAnyRole(array_merge($adminOrExec, ['donor_manager', 'admin_finance_staff']));
        });

        Gate::define('manage-reports', function (User $user) use ($adminOrExec) {
            $staff = $user->staff;
            return $staff?->hasAnyRole(array_merge($adminOrExec, ['donor_manager', 'admin_finance_staff', 'program_manager']));
        });

        Gate::define('is-admin', function (User $user) {
            return ReportRateLimiter::isExempt($user);
        });

        // donor-facing gates
        Gate::define('login-as-donor', function (User $user) {
            return $user->user_type === "donor";
        });

        Gate::policy(\App\Models\Donation::class, DonationPolicy::class);

        ActivityParticipant::observe(ActivityParticipantObserver::class);
        Attendance::observe(AttendanceObserver::class);
        HomeVisit::observe(HomeVisitObserver::class);
        AcademicRecord::observe(AcademicRecordObserver::class);
        FfaAssessmentRecord::observe(FfaAssessmentRecordObserver::class);
        InjuryRecord::observe(InjuryRecordObserver::class);
        EducationEnrollment::observe(EducationEnrollmentObserver::class);
        CompetitionResult::observe(CompetitionResultObserver::class);
        EventModel::observe(\App\Observers\EventObserver::class);

        Event::listen(
            \Illuminate\Notifications\Events\NotificationSent::class,
            \App\Listeners\SendPushNotification::class,
        );
    }
}
