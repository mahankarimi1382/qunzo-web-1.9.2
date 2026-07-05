<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    abort(404);
});

// Contact Form
Route::post('mail-send', [PageController::class, 'mailSend'])->name('mail-send');
Route::post('subscriber', [HomeController::class, 'subscribeNow'])->name('subscriber');

// Gateway status
Route::group(['controller' => StatusController::class, 'prefix' => 'status', 'as' => 'status.'], function () {
    Route::match(['get', 'post'], '/success', 'success')->name('success');
    Route::match(['get', 'post'], '/cancel', 'cancel')->name('cancel');
});

Route::get('non-hosted-gateway/{gateway}/{tnx}', [HomeController::class, 'nonHostedGateway'])->name('non-hosted-gateway');

// Own Payment Success
Route::get('payment/success/{reftrn}', [StatusController::class, 'ownSuccess'])->name('payment.success');

// Translate
Route::get('language-update/{locale}', [HomeController::class, 'languageUpdate'])->name('language-update');

// Without auth
Route::get('notification-tune', [AppController::class, 'notificationTune'])->name('notification-tune');

// Site cron job
Route::get('site-cron', [CronJobController::class, 'runCronJobs'])->name('cron.job');

// Auth
require __DIR__.'/auth.php';
