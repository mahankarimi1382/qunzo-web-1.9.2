<?php

use App\Http\Controllers\Auth\OtpVerifyController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\ForgetPasswordController;
use Illuminate\Support\Facades\Route;

// Verify OTP
Route::get('verify/otp', [OtpVerifyController::class, 'index'])->name('otp.verify');
Route::get('resend/otp', [OtpVerifyController::class, 'resend'])->name('otp.resend');
Route::post('/verify', [OtpVerifyController::class, 'verify'])->name('otp.verify.post');

// ================================ Admin Auth Section ================================
Route::group(['prefix' => setting('site_admin_prefix', 'global'), 'as' => 'admin.', 'middleware' => ['guest:admin']], function () {
    Route::get('login', [AuthController::class, 'loginView'])->name('login-view');
    Route::post('login', [AuthController::class, 'authenticate'])->name('login');
    // Forget Password
    Route::get('forget-password', [ForgetPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.now');
    Route::post('forget-password', [ForgetPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.submit');
    Route::get('reset-password/{token}', [ForgetPasswordController::class, 'showResetPasswordForm'])->name('reset.password.now');
    Route::post('reset-password', [ForgetPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.submit');
});

Route::middleware(['auth:admin'])->name('admin.')->prefix(setting('site_admin_prefix', 'global'))->group(function () {
    Route::get('two-fa-verify', [OtpVerifyController::class, 'adminVerify'])->name('two.fa.verify');
    Route::post('two-fa-verify', [OtpVerifyController::class, 'adminVerifyPost'])->name('two.fa.verify.post');
});
