<?php

use App\Http\Controllers\Api\AddMoneyController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\CashoutController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\GiftController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\PasscodeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentLinkController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\RequestMoneyController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TransferMoneyController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WithdrawAccountController;
use App\Http\Controllers\Api\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum', 'api_role:user', 'account_status_checker')->group(function () {
    // Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'dashboard');
        Route::get('statistics', 'statistics');
        Route::get('qrcode', 'qrCode');
        Route::get('activity-chart', 'activityChartInfo');

        // Transactions
        Route::get('transactions', 'transactions');
        Route::get('transaction/{tnx?}', 'transactionDetail');
    });

    // Wallets
    Route::apiResource('wallets', WalletController::class)->only('index', 'store', 'destroy');

    // Add Money
    Route::apiResource('add-money', AddMoneyController::class)->only('index', 'store');

    // Payment
    Route::controller(PaymentController::class)->prefix('payment')->group(function () {
        Route::get('settings', 'index');
        Route::post('make', 'store');
        Route::get('history', 'history');
    });

    // Pay Bill
    Route::post('pay-bill', [BillController::class, 'payNow']);
    Route::get('pay-bill/history', [BillController::class, 'history']);
    Route::get('pay-bill/services/{country}/{type}', [BillController::class, 'getServices']);

    // Passcode
    Route::controller(PasscodeController::class)->prefix('passcode')->group(function () {
        Route::post('/', 'passcode');
        Route::post('change', 'changePasscode');
        Route::post('disable', 'disablePasscode');
        Route::post('verify', 'verifyPasscode');
    });

    // Additional invoice routes
    Route::prefix('invoices')->group(function () {
        Route::get('config', [InvoiceController::class, 'config']);
    });

    // Invoice resource routes
    Route::apiResource('invoices', InvoiceController::class);

    // Payment Links Routes
    Route::prefix('payment-links')->group(function () {
        Route::post('/create', [PaymentLinkController::class, 'create']);
        Route::get('history', [PaymentLinkController::class, 'history']);
    });

    // Request Money Routes
    Route::prefix('request-money')->group(function () {
        Route::get('config', [RequestMoneyController::class, 'config']);
        Route::post('/', [RequestMoneyController::class, 'store']);
        Route::get('history', [RequestMoneyController::class, 'history']);
        Route::post('{id}/action', [RequestMoneyController::class, 'action']);
    });

    // Gift Routes
    Route::prefix('gifts')->group(function () {
        Route::get('config', [GiftController::class, 'config']);
        Route::post('/', [GiftController::class, 'store']);
        Route::post('redeem', [GiftController::class, 'redeem']);
        Route::get('history', [GiftController::class, 'history']);
        Route::get('redeem/history', [GiftController::class, 'redeemHistory']);
    });

    // Transfer Money Routes
    Route::prefix('transfer')->group(function () {
        Route::get('config', [TransferMoneyController::class, 'config']);
        Route::post('/', [TransferMoneyController::class, 'store']);
        Route::get('history', [TransferMoneyController::class, 'history']);
    });

    // Beneficiary Routes
    Route::apiResource('beneficiaries', BeneficiaryController::class);

    // Cashout Routes
    Route::prefix('cashout')->group(function () {
        Route::get('config', [CashoutController::class, 'config']);
        Route::post('/', [CashoutController::class, 'store']);
        Route::get('history', [CashoutController::class, 'history']);
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

    // Exchange Routes
    Route::prefix('exchange')->group(function () {
        Route::get('config', [ExchangeController::class, 'config']);
        Route::post('/', [ExchangeController::class, 'store']);
        Route::get('history', [ExchangeController::class, 'history']);
    });

    // Referral
    Route::prefix('referral')->controller(ReferralController::class)->group(function () {
        Route::get('info', 'index');
        Route::get('direct', 'directReferrals');
        Route::get('tree', 'referralTree');
    });

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
    Route::apiResource('kyc', KycController::class)->only('index', 'store')->withoutMiddleware('account_status_checker');

    // Ticket
    Route::get('ticket/config', [TicketController::class, 'config']);
    Route::apiResource('ticket', TicketController::class)->except('update', 'destroy');
    Route::post('ticket/reply/{uuid}', [TicketController::class, 'reply']);
    Route::post('ticket/action/{uuid}', [TicketController::class, 'action']);
});
