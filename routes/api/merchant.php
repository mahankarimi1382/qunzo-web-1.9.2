<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\Merchant\ApiAccessController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Payment\SandboxPaymentController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WithdrawAccountController;
use App\Http\Controllers\Api\WithdrawController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Merchant Dashboard API
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum', 'api_role:merchant', 'account_status_checker')->group(function () {
    // Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('mark-as-read-notification', 'markNotification');
        Route::get('qrcode', 'qrCode');
        Route::get('transactions', 'transactions');
        Route::get('activity-chart', 'merchantActivityChartInfo');
        Route::get('circle-chart', 'circleChartInfo');
    });

    // Wallets
    Route::apiResource('wallets', WalletController::class)->only('index', 'store', 'destroy');

    // API Access token
    Route::prefix('access-keys')->group(function () {
        Route::get('/', [ApiAccessController::class, 'getAccessKeys']);
        Route::post('regenerate', [ApiAccessController::class, 'regenerateAccessKey']);
    });

    // Exchange
    Route::prefix('exchange')->controller(ExchangeController::class)->group(function () {
        Route::get('config', 'config');
        Route::post('/', 'store');
        Route::get('history', 'history');
    });

    // Invoice
    Route::get('invoices/config', [InvoiceController::class, 'config']);
    Route::apiResource('invoices', InvoiceController::class);

    // Withdraw & Accounts
    Route::prefix('withdraw-accounts')->group(function () {
        Route::get('config', [WithdrawAccountController::class, 'config']);
        Route::get('methods/list', [WithdrawAccountController::class, 'getWithdrawMethods']);
        Route::get('methods/{id}/details', [WithdrawAccountController::class, 'getMethodDetails']);
    });

    Route::apiResource('withdraw-accounts', WithdrawAccountController::class);
    Route::post('withdraw', WithdrawController::class);

    // KYC
    Route::get('kyc/history', [KycController::class, 'histories']);
    Route::get('kyc/rejected-data', [KycController::class, 'rejectedData']);
    Route::apiResource('kyc', KycController::class)->only('index', 'store', 'show');

    // Ticket
    Route::get('ticket/config', [TicketController::class, 'config']);
    Route::apiResource('ticket', TicketController::class)->except('update', 'destroy');
    Route::post('ticket/reply/{uuid}', [TicketController::class, 'reply']);
    Route::post('ticket/action/{uuid}', [TicketController::class, 'action']);

    // Settings
    Route::prefix('settings')->controller(SettingsController::class)->group(function () {
        Route::get('profile', 'profile');
        Route::post('profile', 'profileUpdate');
        Route::post('2fa/{type}', 'twoFa');
        Route::post('change-password', 'updatePassword');
    });
});

/*
|--------------------------------------------------------------------------
| Merchant Payment API
|--------------------------------------------------------------------------
|
*/

Route::withoutMiddleware(['auth:sanctum'])->group(function () {
    // Real payment
    Route::post('/access-token', [PaymentController::class, 'getAccessToken']);
    Route::post('/make-payment', [PaymentController::class, 'makePayment']);

    // Sandbox payment
    Route::prefix('sandbox')->group(function () {
        Route::post('/access-token', [SandboxPaymentController::class, 'getAccessToken']);
        Route::post('/make-payment', [SandboxPaymentController::class, 'makePayment']);
    });
});
