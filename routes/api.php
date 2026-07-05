<?php

use App\Http\Controllers\Api\Auth\Agent\AgentAuthenticationController;
use App\Http\Controllers\Api\Auth\Merchant\MerchantAuthenticationController;
use App\Http\Controllers\Api\Auth\User\EmailVerificationController;
use App\Http\Controllers\Api\Auth\User\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\User\LoginController;
use App\Http\Controllers\Api\Auth\User\RegisterController;
use App\Http\Controllers\Api\Auth\User\TwoFactorController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SubscriberController;
use App\Http\Controllers\Api\WebsiteController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::prefix('auth')->group(function () {

    // User
    Route::prefix('user')->group(function () {
        Route::post('login', [LoginController::class, 'login']);
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('send-verify-email', [EmailVerificationController::class, 'sendVerifyEmail']);
        Route::post('validate-verify-email', [EmailVerificationController::class, 'validateVerifyEmail']);
        Route::middleware('auth:sanctum', 'api_role:user')->group(function () {
            Route::post('logout', [LoginController::class, 'logout']);

            // Profile Info Update
            Route::post('personal-info-update', [RegisterController::class, 'personalInfoUpdate']);

            // web email verify
            Route::post('verify-email/{id}/{hash}', [EmailVerificationController::class, 'webEmailVerify'])->middleware(['signed', 'throttle:6,1'])->name('api.verification.verify');

            Route::get('get', [LoginController::class, 'getUser']);

            // Two Factor Verify
            Route::post('2fa/verify', TwoFactorController::class);
        });

        // Forgot Password
        Route::controller(ForgotPasswordController::class)->group(function () {
            Route::post('forgot-password', 'sendResetLinkEmail');
            Route::post('reset-verify-otp', 'verifyOtp');
            Route::post('reset-password', 'resetPassword');
        });
    });

    // Agent
    Route::prefix('agent')->middleware(['agent_system'])->group(function () {
        // Public routes
        Route::get('config', [AgentAuthenticationController::class, 'config']);
        Route::post('login', [AgentAuthenticationController::class, 'login']);
        Route::post('register', [AgentAuthenticationController::class, 'register']);

        // Email verification
        Route::post('send-verify-email', [EmailVerificationController::class, 'sendVerifyEmail']);
        Route::post('validate-verify-email', [EmailVerificationController::class, 'validateVerifyEmail']);

        // Protected routes
        Route::middleware('auth:sanctum', 'api_role:agent')->group(function () {
            Route::get('profile', [AgentAuthenticationController::class, 'profile']);
            Route::post('logout', [AgentAuthenticationController::class, 'logout']);

            // Profile Info Update
            Route::post('personal-info-update', [AgentAuthenticationController::class, 'personalInfoUpdate']);

            // Two Factor Verify
            Route::post('2fa/verify', TwoFactorController::class);
        });

        // Forgot Password
        Route::controller(ForgotPasswordController::class)->group(function () {
            Route::post('forgot-password', 'sendResetLinkEmail');
            Route::post('reset-verify-otp', 'verifyOtp');
            Route::post('reset-password', 'resetPassword');
        });
    });

    // Merchant
    Route::prefix('merchant')->group(function () {
        Route::post('login', [MerchantAuthenticationController::class, 'login']);

        // Email verification
        Route::post('send-verify-email', [EmailVerificationController::class, 'sendVerifyEmail']);
        Route::post('validate-verify-email', [EmailVerificationController::class, 'validateVerifyEmail']);

        Route::post('register', [MerchantAuthenticationController::class, 'register']);

        Route::middleware('auth:sanctum', 'api_role:merchant')->group(function () {
            // Get Profile
            Route::get('profile', [MerchantAuthenticationController::class, 'profile']);

            // Profile Info Update
            Route::post('personal-info-update', [MerchantAuthenticationController::class, 'personalInfoUpdate']);

            // Two Factor Verify
            Route::post('2fa/verify', TwoFactorController::class);

            // Log Out
            Route::post('logout', [MerchantAuthenticationController::class, 'logout']);
        });

        Route::controller(ForgotPasswordController::class)->group(function () {
            Route::post('forgot-password', 'sendResetLinkEmail');
            Route::post('reset-verify-otp', 'verifyOtp');
            Route::post('reset-password', 'resetPassword');
        });
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // FCM Notification
    Route::controller(NotificationController::class)->group(function () {
        Route::post('setup-fcm', 'registerDevice');
    });
});

// General settings
Route::controller(GeneralController::class)->group(function () {
    Route::get('get-countries', 'getCountries');
    Route::get('get-kyc-status', 'getKycStatus');
    Route::get('get-branches', 'getBranches');
    Route::get('get-currencies', 'getCurrencies');
    Route::get('get-settings', 'getSettings');
    Route::get('get-banks', 'getBanks');
    Route::get('get-languages', 'getLanguages');
    Route::get('get-card-providers', 'getCardProviders');
    Route::get('get-register-fields/{type}', 'getRegisterFields')->defaults('type', 'user');
    Route::get('get-notification-types/{type}', 'getNotificationTypes');
    Route::get('get-onboarding-screen-images', 'getOnboardingScreenImages');
    Route::get('convert/{amount}/{currencyCode}/{thousandSeparatorRemove?}/{fromCurrency?}', 'convertCurrency')->defaults('thousandSeparatorRemove', 'true');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('get-plugins', 'getPlugins');
        Route::get('get-navigation', 'getNavigation');
        Route::get('get-transaction-types-and-statuses', 'getTransactionTypesAndStatuses');
        Route::get('get-bill-countries/{type}', 'getBillCountries');
        Route::get('get-withdraw-methods', 'getWithdrawMethods');
        Route::get('get-notifications', 'getNotifications');
        Route::post('mark-as-read-notification', 'markNotification');
        Route::get('get-passcode-status', 'getPasscodeStatus');
    });
});

// Website
Route::controller(WebsiteController::class)->group(function () {
    Route::get('home-data', 'index');
    Route::get('navigations-data', 'getNavigationsData');
    Route::get('page-data/{code}', 'getPageData');
    Route::get('blogs', 'blogs');
    Route::get('blog-details/{id}', 'blogDetails');
    Route::post('subscribe-now', SubscriberController::class);
    Route::post('submit-contact-form', 'contactForm');
});
