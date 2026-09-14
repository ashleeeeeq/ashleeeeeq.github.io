<?php

use App\Http\Controllers\Api\AcademicRecordController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\ActivitySessionAttendanceController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\DonorAuthController;
use App\Http\Controllers\Api\DonorController;
use App\Http\Controllers\Api\DonorProfileController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FfaAssessmentController;
use App\Http\Controllers\Api\HomeVisitController;
use App\Http\Controllers\Api\InjuryRecordController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('donor/register', [DonorAuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-reset-code', [AuthController::class, 'verifyResetCode']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

Route::prefix('verify')->group(function () {
    Route::post('resend-email', [AuthController::class, 'resendVerificationEmail']);
});

Route::get('verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);

    // Device tokens (push notifications)
    Route::post('device-tokens', [DeviceTokenController::class, 'register']);
    Route::post('device-tokens/revoke', [DeviceTokenController::class, 'revoke']);
    
    // User profile (single endpoint that returns all user data including beneficiary/donor)
    Route::get('user', [ProfileController::class, 'show']);
    
    // Profile update endpoints
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('profile/beneficiary', [ProfileController::class, 'updateBeneficiary']);
    Route::put('profile/donor', [ProfileController::class, 'updateDonor']);
    
    // Avatar routes
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('profile/avatar', [ProfileController::class, 'deleteAvatar']);
    
    // Beneficiary status history
    Route::get('beneficiaries/{beneficiary}/status', [BeneficiaryController::class, 'statusHistory']);
    
    // Activity Session Attendance (requires registration check)
    Route::post('attendance/activity-session/log', [ActivitySessionAttendanceController::class, 'logAttendance']);
    Route::get('attendance/activity-session/history/{beneficiary_id}', [ActivitySessionAttendanceController::class, 'getHistory']);
    
    // Event Attendance (anyone can attend)
    Route::post('attendance/event/log', [EventController::class, 'logAttendance']);
    Route::get('attendance/event/history/{beneficiary_id}', [EventController::class, 'getHistory']);
    
    // Legacy attendance route
    Route::post('/attendance/log', [AttendanceController::class, 'logAttendance']);
    Route::get('/attendance/history/{beneficiary_id}', [AttendanceController::class, 'getAttendanceHistory']);
    
    // Beneficiary routes (only for beneficiaries)
    Route::middleware('user.type:beneficiary')->prefix('beneficiary')->group(function () {
        Route::get('dashboard', [BeneficiaryController::class, 'dashboard']);
        Route::get('activities', [ActivityController::class, 'index']);
        Route::get('activities/{id}', [ActivityController::class, 'show']);
        Route::post('activities/{id}/register', [ActivityController::class, 'register']);
        Route::get('events/{id}', [EventController::class, 'show']);
        Route::get('attendance', [BeneficiaryController::class, 'attendance']);
        Route::get('academic-records', [AcademicRecordController::class, 'index']);
        Route::get('ffa-assessments', [FfaAssessmentController::class, 'index']);
        Route::get('injury-records', [InjuryRecordController::class, 'index']);
        Route::get('enrollments', [EnrollmentController::class, 'index']);
        Route::get('enrollments/{id}', [EnrollmentController::class, 'show']);
        
        // Home Visit routes
        Route::get('home-visits', [HomeVisitController::class, 'index']);
        Route::get('home-visits/{id}', [HomeVisitController::class, 'show']);

        // Dashboard for mobile
        Route::get('dashboard/summary', [BeneficiaryController::class, 'summary']);
        
        // Notifications
        Route::get('notifications', [BeneficiaryController::class, 'notifications']);
        Route::get('notifications/unread-count', [BeneficiaryController::class, 'unreadNotificationCount']);
        Route::put('notifications/{id}/read', [BeneficiaryController::class, 'markNotificationRead']);
        Route::post('notifications/read-all', [BeneficiaryController::class, 'markAllNotificationsRead']);
        Route::delete('notifications/{id}', [BeneficiaryController::class, 'deleteNotification']);

    });

    
    // Donor routes (only for donors)
    Route::middleware('user.type:donor')->prefix('donor')->group(function () {
        Route::get('dashboard',            [DonorController::class, 'dashboard']);
        Route::get('programs',             [DonorController::class, 'programs']);
        Route::get('subscription-plans',   [DonorController::class, 'subscriptionPlans']);

        Route::post('checkout',            [CheckoutController::class, 'store']);
        Route::get('checkout/{checkout}',  [CheckoutController::class, 'show']);

        Route::get('donations',            [DonorController::class, 'donations']);
        Route::get('donations/{donation}', [DonorController::class, 'donationDetail']);

        Route::get('receipts/{donation}',          [DonorController::class, 'receipt']);
        Route::get('receipts/{donation}/download', [DonorController::class, 'downloadReceipt']);

        Route::get('subscriptions',                    [DonorController::class, 'subscriptions']);
        Route::post('subscriptions/{subscription}/cancel', [DonorController::class, 'cancelSubscription']);

        // Dashboard for mobile
        Route::get('dashboard/summary', [DonorController::class, 'summary']);
        Route::get('dashboard/trends', [DonorController::class, 'trends']);

        // Profile management for mobile
        Route::get('profile', [DonorProfileController::class, 'show']);
        Route::put('profile', [DonorProfileController::class, 'update']);
        Route::put('profile/contact', [DonorProfileController::class, 'updateContact']);
        Route::get('profile/stats', [DonorProfileController::class, 'stats']);

        // Notifications for donors
        Route::get('notifications', [DonorController::class, 'notifications']);
        Route::get('notifications/unread-count', [DonorController::class, 'unreadNotificationCount']);
        Route::put('notifications/{id}/read', [DonorController::class, 'markNotificationRead']);
        Route::post('notifications/read-all', [DonorController::class, 'markAllNotificationsRead']);
        Route::delete('notifications/{id}', [DonorController::class, 'deleteNotification']);
    });
});