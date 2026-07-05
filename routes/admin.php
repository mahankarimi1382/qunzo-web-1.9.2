<?php

use App\Http\Controllers\Backend\AddonController;
use App\Http\Controllers\Backend\Agent\AgentListController;
use App\Http\Controllers\Backend\AppController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\BillController;
use App\Http\Controllers\Backend\BillServiceController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\CronJobController;
use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\CustomCssController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DepositController;
use App\Http\Controllers\Backend\GatewayController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\Merchant\ListController;
use App\Http\Controllers\Backend\NavigationController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\PluginController;
use App\Http\Controllers\Backend\ReferralController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SocialController;
use App\Http\Controllers\Backend\StaffController;
use App\Http\Controllers\Backend\TemplateController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\ThemeController;
use App\Http\Controllers\Backend\TicketController;
use App\Http\Controllers\Backend\TransactionController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\P2pAdsController;
use App\Http\Controllers\Backend\P2pOrderController;
use App\Http\Controllers\Backend\P2pOrderMessageController;
use App\Http\Controllers\Backend\P2pPaymentMethodController;
use App\Http\Controllers\Backend\VerificationController;
use App\Http\Controllers\Backend\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
// Users
Route::resource('/user', UserController::class)->only('index', 'edit', 'update');
Route::group(['prefix' => 'user', 'as' => 'user.', 'controller' => UserController::class], function () {
    Route::get('active', 'activeUser')->name('active');
    Route::get('closed', 'closed')->name('closed');
    Route::get('disabled', 'disabled')->name('disabled');
    Route::get('login/{id}', 'userLogin')->name('login');
    Route::post('status-update/{id}', 'statusUpdate')->name('status-update');
    Route::post('password-update/{id}', 'passwordUpdate')->name('password-update');
    Route::get('destroy/{id}', 'destroy')->name('destroy');
    Route::post('balance-update/{id}', 'balanceUpdate')->name('balance-update');
    Route::get('status/{card:card_id}', 'updateCardStatus')->name('card.status.update');
    Route::put('card/balance/topup/{card}', 'cardBalanceUpdate')->name('card.balance.update');
});

// Merchant List
Route::controller(ListController::class)->middleware('merchant_system')->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('list/{status?}', 'index')->name('index');
    Route::get('edit/{id}', 'edit')->name('edit');
});

// Agent List
Route::controller(AgentListController::class)->middleware('agent_system')->prefix('agent')->name('agent.')->group(function () {
    Route::get('list/{status?}', 'index')->name('index');
    Route::get('edit/{id}', 'edit')->name('edit');
});

//  Verification Center
Route::resource('verification-form', VerificationController::class);
Route::group(['prefix' => 'verification', 'as' => 'verification.', 'controller' => VerificationController::class], function () {
    Route::get('pending', 'pending')->name('pending');
    Route::get('rejected', 'rejected')->name('rejected');
    Route::get('trader-applications', 'traderApplications')->name('trader-applications');
    Route::get('action/{id}', 'verificationData')->name('action');
    Route::post('action-now', 'actionNow')->name('action.now')->withoutMiddleware('isDemo');
    Route::get('all', 'all')->name('all');
});

//  Currency Management
Route::controller(CurrencyController::class)->group(function () {
    Route::get('currency', 'index')->name('currency.index');
    Route::get('currency/create', 'create')->name('currency.create');
    Route::post('currency', 'store')->name('currency.store');
    Route::get('currency/{id}/edit', 'edit')->name('currency.edit');
    Route::post('currency/{id}/update', 'update')->name('currency.update');
    Route::post('currency/{id}/delete', 'delete')->name('currency.delete');
});

//  P2P Payment Methods
Route::group(['prefix' => 'p2p', 'as' => 'p2p.'], function () {
    Route::get('payment-method', [P2pPaymentMethodController::class, 'index'])->name('payment-method.index');
    Route::get('payment-method/create', [P2pPaymentMethodController::class, 'create'])->name('payment-method.create');
    Route::post('payment-method', [P2pPaymentMethodController::class, 'store'])->name('payment-method.store');
    Route::get('payment-method/{id}/edit', [P2pPaymentMethodController::class, 'edit'])->name('payment-method.edit');
    Route::post('payment-method/{id}/update', [P2pPaymentMethodController::class, 'update'])->name('payment-method.update');
    Route::post('payment-method/{id}/delete', [P2pPaymentMethodController::class, 'delete'])->name('payment-method.delete');
    
    // P2P Ads
    Route::get('ads', [P2pAdsController::class, 'index'])->name('ads.index');
    Route::get('ads/pending', [P2pAdsController::class, 'pending'])->name('ads.pending');
    Route::get('ads/{id}/action', [P2pAdsController::class, 'action'])->name('ads.action');
    Route::post('ads/action', [P2pAdsController::class, 'actionNow'])->name('ads.action.now');

    // P2P Orders
    Route::get('orders', [P2pOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [P2pOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{id}/resolve', [P2pOrderController::class, 'resolve'])->name('orders.resolve');
    Route::get('orders/{id}/messages', [P2pOrderMessageController::class, 'index'])->name('orders.messages.index');
    Route::post('orders/{id}/messages', [P2pOrderMessageController::class, 'store'])->name('orders.messages.store');
});

//   Role Management
Route::resource('roles', RoleController::class)->except('show', 'destroy');
Route::resource('staff', StaffController::class)->except('show', 'destroy', 'create');
//   Transactions
Route::controller(TransactionController::class)->group(function () {
    Route::get('transactions', 'transactions')->name('transactions');
    Route::get('transactions/export/csv', 'exportCsv')->name('transactions.export.csv');
    Route::get('admin-profits', 'adminProfits')->name('profits');
    Route::get('virtual-cards', 'virtualCardsList')->name('virtual.cards');
});

//   Essentials
Route::group(['prefix' => 'gateway', 'as' => 'gateway.', 'controller' => GatewayController::class], function () {
    Route::get('automatic', 'automatic')->name('automatic');
    Route::post('update/{id}', 'update')->name('update');
    Route::get('currency/{gateway_id}', 'gatewayCurrency')->name('supported.currency');
});
Route::group(['prefix' => 'deposit', 'as' => 'deposit.', 'controller' => DepositController::class], function () {
    //  deposit Method
    Route::group(['prefix' => 'method', 'as' => 'method.'], function () {
        Route::get('list/{type}', 'methodList')->name('list');
        Route::get('create/{type}', 'createMethod')->name('create');
        Route::post('store', 'methodStore')->name('store');
        Route::get('edit/{type}', 'methodEdit')->name('edit');
        Route::post('update/{id}', 'methodUpdate')->name('update');
    });
    Route::get('manual-pending', 'pending')->name('manual.pending');
    Route::get('history', 'history')->name('history');
    Route::get('action/{id}', 'depositAction')->name('action');
    Route::post('action-now', 'actionNow')->name('action.now');
});
Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.', 'controller' => WithdrawController::class], function () {
    // withdraw
    Route::group(['prefix' => 'method', 'as' => 'method.'], function () {
        Route::get('list/{type}', 'methods')->name('list');
        Route::get('create/{type}', 'methodCreate')->name('create');
        Route::post('store', 'methodStore')->name('store');
        Route::get('edit/{type}', 'methodEdit')->name('edit');
        Route::post('update/{id}', 'methodUpdate')->name('update');
    });
    // Schedule
    Route::get('schedule', 'schedule')->name('schedule');
    Route::post('schedule-update', 'scheduleUpdate')->name('schedule.update');
    // status & action
    Route::get('history', 'history')->name('history');
    Route::get('pending', 'pending')->name('pending');
    Route::get('action/{id}', 'withdrawAction')->name('action');
    Route::post('action-now', 'actionNow')->name('action.now');
});

Route::group(['prefix' => 'referral', 'as' => 'referral.', 'controller' => ReferralController::class], function () {
    // referral
    Route::get('settings', 'settings')->name('settings');
    Route::get('index', 'index')->name('index');
    Route::post('store', 'store')->name('store');
    Route::post('update/{id}', 'update')->name('update');
    Route::post('delete/{id}', 'destroy')->name('delete');
    Route::post('level-status', 'statusUpdate')->name('status');
});
// Bill Management
Route::group(['prefix' => 'bill', 'as' => 'bill.'], function () {

    // Import Services
    Route::get('import-services', [BillServiceController::class, 'import'])->name('import.services');
    // Convert Rate
    Route::get('convert-rate', [BillServiceController::class, 'convertRate'])->name('convert.rate');
    Route::post('save-rate', [BillServiceController::class, 'saveRate'])->name('save.rate');
    // Get Categories
    Route::get('get-categories/{method}', [BillServiceController::class, 'getCategories'])->name('get.categories');
    // Get Operators
    Route::get('get-operators/{method}/{category}', [BillServiceController::class, 'getOperators'])->name('get.operators');

    // Bill Service Management
    Route::group(['prefix' => 'service', 'as' => 'service.', 'controller' => BillServiceController::class], function () {
        Route::get('/index', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/bulk-store', 'bulkStore')->name('bulk.store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    });

    // Bill History Management
    Route::group(['prefix' => 'history', 'as' => 'history.', 'controller' => BillController::class], function () {
        Route::get('/pending', 'pending')->name('pending');
        Route::get('/complete', 'complete')->name('complete');
        Route::get('/returned', 'returned')->name('returned');
        Route::get('/all', 'all')->name('all');
    });
});

Route::get('footer-content', [PageController::class, 'footerContent'])->name('footer-content');
Route::group(['prefix' => 'social', 'as' => 'social.', 'controller' => SocialController::class], function () {
    Route::post('store', 'store')->name('store');
    Route::post('update', 'update')->name('update');
    Route::post('delete', 'delete')->name('delete');
    Route::post('position-update', 'positionUpdate')->name('position.update');
});
// theme
Route::group(['prefix' => 'theme', 'as' => 'theme.', 'controller' => ThemeController::class], function () {
    Route::get('site', 'siteTheme')->name('site');
    Route::get('status-update', 'statusUpdate')->name('status-update');
});
// navigation
Route::group(['prefix' => 'navigation', 'as' => 'navigation.', 'controller' => NavigationController::class], function () {
    Route::get('menu', 'index')->name('menu');
    Route::post('menu-add', 'store')->name('menu.add');
    Route::get('menu-edit/{id}', 'edit')->name('menu.edit');
    Route::post('menu-update', 'update')->name('menu.update');
    Route::post('menu-delete', 'delete')->name('menu.delete');
    Route::get('menu-delete/{id}/{type}', 'typeDelete')->name('menu.type.delete');
    Route::post('menu-position-update', 'positionUpdate')->name('position.update');
    Route::get('header', 'header')->name('header');
    Route::get('footer', 'footer')->name('footer');
    Route::get('translate/{id}', 'translate')->name('translate');
    Route::post('translate', 'translateNow')->name('translate.now');
    Route::get('megamenu/{id}', 'megamenu')->name('megamenu');
    Route::post('megamenu-item-store', 'megamenuItemStore')->name('megamenu.item.store');
    Route::post('megamenu-item-update', 'megamenuItemUpdate')->name('megamenu.item.update');
    Route::post('megamenu-item-delete', 'megamenuItemDelete')->name('megamenu.item.delete');
    Route::post('megamenu-item-position-update', 'megamenuItemPositionUpdate')->name('megamenu.item.position.update');
});

// Pages
Route::group(['prefix' => 'page', 'as' => 'page.', 'controller' => PageController::class], function () {
    Route::get('edit/{name}', 'edit')->name('edit');
    Route::get('create', 'create')->name('create');
    Route::post('store', 'store')->name('store');
    Route::post('update', 'update')->name('update');
    Route::post('delete/now', 'deleteNow')->name('delete.now');

    Route::get('section/{section}', 'landingSection')->name('section.section');
    Route::post('section/update', 'landingSectionUpdate')->name('section.section.update');
    Route::post('content-store', 'contentStore')->name('content-store');
    Route::get('content-edit/{id}', 'contentEdit')->name('content-edit');
    Route::post('content-update', 'contentUpdate')->name('content-update');
    Route::post('content-delete', 'contentDelete')->name('content-delete');
    Route::get('landing-section-management', 'management')->name('section.management');
    Route::post('landing-section-update', 'managementUpdate')->name('section.management.update');
    Route::resource('blog', BlogController::class)->except('show');
    Route::group(['prefix' => 'testimonial', 'as' => 'testimonial.', 'controller' => TestimonialController::class], function () {
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('delete', 'destroy')->name('delete');
    });
    Route::get('settings', 'pageSetting')->name('setting');
    Route::post('setting-update', 'pageSettingUpdate')->name('setting.update');
});

// Pages
Route::group(['prefix' => 'social', 'as' => 'social.', 'controller' => SocialController::class], function () {
    Route::post('store', 'store')->name('store');
    Route::post('update', 'update')->name('update');
    Route::post('delete', 'delete')->name('delete');
    Route::post('position-update', 'positionUpdate')->name('position.update');
});

// Site Settings
Route::group(['prefix' => 'settings', 'as' => 'settings.', 'controller' => SettingController::class], function () {
    Route::get('general', 'siteSetting')->name('site');
    Route::get('transaction-fees', 'transactionSettings')->name('transactions');
    Route::get('seo-meta', 'seoMeta')->name('seo.meta');

    Route::get('plugin/{name}', [PluginController::class, 'plugin'])->name('plugin');
    Route::get('plugin-data/{id}', [PluginController::class, 'pluginData'])->name('plugin.data');
    Route::post('plugin-update/{id}', [PluginController::class, 'update'])->name('plugin.update');

    Route::get('mail', 'mailSetting')->name('mail');
    Route::post('mail-connection-test', 'mailConnectionTest')->name('mail.connection.test');
    Route::post('update', 'update')->name('update');
    Route::post('referral/rules', 'updateReferralRules')->name('referral.rules');

    // Notification tune
    Route::group(['prefix' => 'notification', 'as' => 'notification.', 'controller' => NotificationController::class], function () {
        Route::get('tune', 'setTune')->name('tune');
        Route::get('tune/status/{id}', 'status')->name('tune.status');
    });
});

// Addons Management
Route::group(['prefix' => 'addons', 'as' => 'addons.', 'controller' => AddonController::class], function () {
    Route::get('/', 'index')->name('index');
    Route::post('upload', 'upload')->name('upload');
    Route::post('{slug}/activate', 'activate')->name('activate');
    Route::post('{slug}/deactivate', 'deactivate')->name('deactivate');
    Route::post('{slug}/delete', 'destroy')->name('delete');
});

// Templates
Route::controller(TemplateController::class)->prefix('template')->name('template.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('edit/{id}', 'edit')->name('edit');
    Route::put('update/{id}', 'update')->name('update');
    Route::get('preview/{id}', 'preview')->name('preview');
});

// Notifications
Route::get('notification/all', [NotificationController::class, 'all'])->name('notification.all');
Route::get('latest-notification', [NotificationController::class, 'latestNotification'])->name('latest-notification');
Route::get('notification-read/{id}', [NotificationController::class, 'readNotification'])->name('read-notification');
Route::get('mail-send/all', [NotificationController::class, 'mailSendAll'])->name('mail.send.all');
Route::post('mail-send-now', [NotificationController::class, 'mailSend'])->name('mail.send.now');

// Languages
Route::resource('language', LanguageController::class);
Route::get('language-keyword/{language}', [LanguageController::class, 'languageKeyword'])->name('language-keyword');
Route::post('language-keyword-update', [LanguageController::class, 'keywordUpdate'])->name('language-keyword-update');
Route::get('language-sync-missing', [LanguageController::class, 'syncMissing'])->name('language-sync-missing');
//   Others
Route::group(['controller' => AppController::class], function () {
    Route::get('subscribers', 'subscribers')->name('subscriber');
    Route::get('mail-send-subscriber', 'mailSendSubscriber')->name('mail.send.subscriber');
    Route::post('mail-send-subscriber-now', 'mailSendSubscriberNow')->name('mail.send.subscriber.now');
});
// Ticket
Route::group(['prefix' => 'support-ticket', 'as' => 'ticket.', 'controller' => TicketController::class], function () {
    Route::get('index/{id?}', 'index')->name('index');
    Route::post('reply', 'reply')->name('reply');
    Route::get('show/{uuid}', 'show')->name('show');
    Route::get('close-now/{uuid}', 'closeNow')->name('close.now');
});
// CronJob
Route::controller(CronJobController::class)->as('cron.jobs.')->prefix('cron-jobs')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('store', 'store')->name('store');
    Route::post('update/{id}', 'update')->name('update');
    Route::post('delete/{id}', 'delete')->name('delete');
    Route::get('run-now/{id}', 'runNow')->name('run.now');
    Route::get('logs/{id}', 'logs')->name('logs');
    Route::get('clear-logs/{id}', 'clearLogs')->name('clear.logs');
});
// Custom Css
Route::get('custom-css', [CustomCssController::class, 'customCss'])->name('custom-css');
Route::post('custom-css-update', [CustomCssController::class, 'customCssUpdate'])->name('custom-css.update');
// app
Route::get('profile', [AppController::class, 'profile'])->name('profile');
Route::post('profile-update', [AppController::class, 'profileUpdate'])->name('profile-update');
Route::get('password-change', [AppController::class, 'passwordChange'])->name('password-change');
Route::post('password-update', [AppController::class, 'passwordUpdate'])->name('password-update');
Route::get('security-settings', [AppController::class, 'securitySettings'])->name('security-settings');
Route::post('security-settings-update', [AppController::class, 'securitySettingsUpdate'])->name('security.settings.update');

Route::get('application-info', [AppController::class, 'applicationInfo'])->name('application-info');
Route::get('clear-cache', [AppController::class, 'clearCache'])->name('clear-cache');
// logout
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->withoutMiddleware('isDemo');
