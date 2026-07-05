<?php

use App\Http\Controllers\Api\AddMoneyController;
use App\Http\Controllers\Api\Agent\DashboardController;
use App\Http\Controllers\Api\Agent\StatisticsController;
use App\Http\Controllers\Api\CashInController;
use App\Http\Controllers\Api\DashboardController as UserDashboardController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WithdrawAccountController;
use App\Http\Controllers\Api\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum', 'api_role:agent', 'account_status_checker')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'dashboard']);
    Route::controller(UserDashboardController::class)->group(function () {
        Route::get('mark-as-read-notification', 'markNotification');
        Route::get('qrcode', 'qrCode');
        Route::get('transactions', 'transactions');
    });

    // Statistics
    Route::controller(StatisticsController::class)->group(function () {
        Route::get('circle-charts', 'circleCharts');
        Route::get('transaction-chart', 'activityChartInfo');
    });

    // Exchange Routes
    Route::prefix('exchange')->controller(ExchangeController::class)->group(function () {
        Route::get('config', 'config');
        Route::post('/', 'store');
        Route::get('history', 'history');
    });

    // Wallets
    Route::apiResource('wallets', WalletController::class)->only('index', 'store', 'destroy');

    // Add Money
    Route::apiResource('add-money', AddMoneyController::class)->only('index', 'store');

    // Cashin Routes
    Route::prefix('cashin')->group(function () {
        Route::get('config', [CashInController::class, 'config']);
        Route::post('/', [CashInController::class, 'store']);
        Route::get('history', [CashInController::class, 'history']);
    });

    // Withdraw Account Routes
    Route::prefix('withdraw-accounts')->group(function () {
        Route::get('config', [WithdrawAccountController::class, 'config']);
        Route::get('methods/list', [WithdrawAccountController::class, 'getWithdrawMethods']);
        Route::get('methods/{id}/details', [WithdrawAccountController::class, 'getMethodDetails']);
    });

    Route::apiResource('withdraw-accounts', WithdrawAccountController::class);

    // Withdraw Routes
    Route::post('withdraw', WithdrawController::class);

    // Settings
    Route::prefix('settings')->controller(SettingsController::class)->group(function () {
        Route::post('profile', 'profileUpdate');
        Route::post('2fa/{type}', 'twoFa');
        Route::post('passcode/verify', 'verifyPasscode');
        Route::post('account-close', 'accountClose');
        Route::post('change-password', 'updatePassword');
    });

    // KYC
    Route::get('kyc/history', [KycController::class, 'histories']);
    Route::get('kyc/rejected-data', [KycController::class, 'rejectedData']);
    Route::apiResource('kyc', KycController::class)->only('index', 'store', 'show');

    // Ticket
    Route::get('ticket/config', [TicketController::class, 'config']);
    Route::apiResource('ticket', TicketController::class)->except('update', 'destroy');
    Route::post('ticket/reply/{uuid}', [TicketController::class, 'reply']);
    Route::post('ticket/action/{uuid}', [TicketController::class, 'action']);
});
