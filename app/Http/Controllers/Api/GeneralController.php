<?php

namespace App\Http\Controllers\Api;

use App\Enums\KYCStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Http\Resources\NavigationResource;
use App\Http\Resources\NotificationResource;
use App\Models\BillService;
use App\Models\Currency;
use App\Models\Notification;
use App\Models\PageSetting;
use App\Models\Plugin;
use App\Models\Setting;
use App\Models\UserNavigation;
use App\Models\WithdrawMethod;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    use ApiResponseTrait;

    public function getNotificationTypes($type)
    {
        $templates = \App\Models\Template::where('for', $type)->pluck('code');

        $generalTemplates = [
            'withdraw_approved',
            'withdraw_rejected',
            'user_manual_deposit_approved',
            'user_manual_deposit_rejected',
        ];

        $allTemplates = $type !== 'user' ? array_merge($templates->toArray(), $generalTemplates) : $templates->toArray();

        return response()->json([
            'status' => true,
            'data' => $allTemplates,
        ]);
    }

    public function getCardProviders()
    {
        $providers = Plugin::active()
            ->where('type', 'virtual_card_provider')
            ->select('id', 'name')
            ->get()
            ->map(function ($provider) {
                $provider->code = match ($provider->name) {
                    'Stripe Virtual Card' => 'stripe',
                    'Strowallet Virtual Card' => 'strowallet',
                    default => str($provider->name)->slug()->replace('-', '_')->toString(),
                };

                return $provider;
            });

        return response()->json([
            'status' => true,
            'data' => $providers,
        ]);
    }

    public function getBillCountries($type)
    {
        $countries = BillService::where('type', $type)->pluck('country')->unique()->values();

        return response()->json([
            'status' => true,
            'data' => $countries,
        ]);
    }

    public function getPasscodeStatus()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'deposit_passcode_status' => setting('deposit_passcode_status', 'passcode'),
                'transfer_money_passcode_status' => setting('transfer_money_passcode_status', 'passcode'),
                'make_payment_passcode_status' => setting('make_payment_passcode_status', 'passcode'),
                'gift_passcode_status' => setting('gift_passcode_status', 'passcode'),
                'withdraw_passcode_status' => setting('withdraw_passcode_status', 'passcode'),
                'cashout_passcode_status' => setting('cashout_passcode_status', 'passcode'),
                'request_money_accept_passcode_status' => setting('request_money_accept_passcode_status', 'passcode'),
                'invoice_passcode_status' => setting('invoice_passcode_status', 'passcode'),
                'exchange_passcode_status' => setting('exchange_passcode_status', 'passcode'),
            ],
        ]);
    }

    public function getCountries()
    {
        $location = getLocation();

        return response()->json([
            'status' => true,
            'data' => collect(getCountries())->map(function ($country) use ($location) {
                $country['selected'] = $country['code'] == $location->country_code;

                return $country;
            }),
        ]);
    }

    public function getCurrencies(Request $request)
    {
        $multiCurrencyEnabled = setting('multiple_currency', 'permission');
        if (! $multiCurrencyEnabled) {
            return response()->json([
                'status' => false,
                'message' => 'Multiple currency is disabled',
                'data' => [],
            ]);
        }

        $currencies = Currency::when($request->get('type'), function ($query) use ($request) {
            $query->where('type', $request->get('type'));
        })->get();

        $currencies->transform(function ($currency) {
            $currency->full_name = $currency->name . ' (' . $currency->code . ')';
            $currency->icon = asset($currency->icon);

            return $currency;
        });

        return response()->json([
            'status' => true,
            'data' => $currencies->toArray(),
        ]);
    }

    public function getSettings(Request $request)
    {
        $type = $request->get('key', 'all');
        $settings = Setting::select('name', 'val')->get()->map(function ($setting) {
            return [
                'name' => $setting->name,
                'value' => file_exists(public_path($setting->val)) && is_file(public_path($setting->val)) ? asset($setting->val) : $setting->val,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $type == 'all' ? $settings : data_get(collect($settings)->firstWhere('name', $type), 'value'),
        ]);
    }

    public function getLanguages()
    {
        $languages = \App\Models\Language::where('status', 1)->get();

        return response()->json([
            'status' => true,
            'data' => $languages->toArray(),
        ]);
    }

    public function getRegisterFields($type)
    {
        $registerFields = PageSetting::select('key', 'value')->when($type == 'merchant' || $type == 'agent', function ($query) use ($type) {
            $query->whereLike('key', $type . '_%');
        })->when($type == 'user', function ($query) {
            $query->whereNotLike('key', 'merchant_%')->whereNotLike('key', 'agent_%');
        })->whereNotIn('key', ['shape_one', 'shape_two', 'shape_three', 'basic_page_background', 'breadcrumb'])->get();

        return response()->json([
            'status' => true,
            'data' => $registerFields,
        ]);
    }

    public function getKycStatus()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'status' => [
                    'not_submitted' => KYCStatus::NOT_SUBMITTED->value,
                    'pending' => KYCStatus::Pending->value,
                    'verified' => KYCStatus::Verified->value,
                    'failed' => KYCStatus::Failed->value,
                ],
            ],
        ]);
    }

    public function getTransactionTypesAndStatuses()
    {
        // User transaction types
        $userTransactionTypes = collect([
            TxnType::Deposit,
            TxnType::ManualDeposit,
            TxnType::Withdraw,
            TxnType::WithdrawAuto,
            TxnType::SendMoney,
            TxnType::ReceiveMoney,
            TxnType::CashReceived,
            TxnType::Refund,
            TxnType::Referral,
            TxnType::Exchange,
            TxnType::GiftRedeemed,
            TxnType::SignupBonus,
            TxnType::CashIn,
            TxnType::CashOut,
            TxnType::Credit,
            TxnType::Debit,
            TxnType::RequestMoney,
            TxnType::Invoice,
            TxnType::PayBill,
        ])->map(function ($txnType) {
            return [
                'name' => str($txnType->value)->headline(),
                'value' => $txnType->value,
            ];
        });

        // Agent transaction types
        $agentTransactionTypes = collect([
            TxnType::Deposit,
            TxnType::ManualDeposit,
            TxnType::CashoutCommission,
            TxnType::CashInCommission,
            TxnType::Credit,
            TxnType::Debit,
            TxnType::Refund,
            TxnType::SignupBonus,
            TxnType::CashReceived,
            TxnType::CashIn,
            TxnType::CashOut,
            TxnType::Withdraw,
            TxnType::WithdrawAuto,
            TxnType::Exchange,
        ])->map(function ($txnType) {
            return [
                'name' => str($txnType->value)->headline(),
                'value' => $txnType->value,
            ];
        });

        // Merchant transaction types
        $merchantTransactionTypes = collect([
            TxnType::Payment,
            TxnType::Credit,
            TxnType::Debit,
            TxnType::Refund,
            TxnType::SignupBonus,
            TxnType::Withdraw,
            TxnType::WithdrawAuto,
            TxnType::Deposit,
            TxnType::ManualDeposit,
            TxnType::Exchange,
            TxnType::Invoice,
        ])->map(function ($txnType) {
            return [
                'name' => str($txnType->value)->headline(),
                'value' => $txnType->value,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'user_types' => $userTransactionTypes,
                'agent_types' => $agentTransactionTypes,
                'merchant_types' => $merchantTransactionTypes,
                'statuses' => collect(TxnStatus::cases())->map(function ($txnStatus) {
                    return [
                        'name' => str($txnStatus->value)->headline(),
                        'value' => $txnStatus->value,
                    ];
                }),
            ],
        ]);
    }

    public function getWithdrawMethods()
    {
        $methods = WithdrawMethod::where('status', 1)->get()->map(function ($method) {
            $method->fields = json_encode((object) json_decode($method->fields, true));

            return $method;
        });

        return response()->json([
            'status' => true,
            'data' => $methods,
        ]);
    }

    public function getOnboardingScreenImages()
    {
        return response()->json([
            'status' => true,
            'data' => [
                asset(getPageSetting('app_splash_one_image')),
                asset(getPageSetting('app_splash_two_image')),
                asset(getPageSetting('app_splash_three_image')),
                asset(getPageSetting('app_splash_four_image')),
            ],
        ]);
    }

    public function getNavigation()
    {
        $user_navigations = UserNavigation::orderBy('position')->get();

        return response()->json([
            'status' => true,
            'data' => NavigationResource::collection($user_navigations),
        ]);
    }

    public function getPlugins()
    {
        $plugins = Plugin::where('status', 1)->get();

        return response()->json([
            'status' => true,
            'data' => $plugins,
        ]);
    }

    public function getNotifications(Request $request)
    {
        $query = Notification::whereNot('for', 'admin')->where('user_id', $request->user()->id);

        $notifications = $query->clone()->latest()->paginate($request->integer('per_page', 15));

        $data = [
            'notifications' => NotificationResource::collection($notifications),
            'unread_count' => $query->clone()->where('read', 0)->count(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ];

        return $this->success($data, 'Notifications');
    }

    public function convertCurrency($amount, $currencyCode, $thousandSeparatorRemove = 'true', $fromCurrency = null)
    {
        $fromCurrency = $fromCurrency ?? setting('site_currency');
        $converted = currency()->convert($amount, $fromCurrency, $currencyCode);
        $converted = $thousandSeparatorRemove == 'true' ? currency()->thousandSeparatorRemove($converted) : $converted;

        return $this->success([
            'base_currency' => $fromCurrency,
            'target_currency' => $currencyCode,
            'base_amount' => $amount,
            'converted_amount' => $converted,
            'rate' => (string) (currency()->getCurrencyRate($currencyCode) / currency()->getCurrencyRate($fromCurrency)),
            'thousandSeparatorRemove' => $thousandSeparatorRemove == 'true',
        ]);
    }

    public function markNotification(Request $request)
    {
        $user = $request->user();

        Notification::where('for', '!=', 'admin')->where('user_id', $user->id)->update(['read' => true]);

        return $this->success(null, __('All Notifications marked as read'));
    }
}
