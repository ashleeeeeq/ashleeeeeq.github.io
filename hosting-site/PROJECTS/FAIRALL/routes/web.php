<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\DonorRegistrationController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\DonorManagementController;
use App\Http\Controllers\DeliverableController;
use App\Http\Controllers\DonorDonationController;
use App\Http\Controllers\GrantsController;
use App\Http\Controllers\DonorPortalController;
use App\Http\Controllers\BeneficiaryAcademicRecordController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\BeneficiaryFfaAssessmentController;
use App\Http\Controllers\BeneficiaryGuardianController;
use App\Http\Controllers\BeneficiaryIntakeController;
use App\Http\Controllers\BeneficiaryInjuryRecordController;
use App\Http\Controllers\BeneficiaryManagementController;
use App\Http\Controllers\BeneficiaryStatusHistoryController;
use App\Http\Controllers\BeneficiaryActivityHistoryController;
use App\Http\Controllers\BeneficiaryDocumentParseController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivitySessionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DonationReconciliationController;
use App\Http\Controllers\HomeVisitController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionResultController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\FundingController;
use App\Http\Controllers\FundingTargetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Payments\PayPalWebhookController;
use App\Http\Controllers\Payments\XenditWebhookController;
use App\Http\Controllers\Payments\CheckoutController;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/paypal', [PayPalWebhookController::class, 'handle'])->name('webhooks.paypal.handle');
Route::post('/donate/checkout', [CheckoutController::class, 'create'])->name('donate.checkout');
Route::post('/webhooks/xendit', [XenditWebhookController::class, 'handle'])->name('webhooks.xendit.handle');
Route::get('/donate/return', [CheckoutController::class, 'handleReturn'])->name('donate.return');
Route::get('/donate/cancel', [CheckoutController::class, 'handleCancel'])->name('donate.cancel');
Route::get('/donate', [DonorPortalController::class, 'donationPage'])->name('donate.page');

// guest routes
Route::middleware('guest')->group(function () {

    Route::view('/', 'welcome');
    Route::view('/about', 'about');
    Route::view('/programs', 'programs');
    Route::view('/contact', 'contact');

    // show log in page
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    // authenticate user
    Route::post('/login', [AuthController::class, 'store']);

    // Donor registration
    Route::get('/register', [DonorRegistrationController::class, 'create']);
    Route::get('/register/{type}', [DonorRegistrationController::class, 'showForm']);
    Route::post('/register', [DonorRegistrationController::class, 'store'])->middleware('throttle:5,60');

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');

    Route::get('/verify-email/{id}/{hash}', function (Request $request, string $id, string $hash) {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(403);
        }

        $user->markEmailAsVerified();

        return redirect('/login')->with('status', 'Email verified successfully. You can now sign in.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/verify-email/resend', function (Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if ($user->email_verified_at) {
            return back()->withErrors(['email' => 'Email is already verified.']);
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'Verification email sent successfully.');
    })->name('verification.resend.public');
});

// auth routes
Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::delete('logout', [AuthController::class, 'destroy']);

    // Profile routes
    Route::get('/profile', [UserController::class, 'showProfile']);
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar']);
    Route::delete('/profile/avatar', [UserController::class, 'removeAvatar']);
    Route::get('/profile/email', [UserController::class, 'editEmail']);
    Route::post('/profile/email', [UserController::class, 'updateEmail']);
    Route::get('/profile/password', [UserController::class, 'editPassword']);
    Route::post('/profile/password', [UserController::class, 'updatePassword']);
    Route::post('/profile/resend-verification', [UserController::class, 'resendVerification']);

    // Force password change on first sign-in
    Route::get('/force-password-change', [UserController::class, 'showForcePasswordChange'])->name('force-password-change');
    Route::post('/force-password-change', [UserController::class, 'forcePasswordChange'])->name('force-password-change.store');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/{id}/mark-unread', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-unread');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // show dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard.beneficiary');
    Route::get('/dashboard/activity', [UserController::class, 'dashboard'])->defaults('section', 'activity')->name('dashboard.activity');
    Route::get('/dashboard/donor', [UserController::class, 'dashboard'])->defaults('section', 'donor')->name('dashboard.donor');

    // donor-facing gates (donor portal)
    Route::middleware(['can:login-as-donor'])->group(function () {
        Route::get('/donor-portal/donate', [DonorPortalController::class, 'donationPage'])->name('donor.portal.donate');
        Route::get('/donor-portal/donations', [DonorPortalController::class, 'donations'])->name('donor.portal.donations.index');
        Route::get('/donor-portal/donations/{donation}/receipt', [DonorPortalController::class, 'viewDonationReceipt'])->name('donor.portal.donations.receipt');
        Route::get('/donor-portal/donations/{donation}/receipt/download', [DonorPortalController::class, 'downloadDonationReceipt'])->name('donor.portal.donations.receipt.download');
        Route::get('/donor-portal/subscriptions', [DonorPortalController::class, 'subscriptions'])->name('donor.portal.subscriptions.index');
        Route::post('/donor-portal/subscriptions/{subscription}/cancel', [DonorPortalController::class, 'cancelSubscription'])->name('donor.portal.subscriptions.cancel');
    });

    // manage users routes
    Route::middleware(['can:manage-users'])->group(function () {
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/create', [UserManagementController::class, 'create']);
        Route::get('/users/{user}', [UserManagementController::class, 'show']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/bulk', [UserManagementController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
    });

    Route::middleware(['can:manage-donors-and-grants'])->prefix('donors')->name('donors.')->group(function () {
        Route::get('/', [DonorManagementController::class, 'index'])->name('index');
        Route::get('/create', [DonorManagementController::class, 'create'])->name('create');
        Route::post('/', [DonorManagementController::class, 'store'])->name('store');

        // All Donations (staff ledger)
        Route::get('/donations', [DonorDonationController::class, 'allIndex'])->name('donations.all');
        Route::get('/donations/create', [DonorDonationController::class, 'createAll'])->name('donations.create-all');
        Route::post('/donations', [DonorDonationController::class, 'storeAll'])->name('donations.store-all');
        Route::get('/donations/export/{format}', [DonorDonationController::class, 'exportAll'])->name('donations.export-all');
        Route::get('/donations/{donation}/edit', [DonorDonationController::class, 'editAll'])->name('donations.edit-all');
        Route::put('/donations/{donation}', [DonorDonationController::class, 'updateAll'])->name('donations.update-all');
        Route::delete('/donations/bulk', [DonorDonationController::class, 'bulkDestroyAll'])->name('donations.bulk-destroy-all');
        Route::delete('/donations/{donation}', [DonorDonationController::class, 'destroyAll'])->name('donations.destroy-all');
        Route::patch('/donations/{donation}/reconcile', [DonationReconciliationController::class, 'update'])->name('donations.reconcile');
        Route::patch('/donations/{donation}/resend-receipt', [DonationReconciliationController::class, 'resendReceipt'])->name('donations.resend-receipt');

        Route::delete('/bulk', [DonorManagementController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/{donor}', [DonorManagementController::class, 'show'])->name('show');
        Route::get('/{donor}/edit', [DonorManagementController::class, 'edit'])->name('edit');
        Route::put('/{donor}', [DonorManagementController::class, 'update'])->name('update');
        Route::delete('/{donor}', [DonorManagementController::class, 'destroy'])->name('destroy');

        // Deliverables
        Route::get('/{donor}/deliverables/create', [DeliverableController::class, 'createForDonor'])->name('deliverables.create');
        Route::get('/{donor}/deliverables', [DeliverableController::class, 'indexForDonor'])->name('deliverables.index');
        Route::post('/{donor}/deliverables', [DeliverableController::class, 'storeForDonor'])->name('deliverables.store');
        Route::get('/{donor}/deliverables/{deliverable}', [DeliverableController::class, 'showForDonor'])->name('deliverables.show');
        Route::get('/{donor}/deliverables/{deliverable}/edit', [DeliverableController::class, 'editForDonor'])->name('deliverables.edit');
        Route::put('/{donor}/deliverables/{deliverable}', [DeliverableController::class, 'updateForDonor'])->name('deliverables.update');
        Route::delete('/{donor}/deliverables/{deliverable}', [DeliverableController::class, 'destroyForDonor'])->name('deliverables.destroy');

        // Donations
        Route::get('/{donor}/donations', [DonorDonationController::class, 'index'])->name('donations.index');
        Route::get('/{donor}/donations/create', [DonorDonationController::class, 'create'])->name('donations.create');
        Route::post('/{donor}/donations', [DonorDonationController::class, 'store'])->name('donations.store');
        Route::get('/{donor}/donations/export/{format}', [DonorDonationController::class, 'export'])->name('donations.export');
        Route::get('/{donor}/donations/{donation}', [DonorDonationController::class, 'show'])->name('donations.show');
        Route::get('/{donor}/donations/{donation}/edit', [DonorDonationController::class, 'edit'])->name('donations.edit');
        Route::get('/{donor}/donations/{donation}/receipt', [DonorDonationController::class, 'viewReceipt'])->name('donations.receipt');
        Route::get('/{donor}/donations/{donation}/receipt/download', [DonorDonationController::class, 'downloadReceipt'])->name('donations.receipt.download');
        Route::put('/{donor}/donations/{donation}', [DonorDonationController::class, 'update'])->name('donations.update');
        Route::delete('/{donor}/donations/bulk', [DonorDonationController::class, 'bulkDestroy'])->name('donations.bulk-destroy');
        Route::delete('/{donor}/donations/{donation}', [DonorDonationController::class, 'destroy'])->name('donations.destroy');
    });

    // Funding section
    Route::middleware(['can:manage-donors-and-grants'])->prefix('funding')->name('funding.')->group(function () {
        Route::get('/', [FundingController::class, 'index'])->name('index');

        Route::get('/targets', [FundingTargetController::class, 'index'])->name('targets');
        Route::get('/targets/create', [FundingTargetController::class, 'create'])->name('targets.create');
        Route::post('/targets', [FundingTargetController::class, 'store'])->name('targets.store');
        Route::put('/targets/{fundingTarget}', [FundingTargetController::class, 'update'])->name('targets.update');
        Route::delete('/targets/bulk', [FundingTargetController::class, 'bulkDestroy'])->name('targets.bulk-destroy');
        Route::delete('/targets/{fundingTarget}', [FundingTargetController::class, 'destroy'])->name('targets.destroy');

        Route::prefix('allocations')->name('allocations.')->group(function () {
            Route::get('/', [\App\Http\Controllers\AllocationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\AllocationController::class, 'create'])->name('create');
            Route::get('/export/csv', [\App\Http\Controllers\AllocationController::class, 'exportCsv'])->name('export-csv');
            Route::post('/', [\App\Http\Controllers\AllocationController::class, 'store'])->name('store');
            Route::get('/poll', [\App\Http\Controllers\AllocationController::class, 'pollSource'])->name('poll');
            Route::delete('/bulk', [\App\Http\Controllers\AllocationController::class, 'bulkDestroy'])->name('bulk-destroy');
            Route::get('/{allocation}', [\App\Http\Controllers\AllocationController::class, 'show'])->name('show');
            Route::get('/{allocation}/edit', [\App\Http\Controllers\AllocationController::class, 'edit'])->name('edit');
            Route::put('/{allocation}', [\App\Http\Controllers\AllocationController::class, 'update'])->name('update');
            Route::delete('/{allocation}', [\App\Http\Controllers\AllocationController::class, 'destroy'])->name('destroy');
        });
    });

    // Grant Management
    Route::middleware(['can:manage-donors-and-grants'])->prefix('grants')->name('grants.')->group(function () {
        Route::get('/', [GrantsController::class, 'index'])->name('index');
        Route::get('/create', [GrantsController::class, 'create'])->name('create');
        Route::post('/', [GrantsController::class, 'store'])->name('store');
        Route::delete('/bulk', [GrantsController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/{grant}', [GrantsController::class, 'show'])->name('show');
        Route::get('/{grant}/edit', [GrantsController::class, 'edit'])->name('edit');
        Route::put('/{grant}', [GrantsController::class, 'update'])->name('update');
        Route::delete('/{grant}', [GrantsController::class, 'destroy'])->name('destroy');

        Route::get('/{grant}/deliverables/create', [DeliverableController::class, 'createForGrant'])->name('deliverables.create');
        Route::get('/{grant}/deliverables', [DeliverableController::class, 'indexForGrant'])->name('deliverables.index');
        Route::post('/{grant}/deliverables', [DeliverableController::class, 'storeForGrant'])->name('deliverables.store');
        Route::get('/{grant}/deliverables/{deliverable}', [DeliverableController::class, 'showForGrant'])->name('deliverables.show');
        Route::get('/{grant}/deliverables/{deliverable}/edit', [DeliverableController::class, 'editForGrant'])->name('deliverables.edit');
        Route::put('/{grant}/deliverables/{deliverable}', [DeliverableController::class, 'updateForGrant'])->name('deliverables.update');
        Route::delete('/{grant}/deliverables/{deliverable}', [DeliverableController::class, 'destroyForGrant'])->name('deliverables.destroy');
    });

    // manage configuration routes
    Route::middleware(['can:manage-configurations'])->group(function () {
        Route::get('/configuration', [ConfigurationController::class, 'index']);
        Route::post('/configuration/status-types', [ConfigurationController::class, 'storeStatusType']);
        Route::put('/configuration/status-types/{statusType}', [ConfigurationController::class, 'updateStatusType']);
        Route::delete('/configuration/status-types/{statusType}', [ConfigurationController::class, 'destroyStatusType']);
        Route::post('/configuration/assessment-categories', [ConfigurationController::class, 'storeAssessmentCategory']);
        Route::put('/configuration/assessment-categories/{assessmentCategory}', [ConfigurationController::class, 'updateAssessmentCategory']);
        Route::delete('/configuration/assessment-categories/{assessmentCategory}', [ConfigurationController::class, 'destroyAssessmentCategory']);
        Route::post('/configuration/sport-types', [ConfigurationController::class, 'storeSportType'])->name('configuration.sport-types.store');
        Route::put('/configuration/sport-types/{sportType}', [ConfigurationController::class, 'updateSportType'])->name('configuration.sport-types.update');
        Route::delete('/configuration/sport-types/{sportType}', [ConfigurationController::class, 'destroySportType'])->name('configuration.sport-types.destroy');
        Route::post('/configuration/activity-types', [ConfigurationController::class, 'storeActivityType'])->name('configuration.activity-types.store');
        Route::put('/configuration/activity-types/{activityType}', [ConfigurationController::class, 'updateActivityType'])->name('configuration.activity-types.update');
        Route::delete('/configuration/activity-types/{activityType}', [ConfigurationController::class, 'destroyActivityType'])->name('configuration.activity-types.destroy');
        Route::post('/configuration/event-types', [ConfigurationController::class, 'storeEventType'])->name('configuration.event-types.store');
        Route::put('/configuration/event-types/{eventType}', [ConfigurationController::class, 'updateEventType'])->name('configuration.event-types.update');
        Route::delete('/configuration/event-types/{eventType}', [ConfigurationController::class, 'destroyEventType'])->name('configuration.event-types.destroy');
        Route::put('/configuration/monitoring-configurations/{monitoringConfiguration}', [ConfigurationController::class, 'updateMonitoringConfiguration'])->name('configuration.monitoring-configurations.update');
    });

    // archive routes (accessible to all staff; per-type gating in controller)
    Route::get('/archive', [\App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('archive.index');
    Route::post('/archive/restore', [\App\Http\Controllers\Admin\ArchiveController::class, 'restore'])->name('archive.restore');
    Route::delete('/archive/force-destroy', [\App\Http\Controllers\Admin\ArchiveController::class, 'forceDestroy'])->name('archive.force-destroy');
    Route::post('/archive/restore-all', [\App\Http\Controllers\Admin\ArchiveController::class, 'restoreAll'])->name('archive.restore-all');
    Route::delete('/archive/force-delete-all', [\App\Http\Controllers\Admin\ArchiveController::class, 'forceDeleteAll'])->name('archive.force-delete-all');
    Route::post('/archive/restore-selected', [\App\Http\Controllers\Admin\ArchiveController::class, 'restoreSelected'])->name('archive.restore-selected');
    Route::delete('/archive/force-delete-selected', [\App\Http\Controllers\Admin\ArchiveController::class, 'forceDeleteSelected'])->name('archive.force-delete-selected');

    // manage beneficiaries routes
    Route::middleware(['can:work-on-beneficiaries'])->group(function () {
        Route::get('/beneficiaries/search', [BeneficiaryManagementController::class, 'search'])->name('beneficiaries.search')->middleware('throttle:60,1');
        Route::get('/beneficiaries', [BeneficiaryManagementController::class, 'index']);

        // protected routes for creating/editing beneficiaries and enrollment management
        Route::middleware(['can:create-beneficiaries'])->group(function () {
            Route::get('/beneficiaries/create', [BeneficiaryManagementController::class, 'create']);
            Route::post('/beneficiaries', [BeneficiaryManagementController::class, 'store']);
            Route::get('/beneficiaries/{beneficiary}/edit', [BeneficiaryManagementController::class, 'edit']);
            Route::put('/beneficiaries/{beneficiary}', [BeneficiaryManagementController::class, 'update']);
            Route::delete('/beneficiaries/bulk', [BeneficiaryManagementController::class, 'bulkDestroy'])->name('beneficiaries.bulk-destroy');
            Route::delete('/beneficiaries/{beneficiary}', [BeneficiaryManagementController::class, 'destroy']);

            Route::get('/beneficiaries/enrollment', [BeneficiaryManagementController::class, 'enrollmentIndex'])->name('beneficiaries.enrollment.index');
            Route::post('/beneficiaries/enrollment/enroll', [BeneficiaryManagementController::class, 'enrollToProgram']);
            Route::post('/beneficiaries/enrollment/unenroll', [BeneficiaryManagementController::class, 'unenrollFromProgram']);

            Route::post('/beneficiaries/parse-document', [BeneficiaryDocumentParseController::class, '__invoke']);
        });

        Route::get('/beneficiaries/{beneficiary}/profile', [BeneficiaryManagementController::class, 'profile']);

        // protected routes for education-only records
        Route::middleware(['can:manage-education-records'])->group(function () {
            Route::get('/beneficiaries/{beneficiary}/academic-records', [BeneficiaryAcademicRecordController::class, 'index']);
            // Academic records CRUD (simple forms)
            Route::get('/beneficiaries/{beneficiary}/academic-records/create', [BeneficiaryAcademicRecordController::class, 'create']);
            Route::post('/beneficiaries/{beneficiary}/academic-records', [BeneficiaryAcademicRecordController::class, 'store']);
            Route::get('/beneficiaries/{beneficiary}/academic-records/{academicRecord}', [BeneficiaryAcademicRecordController::class, 'show']);
            Route::get('/beneficiaries/{beneficiary}/academic-records/{academicRecord}/edit', [BeneficiaryAcademicRecordController::class, 'edit']);
            Route::put('/beneficiaries/{beneficiary}/academic-records/{academicRecord}', [BeneficiaryAcademicRecordController::class, 'update']);
            Route::delete('/beneficiaries/{beneficiary}/academic-records/bulk', [BeneficiaryAcademicRecordController::class, 'bulkDestroy'])->name('beneficiaries.academic-records.bulk-destroy');
            Route::delete('/beneficiaries/{beneficiary}/academic-records/{academicRecord}', [BeneficiaryAcademicRecordController::class, 'destroy']);

            // Education enrollments CRUD
            Route::get('/beneficiaries/{beneficiary}/enrollments', [\App\Http\Controllers\EducationEnrollmentController::class, 'index'])->name('beneficiaries.enrollments.index');
            Route::get('/beneficiaries/{beneficiary}/enrollments/create', [\App\Http\Controllers\EducationEnrollmentController::class, 'create'])->name('beneficiaries.enrollments.create');
            Route::post('/beneficiaries/{beneficiary}/enrollments', [\App\Http\Controllers\EducationEnrollmentController::class, 'store'])->name('beneficiaries.enrollments.store');
            Route::get('/beneficiaries/{beneficiary}/enrollments/{enrollment}', [\App\Http\Controllers\EducationEnrollmentController::class, 'show'])->name('beneficiaries.enrollments.show');
            Route::get('/beneficiaries/{beneficiary}/enrollments/{enrollment}/edit', [\App\Http\Controllers\EducationEnrollmentController::class, 'edit'])->name('beneficiaries.enrollments.edit');
            Route::put('/beneficiaries/{beneficiary}/enrollments/{enrollment}', [\App\Http\Controllers\EducationEnrollmentController::class, 'update'])->name('beneficiaries.enrollments.update');
            Route::delete('/beneficiaries/{beneficiary}/enrollments/bulk', [\App\Http\Controllers\EducationEnrollmentController::class, 'bulkDestroy'])->name('beneficiaries.enrollments.bulk-destroy');
            Route::delete('/beneficiaries/{beneficiary}/enrollments/{enrollment}', [\App\Http\Controllers\EducationEnrollmentController::class, 'destroy'])->name('beneficiaries.enrollments.destroy');

            Route::get('/beneficiaries/{beneficiary}/ffa-assessments', [BeneficiaryFfaAssessmentController::class, 'index']);
            // FFA assessment CRUD
            Route::get('/beneficiaries/{beneficiary}/ffa-assessments/create', [BeneficiaryFfaAssessmentController::class, 'create']);
            Route::post('/beneficiaries/{beneficiary}/ffa-assessments', [BeneficiaryFfaAssessmentController::class, 'store']);
            Route::get('/beneficiaries/{beneficiary}/ffa-assessments/{ffaAssessment}/edit', [BeneficiaryFfaAssessmentController::class, 'edit']);
            Route::put('/beneficiaries/{beneficiary}/ffa-assessments/{ffaAssessment}', [BeneficiaryFfaAssessmentController::class, 'update']);
            Route::delete('/beneficiaries/{beneficiary}/ffa-assessments/bulk', [BeneficiaryFfaAssessmentController::class, 'bulkDestroy'])->name('beneficiaries.ffa-assessments.bulk-destroy');
            Route::delete('/beneficiaries/{beneficiary}/ffa-assessments/{ffaAssessment}', [BeneficiaryFfaAssessmentController::class, 'destroy']);

            Route::get('/beneficiaries/{beneficiary}/education-intake', [BeneficiaryIntakeController::class, 'create'])->name('beneficiaries.education-intake.create');
            Route::post('/beneficiaries/{beneficiary}/education-intake', [BeneficiaryIntakeController::class, 'store'])->name('beneficiaries.education-intake.store');
            Route::get('/beneficiaries/{beneficiary}/education-intake/edit', [BeneficiaryIntakeController::class, 'edit'])->name('beneficiaries.education-intake.edit');
            Route::put('/beneficiaries/{beneficiary}/education-intake', [BeneficiaryIntakeController::class, 'update'])->name('beneficiaries.education-intake.update');
        });

        // protected routes for sports-only records
        Route::middleware(['can:manage-sports-records'])->group(function () {
            Route::get('/beneficiaries/{beneficiary}/injury-records', [BeneficiaryInjuryRecordController::class, 'index']);
            // Injury records CRUD
            Route::get('/beneficiaries/{beneficiary}/injury-records/create', [BeneficiaryInjuryRecordController::class, 'create']);
            Route::post('/beneficiaries/{beneficiary}/injury-records', [BeneficiaryInjuryRecordController::class, 'store']);
            Route::get('/beneficiaries/{beneficiary}/injury-records/{injuryRecord}', [BeneficiaryInjuryRecordController::class, 'show']);
            Route::get('/beneficiaries/{beneficiary}/injury-records/{injuryRecord}/edit', [BeneficiaryInjuryRecordController::class, 'edit']);
            Route::put('/beneficiaries/{beneficiary}/injury-records/{injuryRecord}', [BeneficiaryInjuryRecordController::class, 'update']);
            Route::delete('/beneficiaries/{beneficiary}/injury-records/{injuryRecord}', [BeneficiaryInjuryRecordController::class, 'destroy']);
        });

        Route::get('/beneficiaries/{beneficiary}/guardians', [BeneficiaryGuardianController::class, 'index']);
        // Guardians CRUD
        Route::get('/beneficiaries/{beneficiary}/guardians/create', [BeneficiaryGuardianController::class, 'create']);
        Route::post('/beneficiaries/{beneficiary}/guardians', [BeneficiaryGuardianController::class, 'store']);
        Route::get('/beneficiaries/{beneficiary}/guardians/{guardianId}', [BeneficiaryGuardianController::class, 'show']);
        Route::get('/beneficiaries/{beneficiary}/guardians/{guardianId}/edit', [BeneficiaryGuardianController::class, 'edit']);
        Route::put('/beneficiaries/{beneficiary}/guardians/{guardianId}', [BeneficiaryGuardianController::class, 'update']);
        Route::delete('/beneficiaries/{beneficiary}/guardians/{guardianId}', [BeneficiaryGuardianController::class, 'destroy']);

        Route::get('/beneficiaries/{beneficiary}/status-history', [BeneficiaryStatusHistoryController::class, 'index']);
        // Status history CRUD
        Route::get('/beneficiaries/{beneficiary}/status-history/create', [BeneficiaryStatusHistoryController::class, 'create']);
        Route::post('/beneficiaries/{beneficiary}/status-history', [BeneficiaryStatusHistoryController::class, 'store']);
        Route::get('/beneficiaries/{beneficiary}/status-history/{statusRecord}/edit', [BeneficiaryStatusHistoryController::class, 'edit']);
        Route::put('/beneficiaries/{beneficiary}/status-history/{statusRecord}', [BeneficiaryStatusHistoryController::class, 'update']);
        Route::delete('/beneficiaries/{beneficiary}/status-history/{statusRecord}', [BeneficiaryStatusHistoryController::class, 'destroy']);

        // Activity History
        Route::get('/beneficiaries/{beneficiary}/activity-history', [BeneficiaryActivityHistoryController::class, 'index'])->name('beneficiaries.activity-history');

        // Alerts
        Route::get('/beneficiaries/{beneficiary}/alerts', [BeneficiaryManagementController::class, 'alerts'])->name('beneficiaries.alerts');

        // Documents CRUD
        Route::get('/beneficiaries/{beneficiary}/documents', [DocumentController::class, 'index'])->name('beneficiaries.documents.index');
        Route::get('/beneficiaries/{beneficiary}/documents/create', [DocumentController::class, 'create'])->name('beneficiaries.documents.create');
        Route::post('/beneficiaries/{beneficiary}/documents', [DocumentController::class, 'store'])->name('beneficiaries.documents.store');
        Route::get('/beneficiaries/{beneficiary}/documents/{document}/edit', [DocumentController::class, 'edit'])->name('beneficiaries.documents.edit');
        Route::put('/beneficiaries/{beneficiary}/documents/{document}', [DocumentController::class, 'update'])->name('beneficiaries.documents.update');
        Route::delete('/beneficiaries/{beneficiary}/documents/{document}', [DocumentController::class, 'destroy'])->name('beneficiaries.documents.destroy');
        Route::get('/beneficiaries/{beneficiary}/documents/{document}/download', [DocumentController::class, 'download'])->name('beneficiaries.documents.download');
    });

    Route::middleware(['can:work-on-activities'])->group(function () {
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

        // protected routes for creating activities
        Route::middleware(['can:create-activities'])->group(function () {
            Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
            Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
            Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
            Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
            Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

            // participant list editable by those who can create/edit activities only
            Route::post('/activities/{activity}/participants', [ActivityController::class, 'addParticipant'])->name('activities.participants.store');
            Route::patch('/activities/{activity}/participants/{participant}', [ActivityController::class, 'removeParticipant'])->name('activities.participants.remove');
        });

        Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
        Route::get('/activities/{activity}/participants', [ActivityController::class, 'participants'])->name('activities.participants.index');
        Route::get('/activities/{activity}/history', [ActivityController::class, 'history'])->name('activities.history.index');

        Route::get('/activities/{activity}/sessions/create', [ActivitySessionController::class, 'create'])->name('activities.sessions.create');
        Route::post('/activities/{activity}/sessions', [ActivitySessionController::class, 'store'])->name('activities.sessions.store');
        Route::get('/activities/{activity}/sessions/{session}/edit', [ActivitySessionController::class, 'edit'])->name('activities.sessions.edit');
        Route::put('/activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'update'])->name('activities.sessions.update');
        Route::delete('/activities/{activity}/sessions/{session}', [ActivitySessionController::class, 'destroy'])->name('activities.sessions.destroy');

        Route::get('/activities/{activity}/sessions/{session}/attendance', [AttendanceController::class, 'createSession'])->name('activities.sessions.attendance.create');
        Route::post('/activities/{activity}/sessions/{session}/attendance', [AttendanceController::class, 'storeSession'])->name('activities.sessions.attendance.store');
        Route::get('/activities/{activity}/sessions/{session}/attendances/poll', [AttendanceController::class, 'pollSession'])->name('activities.sessions.attendance.poll');
        Route::post('/activities/{activity}/sessions/{session}/qr/regenerate', [ActivitySessionController::class, 'regenerateQr'])->name('activities.sessions.qr.regenerate');
        Route::get('/activities/{activity}/sessions/{session}/qr/print', [ActivitySessionController::class, 'showQr'])->name('activities.sessions.qr.print');
    });

    Route::middleware(['can:manage-events'])->group(function () {
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        Route::get('/events/{event}/attendance', [AttendanceController::class, 'createEvent'])->name('events.attendance.create');
        Route::post('/events/{event}/attendance', [AttendanceController::class, 'storeEvent'])->name('events.attendance.store');
        Route::get('/events/{event}/attendances/poll', [AttendanceController::class, 'pollEvent'])->name('events.attendance.poll');
        Route::post('/events/{event}/qr/regenerate', [EventController::class, 'regenerateQr'])->name('events.qr.regenerate');
        Route::get('/events/{event}/qr/print', [EventController::class, 'showQr'])->name('events.qr.print');
    });

    // List is available to staff (scoped in controller). Create/store/destroy remain restricted.
    Route::get('/home-visits', [HomeVisitController::class, 'index'])->name('home-visits.index');

    Route::middleware(['can:manage-home-visits'])->group(function () {
        Route::get('/home-visits/create', [HomeVisitController::class, 'create'])->name('home-visits.create');
        Route::post('/home-visits', [HomeVisitController::class, 'store'])->name('home-visits.store');
        Route::delete('/home-visits/bulk', [HomeVisitController::class, 'bulkDestroy'])->name('home-visits.bulk-destroy');
        Route::delete('/home-visits/{homeVisit}', [HomeVisitController::class, 'destroy'])->name('home-visits.destroy');
    });

    Route::middleware(['can:manage-home-visits,homeVisit'])->group(function () {
        Route::get('/home-visits/{homeVisit}', [HomeVisitController::class, 'show'])->name('home-visits.show');
        Route::get('/home-visits/{homeVisit}/edit', [HomeVisitController::class, 'edit'])->name('home-visits.edit');
        Route::put('/home-visits/{homeVisit}', [HomeVisitController::class, 'update'])->name('home-visits.update');
    });

    Route::middleware(['can:work-on-competitions'])->group(function () {
        Route::get('/competitions', [CompetitionController::class, 'index'])->name('competitions.index');

        Route::middleware(['can:create-competitions'])->group(function () {
            Route::get('/competitions/create', [CompetitionController::class, 'create'])->name('competitions.create');
            Route::post('/competitions', [CompetitionController::class, 'store'])->name('competitions.store');
            Route::get('/competitions/{competition}/edit', [CompetitionController::class, 'edit'])->name('competitions.edit');
            Route::put('/competitions/{competition}', [CompetitionController::class, 'update'])->name('competitions.update');
            Route::delete('/competitions/{competition}', [CompetitionController::class, 'destroy'])->name('competitions.destroy');
        });

        Route::get('/competitions/{competition}', [CompetitionController::class, 'show'])->name('competitions.show');
        Route::get('/competitions/{competition}/results/create', [CompetitionResultController::class, 'create'])->name('competition-results.create');
        Route::post('/competitions/{competition}/results', [CompetitionResultController::class, 'store'])->name('competition-results.store');
        Route::put('/competitions/{competition}/results/{competitionResult}', [CompetitionResultController::class, 'update'])->name('competition-results.update');
        Route::delete('/competitions/{competition}/results/{competitionResult}', [CompetitionResultController::class, 'destroy'])->name('competition-results.destroy');
    });

    // reports routes
    Route::middleware(['can:manage-reports'])->prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/template-preview', [ReportController::class, 'templatePreview'])->name('reports.template-preview');
        Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::post('/{id}/retry-failed', [ReportController::class, 'retryFailed'])->name('reports.retry-failed');
        Route::get('/{id}/status', [ReportController::class, 'status'])->name('reports.status');
        Route::get('/{id}/preview', [ReportController::class, 'previewPdf'])->name('reports.preview');
        Route::get('/{id}/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    });
    // Soft-delete (archive) restricted to admin / System Admin only
    Route::middleware(['can:is-admin'])->prefix('reports')->group(function () {
        Route::delete('/bulk', [ReportController::class, 'bulkDestroy'])->name('reports.bulk-destroy');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    });
});
