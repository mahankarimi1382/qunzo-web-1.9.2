<?php

use App\Enums\CurrencyType;
use App\Enums\KYCStatus;
use App\Enums\TicketStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Agent;
use App\Models\Currency;
use App\Models\CustomCss;
use App\Models\Gateway;
use App\Models\Language;
use App\Models\LevelReferral;
use App\Models\Merchant;
use App\Models\PageSetting;
use App\Models\Plugin;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserNavigation;
use App\Models\UserWallet;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

if (! function_exists('isActive')) {
    function isActive($route, $parameter = null)
    {
        if ($parameter != null && request()->url() === route($route, $parameter)) {
            return 'active';
        }

        if ($parameter == null && is_array($route)) {
            foreach ($route as $value) {
                if (Request::routeIs($value)) {
                    return 'show';
                }
            }
        }

        if ($parameter == null && Request::routeIs($route)) {
            return 'active';
        }

        return null;
    }
}

if (! function_exists('tnotify')) {
    function tnotify($type, $message)
    {
        session()->flash('tnotify', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}

if (! function_exists('setting')) {
    function setting($key, $section = null, $default = null)
    {
        if (is_null($key)) {
            return new Setting;
        }

        if (is_array($key)) {

            return Setting::set($key[0], $key[1]);
        }

        $value = Setting::getValue($key, $section, $default);

        return is_null($value) ? value($default) : $value;
    }
}

if (! function_exists('oldSetting')) {

    function oldSetting($field, $section)
    {
        return old($field, setting($field, $section));
    }
}

if (! function_exists('settingValue')) {

    function settingValue($field)
    {
        return Setting::get($field);
    }
}

if (! function_exists('getPageSetting')) {

    function getPageSetting($key)
    {
        return PageSetting::where('key', $key)->first()?->value;
    }
}

if (! function_exists('curl_get_file_contents')) {

    function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) {
            return $contents;
        }

        return false;
    }
}

if (! function_exists('getCountries')) {

    function getCountries()
    {
        return json_decode(file_get_contents(resource_path().'/json/CountryCodes.json'), true);
    }
}

if (! function_exists('getCurrency')) {

    function getCurrency($countryName)
    {
        $currencies = json_decode(getJsonData('currency'), true)['fiat'];

        return collect($currencies)->filter(function ($value) use ($countryName) {
            return str_contains($value['text'], $countryName);
        })->value('id', '');
    }
}

if (! function_exists('getJsonData')) {

    function getJsonData($fileName)
    {
        return file_get_contents(resource_path().sprintf('/json/%s.json', $fileName));
    }
}

if (! function_exists('getTimezone')) {
    function getTimezone()
    {
        $timeZones = json_decode(file_get_contents(resource_path().'/json/timeZone.json'), true);

        return array_values(Arr::sort($timeZones, function ($value) {
            return $value['name'];
        }));
    }
}

if (! function_exists('getIpAddress')) {
    function getIpAddress()
    {
        return request()->ip();
    }
}

if (! function_exists('getLocation')) {
    function getLocation()
    {
        $clientIp = request()->ip();

        $ip = $clientIp == '127.0.0.1' ? '103.77.188.202' : $clientIp;

        $response = json_decode(curl_get_file_contents('http://ip-api.com/json/'.$ip), true);
        if (isset($response['status']) && $response['status'] == 'fail') {

            return new \Illuminate\Support\Fluent([
                'country_code' => 0,
                'name' => 'Unknown',
                'dial_code' => '',
                'ip' => $ip,
            ]);
        }

        $countryCode = $response['countryCode'] ?? null;

        $currentCountry = collect(getCountries())->first(function ($value) use ($countryCode) {
            return $value['code'] == $countryCode;
        });

        $location = [
            'country_code' => data_get($currentCountry, 'code', 0),
            'name' => data_get($currentCountry, 'name', 'Unknown'),
            'dial_code' => data_get($currentCountry, 'dial_code', ''),
            'ip' => $response['query'] ?? $ip,
        ];

        return new \Illuminate\Support\Fluent($location);
    }
}

if (! function_exists('gateway_info')) {
    function gateway_info($code)
    {
        $info = Gateway::where('gateway_code', $code)->first();

        return json_decode($info->credentials);
    }
}

if (! function_exists('plugin_active')) {
    function plugin_active($name)
    {
        return Plugin::where('name', $name)->where('status', true)->first();
    }
}

if (! function_exists('get_navigation_name')) {
    function get_navigation_name($type)
    {
        $navigation = UserNavigation::where('type', $type)->first();

        return $navigation->name ?? '';
    }
}

if (! function_exists('default_plugin')) {
    function default_plugin($type)
    {
        return Plugin::where('type', $type)->where('status', 1)->first('name')?->name;
    }
}

if (! function_exists('br2nl')) {
    function br2nl($input)
    {
        return preg_replace('/<br\\s*?\/??>/i', '', $input);
    }
}

if (! function_exists('safe')) {
    function safe($input)
    {
        if (! env('APP_DEMO', false)) {
            return $input;
        }

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {

            $emailParts = explode('@', $input);
            $username = $emailParts[0];
            $hiddenUsername = substr($username, 0, 2).str_repeat('*', strlen($username) - 2);
            $hiddenEmailDomain = substr($emailParts[1], 0, 2).str_repeat('*', strlen($emailParts[1]) - 3).$emailParts[1][strlen($emailParts[1]) - 1];

            return $hiddenUsername.'@'.$hiddenEmailDomain;
        }

        return preg_replace('/(\d{3})\d{3}(\d{3})/', '$1****$2', $input);
    }
}

if (! function_exists('site_theme')) {
    function site_theme()
    {
        return once(function () {
            return Theme::where('status', true)->value('name');
        });
    }
}

if (! function_exists('getLandingContents')) {
    function getLandingContents($type)
    {
        $data = \App\Models\LandingContent::where('locale', app()->getLocale())->where('theme', site_theme())->where('type', $type)->get();

        return $data;
    }
}

if (! function_exists('generate_date_range_array')) {
    function generate_date_range_array($startDate, $endDate): array
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $dates = collect([]);

        while ($startDate->lte($endDate)) {
            $dates->push($startDate->format('d M'));
            $startDate->addDay();
        }

        return $dates->toArray();
    }
}

if (! function_exists('getQRCode')) {
    function getQRCode($data)
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data='.$data;
    }
}

if (! function_exists('generateAccountNumber')) {
    function generateAccountNumber()
    {
        do {
            $account_number = substr(random_int(1000000000000000, 9999999999999999), 0, setting('account_no_limit', 'global'));
        } while (User::where('account_number', $account_number)->exists());

        return $account_number;
    }
}

if (! function_exists('generateReferralCode')) {
    function generateReferralCode()
    {
        do {
            $referral_code = Str::random(setting('referral_code_limit', 'global'));
        } while (User::where('referral_code', $referral_code)->exists());

        return $referral_code;
    }
}

if (! function_exists('merchantSystemEnabled')) {
    function merchantSystemEnabled()
    {
        return (bool) setting('merchant_system', 'permission');
    }
}

if (! function_exists('agentSystemEnabled')) {
    function agentSystemEnabled()
    {
        return (bool) setting('agent_system', 'permission');
    }
}

if (! function_exists('generateUniqueUsername')) {
    function generateUniqueUsername($name)
    {
        do {
            $username = str_replace(' ', '', strtolower($name)).rand(1000, 9999);
        } while (User::where('username', $username)->exists());

        return $username;
    }
}

if (! function_exists('pending_count')) {
    function pending_count()
    {
        $withdrawCount = Transaction::where('type', TxnType::Withdraw)
            ->where('status', TxnStatus::Pending)
            ->count();

        $kycCount = User::where('kyc', KYCStatus::Pending)->count();

        $depositCount = Transaction::where('type', TxnType::ManualDeposit)
            ->where('status', TxnStatus::Pending)
            ->count();

        $ticketCount = Ticket::where('status', TicketStatus::OPEN)->count();

        $merchantRequests = Merchant::where('status', \App\Enums\MerchantStatus::Pending)->count();
        $agentRequests = Agent::where('status', \App\Enums\AgentStatus::Pending)->count();

        return [
            'withdraw_count' => $withdrawCount,
            'kyc_count' => $kycCount,
            'deposit_count' => $depositCount,
            'ticket_count' => $ticketCount,
            'merchant_requests_count' => $merchantRequests,
            'agent_requests_count' => $agentRequests,
        ];
    }
}

if (! function_exists('defaultLocale')) {
    function defaultLocale()
    {
        $language = Language::where('is_default', true)->first();

        return $language->locale ?? 'en';
    }
}

if (! function_exists('isRtl')) {
    function isRtl($code)
    {
        return once(function () use ($code) {
            return Language::where('locale', $code)->first()->is_rtl ?? false;
        });
    }
}

if (! function_exists('getActiveLangName')) {
    function getActiveLangName()
    {
        return Language::where('locale', app()->getLocale())->first()?->name;
    }
}

if (! function_exists('getBrowser')) {

    function getBrowser($user_agent = null)
    {

        $user_agent = $user_agent != null ? $user_agent : request()->userAgent();

        $browser = 'Unknown';
        $platform = 'Unknown';

        if (preg_match('/linux/i', $user_agent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $platform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            $platform = 'Windows';
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            $platform = 'Windows';
        }

        if (preg_match('/MSIE/i', $user_agent) && ! preg_match('/Opera/i', $user_agent)) {
            $browser = 'IE';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/OPR/i', $user_agent)) {
            $browser = 'Opera';
        } elseif (preg_match('/Chrome/i', $user_agent) && ! preg_match('/Edge/i', $user_agent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $user_agent) && ! preg_match('/Edge/i', $user_agent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Netscape/i', $user_agent)) {
            $browser = 'Netscape';
        } elseif (preg_match('/Edge/i', $user_agent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Trident/i', $user_agent)) {
            $browser = 'IE';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
        ];
    }
}

if (! function_exists('mySqlVersion')) {
    function mySqlVersion()
    {
        $pdo = DB::connection()->getPdo();
        $version = $pdo->query('select version()')->fetchColumn();

        preg_match("/^[0-9\.]+/", $version, $match);

        return $match[0];
    }
}

if (! function_exists('notify')) {
    function notify(?string $message = null, ?string $title = null)
    {
        $notify = app('notify');

        if (! is_null($message)) {
            return $notify->success($message, $title);
        }

        return $notify;
    }
}

if (! function_exists('hideCharacter')) {
    function hideCharacter($string)
    {
        // Get the last 4 digits of the account number
        $lastFourDigits = substr($string, -4);

        // Mask the rest of the account number with asterisks
        $masked = str_repeat('*', strlen($string) - 4).$lastFourDigits;

        return $masked;
    }
}

if (! function_exists('months')) {
    function months()
    {

        $start = Carbon::createFromDate(null, 1, 1); // Set January
        $months = [];

        for ($i = 0; $i < 12; $i++) {
            $months[] = $start->monthName;
            $start->addMonth();
        }

        return $months;
    }
}

if (! function_exists('days')) {
    function days()
    {

        $days = [];
        $start = Carbon::now();

        for ($i = 0; $i < 32; $i++) {
            $days[] = $start->copy()->subDays($i)->day; // Format as 'Y-m-d'
        }

        return $days;
    }
}

if (! function_exists('socialMediaShareLinks')) {
    function socialMediaShareLinks(string $path, string $provider)
    {
        switch ($provider) {
            case 'facebook':
                $share_link = 'https://www.facebook.com/sharer/sharer.php?u='.$path;
                break;
            case 'twitter':
                $share_link = 'https://twitter.com/intent/tweet?text='.$path;
                break;
            case 'linkedin':
                $share_link = 'https://www.linkedin.com/shareArticle?mini=true&url='.$path;
                break;
        }

        return $share_link;
    }
}

if (! function_exists('formatNumber')) {
    function formatNumber($number)
    {
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1).'M+';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1).'K+';
        }

        return $number;
    }
}

if (! function_exists('creditReferralBonus')) {
    function creditReferralBonus($user, $type, $mainAmount, $level = null, $depth = 1, $fromUser = null, $wallet_id = 'default')
    {
        $LevelReferral = LevelReferral::query()
            ->where('type', $type)
            ->where('the_order', $depth)
            ->first('bounty');

        if ($user->ref_id !== null && $depth <= $level && $LevelReferral) {
            $referrer = User::find($user->ref_id);

            $bounty = $LevelReferral->bounty;

            $amount = (float) ($mainAmount * $bounty) / 100;

            $fromUserReferral = $fromUser == null ? $user : $fromUser;

            $description = ucwords(str_replace('_', ' ', $type)).' Referral Bonus Via '.$fromUserReferral->full_name.' - Level '.$depth;

            $transaction = new Transaction;
            $transaction->user_id = $referrer->id;
            $transaction->from_user_id = $fromUserReferral->id;
            $transaction->from_model = 'User';
            $transaction->description = $description;
            $transaction->amount = $amount;
            $transaction->type = TxnType::Referral;
            $transaction->charge = 0;
            $transaction->final_amount = $amount;
            $transaction->method = 'System';
            $transaction->status = TxnStatus::Success;
            $transaction->wallet_type = $wallet_id;
            $transaction->save();

            $referrer->balance += $amount;
            $referrer->save();

            creditReferralBonus($referrer, $type, $mainAmount, $level, $depth + 1, $user);
        }
    }
}

if (! function_exists('creditCurrencyWiseReferralBonus')) {
    function creditCurrencyWiseReferralBonus($user, $type, $mainAmount, $level = null, $depth = 1, $fromUser = null, $userWallet = null)
    {
        $level = LevelReferral::where('type', $type)->max('the_order') + 1;

        // Convert the amount to the site currency
        $toCurrency = setting('site_currency', 'global');

        if ($userWallet instanceof UserWallet && $userWallet?->currency instanceof Currency) {
            $mainAmount = formatAmount(CurrencyService::convert($mainAmount, $userWallet->currency->code, $toCurrency), $toCurrency, false, true);
        }

        creditReferralBonus($user, $type, $mainAmount, $level, $depth, $fromUser, 'default');
    }
}

if (! function_exists('getShortName')) {
    function getShortName(?string $name): string
    {
        try {
            return collect(explode(' ', $name))
                ->filter()
                ->map(function ($part) {
                    return strtoupper($part[0]);
                })
                ->implode('');
        } catch (Exception $exception) {
            return 'N/A';
        }
    }
}

if (! function_exists('isPlusTransaction')) {
    function isPlusTransaction($type)
    {
        return in_array($type, [
            TxnType::Deposit,
            TxnType::ManualDeposit,
            TxnType::ReceiveMoney,
            TxnType::CashReceived,
            TxnType::Refund,
            TxnType::Referral,
            TxnType::Exchange,
            TxnType::GiftRedeemed,
            TxnType::SignupBonus,
            TxnType::CashIn,
            TxnType::CashoutCommission,
            TxnType::Credit,
            TxnType::PaymentLink,
        ]);
    }
}

if (! function_exists('isAgentPlusTransaction')) {
    function isAgentPlusTransaction($type)
    {
        return in_array($type, [
            TxnType::Deposit,
            TxnType::ManualDeposit,
            TxnType::CashoutCommission,
            TxnType::Credit,
            TxnType::SignupBonus,
            TxnType::CashReceived,
            TxnType::CashInCommission,
        ]);
    }
}

if (! function_exists('generateKey')) {
    function generateKey(string $type): string
    {
        do {
            $key = Str::random(40);
        } while (Merchant::where($type, $key)->exists());

        return $key;
    }
}

if (! function_exists('formatAmount')) {
    function formatAmount($amount, $currency = null, $showCurrency = false, $thousandSeparatorRemove = false): string
    {
        // if currency is string and not instance of Currency, then get the currency from cache
        if (is_string($currency) && ! $currency instanceof Currency && $currency !== setting('site_currency', 'global')) {
            $currency = once(fn () => Currency::where('code', $currency)->first());
        }
        // Set the precision
        [$currencyCode, $precision] = $currency instanceof Currency ? match ($currency?->type) {
            CurrencyType::Crypto => [$currency->code, 8],
            CurrencyType::Fiat => [$currency->code, 2],
            default => [$currency->code ?? setting('site_currency', 'global'), setting('site_currency_decimals', 'global')],
        } : [$currency ? setting('site_currency') : 'N/A', $currency ? setting('site_currency_decimals', 'global') : 2];

        // return format amount
        $amount = $thousandSeparatorRemove ? str_replace(',', '', Number::format($amount, precision: $precision)) : Number::format($amount, precision: $precision);

        return $showCurrency ? sprintf('%s %s', $amount, $currencyCode) : $amount;
    }
}

if (! function_exists('isMerchantPlusTransaction')) {
    function isMerchantPlusTransaction(TxnType $type): bool
    {
        return in_array($type, [
            TxnType::Payment,
            TxnType::Credit,
            TxnType::SignupBonus,
        ]);
    }
}

if (! function_exists('currency')) {
    function currency()
    {
        return new CurrencyService;
    }
}

if (! function_exists('notificationIcon')) {
    function notificationIcon($type, $for = 'user')
    {
        // User Icons
        $userIcons = [
            'user_mail' => 'hugeicons--invoice-01',
            'user_manual_deposit_approved' => 'hugeicons--money-bag-02',
            'user_manual_deposit_rejected' => 'hugeicons--money-bag-02',
            'user_invoice_payment' => 'hugeicons--invoice-01',
            'user_request_money' => 'hugeicons--money-bag-02',
            'user_gift_redeem' => 'hugeicons--gift',
            'user_cashout' => 'hugeicons--wallet-03',
            'user_withdraw' => 'hugeicons--reverse-withdrawal-01',
            'user_transfer_money' => 'mynaui--send-solid',
            'user_referral_join' => 'hugeicons--workflow-square-10',
            'user_ticket_reply' => 'hugeicons--customer-support',
        ];

        // Merchant Icons
        $merchantIcons = [
            'merchant_payment' => 'hugeicons--payment-02',
            'merchant_ticket_reply' => 'hugeicons--customer-support',
            'merchant_withdraw' => 'hugeicons--reverse-withdrawal-01',
        ];

        // Agent Icons
        $agentIcons = [
            'agent_withdraw' => 'hugeicons--reverse-withdrawal-01',
            'agent_ticket_reply' => 'hugeicons--customer-support',
            'agent_commission' => 'hugeicons--money-bag-02',
        ];

        return match ($for) {
            'user' => $userIcons[$type] ?? 'hugeicons--notification-02',
            'merchant' => $merchantIcons[$type] ?? 'hugeicons--notification-02',
            'agent' => $agentIcons[$type] ?? 'hugeicons--notification-02',
            default => 'hugeicons--notification-02',
        };
    }
}

if (! function_exists('getTransactionIcon')) {
    function getTransactionIcon(string $type): string
    {
        $icons = [
            'Credit' => 'hugeicons--credit-card',
            'Debit' => 'hugeicons--credit-card-pos',
            'Deposit' => 'hugeicons--money-bag-02',
            'Manual Deposit' => 'hugeicons--money-saving-jar',
            'Withdraw' => 'hugeicons--reverse-withdrawal-01',
            'Send Money' => 'hugeicons--money-send-01',
            'Referral' => 'hugeicons--workflow-square-10',
            'Withdraw Auto' => 'hugeicons--reverse-withdrawal-01',
            'Receive Money' => 'hugeicons--save-money-dollar',
            'Refund' => 'hugeicons--coins-dollar',
            'Exchange' => 'hugeicons--card-exchange-02',
            'Signup Bonus' => 'hugeicons--gift',
            'Payment' => 'hugeicons--payment-02',
            'Gift Redeemed' => 'hugeicons--gift-card',
            'Cash In' => 'hugeicons--cash-02',
            'Cash Out' => 'hugeicons--money-remove-01',
            'Cash Received' => 'hugeicons--money-remove-02',
            'Request Money' => 'hugeicons--return-request',
            'Invoice' => 'hugeicons--invoice',
            'Cashout Commission' => 'hugeicons--cash-02',
        ];

        return $icons[$type] ?? 'hugeicons--cash-02';
    }
}
if (! function_exists('highlightColor')) {
    function highlightColor($text, $class = 'gradient-text')
    {
        return preg_replace_callback('/\[\[color_text=(.*?)\]\]/', function ($matches) use ($class) {
            return '<span class="'.$class.'">'.$matches[1].'</span>';
        }, $text);
    }
}

if (! function_exists('greeting')) {
    function greeting(): string
    {
        $hour = now()->hour;

        if ($hour < 12) {
            $greeting = __('Good morning');
        } elseif ($hour < 18) {
            $greeting = __('Good afternoon');
        } else {
            $greeting = __('Good evening');
        }

        return $greeting;
    }
}

if (! function_exists('getBasename')) {
    function getBasename($path): string
    {
        return basename($path);
    }
}

if (! function_exists('isValidationException')) {
    function isValidationException($exception)
    {
        return $exception instanceof \Illuminate\Validation\ValidationException;
    }
}

if (! function_exists('makeValidationException')) {
    function makeValidationException($errors)
    {
        return ValidationException::withMessages($errors);
    }
}

if (! function_exists('isFromApp')) {
    function isFromApp()
    {
        $request = request();

        return $request->query('is_app') == 'true' || $request->query('is_app') == true || str($request->fullUrl())->contains('is_app=true');
    }
}

if (! function_exists('customCss')) {
    function customCss()
    {
        return CustomCss::first()->css ?? '';
    }
}

if (! function_exists('isPreviewableFile')) {
    function isPreviewableFile($file)
    {
        $previewableMimes = [
            'jpeg',
            'png',
            'jpg',
            'gif',
            'svg',
            'webp',
        ];

        $extension = pathinfo($file, PATHINFO_EXTENSION);

        return in_array($extension, $previewableMimes);
    }
}

if (! function_exists('getAllAddons')) {
    function getAllAddons()
    {
        $addonsPath = base_path('modules/Addons');

        // Get all addons and memoize the result for the duration of the request
        $addons = once(function () use ($addonsPath) {
            $directories = File::directories($addonsPath);

            return collect($directories)
                ->reject(fn ($addonPath) => ! File::exists($addonPath.'/plugin.json'))
                ->map(function ($addonPath) {
                    $pluginJsonPath = $addonPath.'/plugin.json';

                    $data = json_decode(File::get($pluginJsonPath), true) ?: [];
                    $data['json_path'] = $pluginJsonPath;

                    return $data;
                })
                ->filter()
                ->all();
        });

        return $addons;
    }
}

if (! function_exists('addonActive')) {
    function addonActive($slug)
    {
        $addons = getAllAddons();

        $data = collect($addons)
            ->where('slug', $slug)
            ->first();

        return $data['active'] ?? false;
    }
}
