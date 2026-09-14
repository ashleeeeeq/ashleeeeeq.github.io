<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicRecord;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Allocation;
use App\Models\AssessmentCategory;
use App\Models\Beneficiary;
use App\Models\BeneficiaryStatusType;
use App\Models\Competition;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\EducationEnrollment;
use App\Models\Event;
use App\Models\EventType;
use App\Models\FfaAssessmentRecord;
use App\Models\FundingTarget;
use App\Models\Grant;
use App\Models\HomeVisit;
use App\Enums\ReportType;
use App\Models\Report;
use App\Models\SportType;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    private function getTypeGate(string $type): ?string
    {
        return match ($type) {
            'users_staff' => 'manage-users',
            'users_beneficiary' => 'work-on-beneficiaries',
            'users_donor' => 'manage-donors-and-grants',
            'beneficiary_status_types', 'assessment_categories', 'sport_types', 'activity_types', 'event_types' => 'manage-configurations',
            'grants' => 'manage-donors-and-grants',
            'donations' => 'manage-donors-and-grants',
            'activities' => 'work-on-activities',
            'events' => 'manage-events',
            'competitions' => 'work-on-competitions',
            'home_visits' => 'manage-home-visits',
            'reports' => 'is-admin',
            'education_enrollments', 'academic_records', 'ffa_assessment_records' => 'manage-education-records',
            'allocations', 'funding_targets' => 'manage-donors-and-grants',
            default => null,
        };
    }

    private function userCanAccessType(string $type): bool
    {
        $gate = $this->getTypeGate($type);
        if (!$gate) return false;
        return Auth::user()->can($gate);
    }

    public function index(Request $request): View|RedirectResponse
    {
        $typeFilter = $request->query('type');

        // Redirect if filter refers to an inaccessible type
        if ($typeFilter && !$this->userCanAccessType($typeFilter)) {
            return redirect('/archive');
        }

        $allTypes = [
            'users_staff', 'users_beneficiary', 'users_donor',
            'beneficiary_status_types', 'assessment_categories',
            'sport_types', 'activity_types', 'event_types',
            'grants', 'donations', 'activities', 'events',
            'competitions', 'home_visits', 'reports',
            'education_enrollments', 'academic_records', 'ffa_assessment_records',
            'allocations', 'funding_targets',
        ];

        $allowedTypes = array_values(array_filter($allTypes, fn($t) => $this->userCanAccessType($t)));

        $archivedUsers = collect();
        if ($this->userCanAccessType('users_staff') || $this->userCanAccessType('users_beneficiary') || $this->userCanAccessType('users_donor')) {
            $archivedUsers = User::onlyTrashed()->with([
                'staff' => fn($q) => $q->withTrashed(),
                'beneficiary' => fn($q) => $q->withTrashed(),
                'donor' => fn($q) => $q->withTrashed(),
            ])->orderBy('deleted_at', 'desc')->get()->map(function ($user) {
                $profile = $user->staff ?? $user->beneficiary ?? $user->donor;
                $user->archived_name = $profile?->display_name ?? $user->email;
                $user->archived_type = match (true) {
                    (bool) $user->staff => 'Staff',
                    (bool) $user->beneficiary => 'Beneficiary',
                    (bool) $user->donor => 'Donor',
                    default => 'User',
                };
                return $user;
            });
        }

        $archivedBeneficiaryStatusTypes = $this->userCanAccessType('beneficiary_status_types') ? BeneficiaryStatusType::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedAssessmentCategories = $this->userCanAccessType('assessment_categories') ? AssessmentCategory::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedSportTypes = $this->userCanAccessType('sport_types') ? SportType::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedActivityTypes = $this->userCanAccessType('activity_types') ? ActivityType::onlyTrashed()->with('program')->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedEventTypes = $this->userCanAccessType('event_types') ? EventType::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedGrants = $this->userCanAccessType('grants') ? Grant::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedDonations = $this->userCanAccessType('donations') ? Donation::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedActivities = $this->userCanAccessType('activities') ? Activity::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedEvents = $this->userCanAccessType('events') ? Event::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedCompetitions = $this->userCanAccessType('competitions') ? Competition::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedHomeVisits = $this->userCanAccessType('home_visits') ? HomeVisit::onlyTrashed()->with(['beneficiary' => fn($q) => $q->withTrashed()])->orderBy('deleted_at', 'desc')->get() : collect();
        $archivedReports = $this->userCanAccessType('reports') ? Report::onlyTrashed()->orderBy('deleted_at', 'desc')->get()->map(function ($item) {
            $item->display_name = ReportType::tryFrom($item->type)?->label() ?? ucwords(str_replace('_', ' ', $item->type));
            return $item;
        }) : collect();
        $archivedEducationEnrollments = $this->userCanAccessType('education_enrollments') ? EducationEnrollment::onlyTrashed()->with(['beneficiary' => fn($q) => $q->withTrashed()])->orderBy('deleted_at', 'desc')->get()->map(function ($item) {
            $item->display_name = $item->school_name . ' — ' . $item->grade_level;
            $item->extra_info = $item->beneficiary?->display_name ?? 'No beneficiary';
            return $item;
        }) : collect();
        $archivedAcademicRecords = $this->userCanAccessType('academic_records') ? AcademicRecord::onlyTrashed()->with(['beneficiary' => fn($q) => $q->withTrashed(), 'educationEnrollment' => fn($q) => $q->withTrashed()])->orderBy('deleted_at', 'desc')->get()->map(function ($item) {
            $item->display_name = ($item->school_name ? $item->school_name . ' — ' : '') . $item->term . ' (GWA ' . $item->gwa . ')';
            $enrollment = $item->educationEnrollment;
            $item->education_enrollment_info = $enrollment
                ? $enrollment->school_name . ' — ' . $enrollment->grade_level . ' (' . ($enrollment->academic_year_start_date?->format('Y') . '-' . $enrollment->academic_year_end_date?->format('Y')) . ')'
                : ($item->education_enrollment_id ? 'Enrollment #' . $item->education_enrollment_id . ' (deleted)' : 'No enrollment');
            $item->extra_info = $item->beneficiary?->display_name ?? 'No beneficiary';
            return $item;
        }) : collect();
        $archivedFfaRecords = $this->userCanAccessType('ffa_assessment_records') ? FfaAssessmentRecord::onlyTrashed()->with(['beneficiary' => fn($q) => $q->withTrashed(), 'assessmentCategory'])->orderBy('deleted_at', 'desc')->get()->map(function ($item) {
            $item->display_name = ($item->name ?? 'FFA Assessment') . ' — ' . ($item->assessmentCategory?->assessment_name ?? 'N/A');
            $item->extra_info = $item->beneficiary?->display_name ?? 'No beneficiary';
            return $item;
        }) : collect();
        $archivedAllocations = $this->userCanAccessType('allocations') ? Allocation::onlyTrashed()->with(['beneficiary' => fn($q) => $q->withTrashed(), 'donation' => fn($q) => $q->withTrashed(), 'grant' => fn($q) => $q->withTrashed()])->orderBy('deleted_at', 'desc')->get()->map(function ($item) {
            $item->display_name = ($item->beneficiary?->display_name ?? 'Allocation #' . $item->id);
            $item->reference_info = $item->donation?->reference_number ?? $item->grant?->grant_name ?? '-';
            $item->extra_info = '₱' . number_format($item->amount_cents / 100, 2);
            return $item;
        }) : collect();
        $archivedFundingTargets = $this->userCanAccessType('funding_targets') ? FundingTarget::onlyTrashed()->with('program')->orderBy('deleted_at', 'desc')->get()->map(function ($item) {
            $item->display_name = ($item->program?->program_name ?? 'Organization-wide') . ' — ' . $item->year;
            $item->extra_info = '₱' . number_format($item->target_amount_cents / 100, 2);
            return $item;
        }) : collect();

        $groups = [];

        if (in_array('users_staff', $allowedTypes) && (!$typeFilter || $typeFilter === 'users_staff')) {
            $staffUsers = $archivedUsers->filter(fn($u) => $u->archived_type === 'Staff');
            if ($staffUsers->isNotEmpty()) {
                $groups[] = ['key' => 'users_staff', 'label' => 'Staff Users', 'items' => $staffUsers->values(), 'deletedAtField' => 'deleted_at', 'nameField' => 'archived_name', 'identifierField' => 'login_id', 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('users_beneficiary', $allowedTypes) && (!$typeFilter || $typeFilter === 'users_beneficiary')) {
            $beneficiaryUsers = $archivedUsers->filter(fn($u) => $u->archived_type === 'Beneficiary');
            if ($beneficiaryUsers->isNotEmpty()) {
                $groups[] = ['key' => 'users_beneficiary', 'label' => 'Beneficiary Users', 'items' => $beneficiaryUsers->values(), 'deletedAtField' => 'deleted_at', 'nameField' => 'archived_name', 'identifierField' => 'login_id', 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('users_donor', $allowedTypes) && (!$typeFilter || $typeFilter === 'users_donor')) {
            $donorUsers = $archivedUsers->filter(fn($u) => $u->archived_type === 'Donor');
            if ($donorUsers->isNotEmpty()) {
                $groups[] = ['key' => 'users_donor', 'label' => 'Donor Users', 'items' => $donorUsers->values(), 'deletedAtField' => 'deleted_at', 'nameField' => 'archived_name', 'identifierField' => 'login_id', 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('beneficiary_status_types', $allowedTypes) && (!$typeFilter || $typeFilter === 'beneficiary_status_types')) {
            if ($archivedBeneficiaryStatusTypes->isNotEmpty()) {
                $groups[] = ['key' => 'beneficiary_status_types', 'label' => 'Beneficiary Status Types', 'items' => $archivedBeneficiaryStatusTypes, 'deletedAtField' => 'deleted_at', 'nameField' => 'status_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('assessment_categories', $allowedTypes) && (!$typeFilter || $typeFilter === 'assessment_categories')) {
            if ($archivedAssessmentCategories->isNotEmpty()) {
                $groups[] = ['key' => 'assessment_categories', 'label' => 'FFA Assessment Categories', 'items' => $archivedAssessmentCategories, 'deletedAtField' => 'deleted_at', 'nameField' => 'assessment_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('sport_types', $allowedTypes) && (!$typeFilter || $typeFilter === 'sport_types')) {
            if ($archivedSportTypes->isNotEmpty()) {
                $groups[] = ['key' => 'sport_types', 'label' => 'Sport Types', 'items' => $archivedSportTypes, 'deletedAtField' => 'deleted_at', 'nameField' => 'name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('activity_types', $allowedTypes) && (!$typeFilter || $typeFilter === 'activity_types')) {
            if ($archivedActivityTypes->isNotEmpty()) {
                $items = $archivedActivityTypes->map(function ($item) {
                    $item->extra_info = $item->program?->program_name ? "({$item->program->program_name})" : null;
                    return $item;
                });
                $groups[] = ['key' => 'activity_types', 'label' => 'Activity Types', 'items' => $items, 'deletedAtField' => 'deleted_at', 'nameField' => 'name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('event_types', $allowedTypes) && (!$typeFilter || $typeFilter === 'event_types')) {
            if ($archivedEventTypes->isNotEmpty()) {
                $groups[] = ['key' => 'event_types', 'label' => 'Event Types', 'items' => $archivedEventTypes, 'deletedAtField' => 'deleted_at', 'nameField' => 'name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('grants', $allowedTypes) && (!$typeFilter || $typeFilter === 'grants')) {
            if ($archivedGrants->isNotEmpty()) {
                $groups[] = ['key' => 'grants', 'label' => 'Grants', 'items' => $archivedGrants, 'deletedAtField' => 'deleted_at', 'nameField' => 'grant_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('donations', $allowedTypes) && (!$typeFilter || $typeFilter === 'donations')) {
            if ($archivedDonations->isNotEmpty()) {
                $groups[] = ['key' => 'donations', 'label' => 'Donations', 'items' => $archivedDonations, 'deletedAtField' => 'deleted_at', 'nameField' => 'reference_number', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('activities', $allowedTypes) && (!$typeFilter || $typeFilter === 'activities')) {
            if ($archivedActivities->isNotEmpty()) {
                $groups[] = ['key' => 'activities', 'label' => 'Activities', 'items' => $archivedActivities, 'deletedAtField' => 'deleted_at', 'nameField' => 'name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('events', $allowedTypes) && (!$typeFilter || $typeFilter === 'events')) {
            if ($archivedEvents->isNotEmpty()) {
                $groups[] = ['key' => 'events', 'label' => 'Events', 'items' => $archivedEvents, 'deletedAtField' => 'deleted_at', 'nameField' => 'name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('competitions', $allowedTypes) && (!$typeFilter || $typeFilter === 'competitions')) {
            if ($archivedCompetitions->isNotEmpty()) {
                $groups[] = ['key' => 'competitions', 'label' => 'Competitions', 'items' => $archivedCompetitions, 'deletedAtField' => 'deleted_at', 'nameField' => 'name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('home_visits', $allowedTypes) && (!$typeFilter || $typeFilter === 'home_visits')) {
            if ($archivedHomeVisits->isNotEmpty()) {
                $items = $archivedHomeVisits->map(function ($item) {
                    $item->extra_info = $item->beneficiary?->display_name ?? 'No beneficiary';
                    return $item;
                });
                $groups[] = ['key' => 'home_visits', 'label' => 'Home Visits', 'items' => $items, 'deletedAtField' => 'deleted_at', 'nameField' => 'purpose', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('reports', $allowedTypes) && (!$typeFilter || $typeFilter === 'reports')) {
            if ($archivedReports->isNotEmpty()) {
                $groups[] = ['key' => 'reports', 'label' => 'Reports', 'items' => $archivedReports, 'deletedAtField' => 'deleted_at', 'nameField' => 'display_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('education_enrollments', $allowedTypes) && (!$typeFilter || $typeFilter === 'education_enrollments')) {
            if ($archivedEducationEnrollments->isNotEmpty()) {
                $groups[] = ['key' => 'education_enrollments', 'label' => 'Education Enrollments', 'items' => $archivedEducationEnrollments, 'deletedAtField' => 'deleted_at', 'nameField' => 'display_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('academic_records', $allowedTypes) && (!$typeFilter || $typeFilter === 'academic_records')) {
            if ($archivedAcademicRecords->isNotEmpty()) {
                $groups[] = ['key' => 'academic_records', 'label' => 'Academic Records', 'items' => $archivedAcademicRecords, 'deletedAtField' => 'deleted_at', 'nameField' => 'display_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('ffa_assessment_records', $allowedTypes) && (!$typeFilter || $typeFilter === 'ffa_assessment_records')) {
            if ($archivedFfaRecords->isNotEmpty()) {
                $groups[] = ['key' => 'ffa_assessment_records', 'label' => 'FFA Assessment Records', 'items' => $archivedFfaRecords, 'deletedAtField' => 'deleted_at', 'nameField' => 'display_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('allocations', $allowedTypes) && (!$typeFilter || $typeFilter === 'allocations')) {
            if ($archivedAllocations->isNotEmpty()) {
                $groups[] = ['key' => 'allocations', 'label' => 'Allocations', 'items' => $archivedAllocations, 'deletedAtField' => 'deleted_at', 'nameField' => 'display_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        if (in_array('funding_targets', $allowedTypes) && (!$typeFilter || $typeFilter === 'funding_targets')) {
            if ($archivedFundingTargets->isNotEmpty()) {
                $groups[] = ['key' => 'funding_targets', 'label' => 'Funding Targets', 'items' => $archivedFundingTargets, 'deletedAtField' => 'deleted_at', 'nameField' => 'display_name', 'identifierField' => null, 'restoreRoute' => 'archive.restore', 'forceDestroyRoute' => 'archive.force-destroy'];
            }
        }

        $counts = [
            'users_staff' => $archivedUsers->filter(fn($u) => $u->archived_type === 'Staff')->count(),
            'users_beneficiary' => $archivedUsers->filter(fn($u) => $u->archived_type === 'Beneficiary')->count(),
            'users_donor' => $archivedUsers->filter(fn($u) => $u->archived_type === 'Donor')->count(),
            'beneficiary_status_types' => $archivedBeneficiaryStatusTypes->count(),
            'assessment_categories' => $archivedAssessmentCategories->count(),
            'sport_types' => $archivedSportTypes->count(),
            'activity_types' => $archivedActivityTypes->count(),
            'event_types' => $archivedEventTypes->count(),
            'grants' => $archivedGrants->count(),
            'donations' => $archivedDonations->count(),
            'activities' => $archivedActivities->count(),
            'events' => $archivedEvents->count(),
            'competitions' => $archivedCompetitions->count(),
            'home_visits' => $archivedHomeVisits->count(),
            'reports' => $archivedReports->count(),
            'education_enrollments' => $archivedEducationEnrollments->count(),
            'academic_records' => $archivedAcademicRecords->count(),
            'ffa_assessment_records' => $archivedFfaRecords->count(),
            'allocations' => $archivedAllocations->count(),
            'funding_targets' => $archivedFundingTargets->count(),
        ];

        return view('admin.archive.index', [
            'name' => Auth::user()?->display_name ?? 'Admin',
            'groups' => $groups,
            'counts' => $counts,
            'selectedType' => $typeFilter,
            'allowedTypes' => $allowedTypes,
            'totalArchived' => collect($allowedTypes)->sum(fn($t) => $counts[$t] ?? 0),
        ]);
    }

    public function restore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            'id' => ['required', 'integer'],
        ]);

        if (!$this->userCanAccessType($validated['type'])) {
            abort(403);
        }

        $model = $this->resolveModel($validated['type'], $validated['id']);
        if ($model) {
            if ($model instanceof User) {
                $model->load([
                    'staff' => fn($q) => $q->withTrashed(),
                    'beneficiary' => fn($q) => $q->withTrashed(),
                    'donor' => fn($q) => $q->withTrashed(),
                ]);
                if ($model->staff) $model->staff->restore();
                if ($model->beneficiary) $model->beneficiary->restore();
                if ($model->donor) $model->donor->restore();
            }
            $model->restore();
        }

        return back()->with('status', 'Record restored from archive.');
    }

    public function forceDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            'id' => ['required', 'integer'],
        ]);

        if (!$this->userCanAccessType($validated['type'])) {
            abort(403);
        }

        $model = $this->resolveModel($validated['type'], $validated['id']);
        if ($model) {
            if ($model instanceof User) {
                $model->load([
                    'staff' => fn($q) => $q->withTrashed(),
                    'beneficiary' => fn($q) => $q->withTrashed(),
                    'donor' => fn($q) => $q->withTrashed(),
                ]);
                if ($model->staff) $model->staff->forceDelete();
                if ($model->beneficiary) $model->beneficiary->forceDelete();
                if ($model->donor) $model->donor->forceDelete();
            }
            $model->forceDelete();
        }

        return back()->with('status', 'Record permanently deleted.');
    }

    public function restoreAll(Request $request): RedirectResponse
    {
        $typeFilter = $request->input('type');

        if ($typeFilter && !$this->userCanAccessType($typeFilter)) {
            abort(403);
        }

        $models = $this->resolveModels($typeFilter);

        foreach ($models as $model) {
            if ($model instanceof User) {
                $model->load([
                    'staff' => fn($q) => $q->withTrashed(),
                    'beneficiary' => fn($q) => $q->withTrashed(),
                    'donor' => fn($q) => $q->withTrashed(),
                ]);
                if ($model->staff) $model->staff->restore();
                if ($model->beneficiary) $model->beneficiary->restore();
                if ($model->donor) $model->donor->restore();
            }
            $model->restore();
        }

        return back()->with('status', 'All archived records restored.');
    }

    public function forceDeleteAll(Request $request): RedirectResponse
    {
        $typeFilter = $request->input('type');

        if ($typeFilter && !$this->userCanAccessType($typeFilter)) {
            abort(403);
        }

        $models = $this->resolveModels($typeFilter);

        foreach ($models as $model) {
            if ($model instanceof User) {
                $model->load([
                    'staff' => fn($q) => $q->withTrashed(),
                    'beneficiary' => fn($q) => $q->withTrashed(),
                    'donor' => fn($q) => $q->withTrashed(),
                ]);
                if ($model->staff) $model->staff->forceDelete();
                if ($model->beneficiary) $model->beneficiary->forceDelete();
                if ($model->donor) $model->donor->forceDelete();
            }
            $model->forceDelete();
        }

        return back()->with('status', 'All archived records permanently deleted.');
    }

    public function restoreSelected(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct'],
        ]);

        $type = $validated['type'];
        $ids = $validated['ids'];

        if (!$this->userCanAccessType($type)) {
            abort(403);
        }

        $class = $this->getModelClass($type);
        if (!$class) {
            abort(404);
        }

        $models = $class::onlyTrashed()->whereIn('id', $ids)->get();

        foreach ($models as $model) {
            if ($model instanceof User) {
                $model->load([
                    'staff' => fn($q) => $q->withTrashed(),
                    'beneficiary' => fn($q) => $q->withTrashed(),
                    'donor' => fn($q) => $q->withTrashed(),
                ]);
                if ($model->staff) $model->staff->restore();
                if ($model->beneficiary) $model->beneficiary->restore();
                if ($model->donor) $model->donor->restore();
            }
            $model->restore();
        }

        $count = $models->count();

        if ($count === 0) {
            return redirect()->route('archive.index', $type ? ['type' => $type] : [])->with('status', 'No records needed restoring — already restored.');
        }

        return redirect()->route('archive.index', $type ? ['type' => $type] : [])->with('status', $count . ' records restored from archive.');
    }

    public function forceDeleteSelected(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct'],
        ]);

        $type = $validated['type'];
        $ids = $validated['ids'];

        if (!$this->userCanAccessType($type)) {
            abort(403);
        }

        $class = $this->getModelClass($type);
        if (!$class) {
            abort(404);
        }

        $models = $class::onlyTrashed()->whereIn('id', $ids)->get();

        foreach ($models as $model) {
            if ($model instanceof User) {
                $model->load([
                    'staff' => fn($q) => $q->withTrashed(),
                    'beneficiary' => fn($q) => $q->withTrashed(),
                    'donor' => fn($q) => $q->withTrashed(),
                ]);
                if ($model->staff) $model->staff->forceDelete();
                if ($model->beneficiary) $model->beneficiary->forceDelete();
                if ($model->donor) $model->donor->forceDelete();
            }
            $model->forceDelete();
        }

        $count = $models->count();

        if ($count === 0) {
            return redirect()->route('archive.index', $type ? ['type' => $type] : [])->with('status', 'No records needed deleting — already deleted.');
        }

        return redirect()->route('archive.index', $type ? ['type' => $type] : [])->with('status', $count . ' records permanently deleted.');
    }

    private function resolveModel(string $type, int $id)
    {
        $class = $this->getModelClass($type);
        if (!$class) return null;

        return $class::withTrashed()->where('id', $id)->first();
    }

    private function resolveModels(?string $typeFilter): array
    {
        $types = $typeFilter ? [$typeFilter] : [
            'users_staff', 'users_beneficiary', 'users_donor',
            'beneficiary_status_types', 'assessment_categories',
            'sport_types', 'activity_types', 'event_types',
            'grants', 'donations', 'activities', 'events',
            'competitions', 'home_visits', 'reports',
            'education_enrollments', 'academic_records', 'ffa_assessment_records',
            'allocations', 'funding_targets',
        ];

        // Only process types the user has access to
        $types = array_values(array_filter($types, fn($t) => $this->userCanAccessType($t)));

        $models = [];

        foreach ($types as $type) {
            $class = $this->getModelClass($type);
            if (!$class) continue;

            if ($type === 'users_staff') {
                $users = User::onlyTrashed()->whereHas('staff')->get();
                foreach ($users as $user) {
                    $models[] = $user;
                }
            } elseif ($type === 'users_beneficiary') {
                $users = User::onlyTrashed()->whereHas('beneficiary')->get();
                foreach ($users as $user) {
                    $models[] = $user;
                }
            } elseif ($type === 'users_donor') {
                $users = User::onlyTrashed()->whereHas('donor')->get();
                foreach ($users as $user) {
                    $models[] = $user;
                }
            } elseif ($class === User::class) {
                $records = $class::onlyTrashed()->get();
                foreach ($records as $record) {
                    $models[] = $record;
                }
            } else {
                $records = $class::onlyTrashed()->get();
                foreach ($records as $record) {
                    $models[] = $record;
                }
            }
        }

        return $models;
    }

    private function getModelClass(string $type): ?string
    {
        return match ($type) {
            'users_staff', 'users_beneficiary', 'users_donor', 'user' => User::class,
            'beneficiary_status_types' => BeneficiaryStatusType::class,
            'assessment_categories' => AssessmentCategory::class,
            'sport_types' => SportType::class,
            'activity_types' => ActivityType::class,
            'event_types' => EventType::class,
            'grants' => Grant::class,
            'donations' => Donation::class,
            'activities' => Activity::class,
            'events' => Event::class,
            'competitions' => Competition::class,
            'home_visits' => HomeVisit::class,
            'reports' => Report::class,
            'education_enrollments' => EducationEnrollment::class,
            'academic_records' => AcademicRecord::class,
            'ffa_assessment_records' => FfaAssessmentRecord::class,
            'allocations' => Allocation::class,
            'funding_targets' => FundingTarget::class,
            default => null,
        };
    }
}
