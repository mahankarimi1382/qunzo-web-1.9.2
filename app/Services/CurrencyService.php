<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $from = self::getCurrencyRate($fromCurrency);
        $to = self::getCurrencyRate($toCurrency);

        return ($amount / $from) * $to;
    }

    public static function getConvertedAmount($amount, $toCurrency, $thousanSepartorRemove = false)
    {
        $fromCurrency = self::getBaseCurrency();

        $convertedAmount = self::convert($amount, $fromCurrency, $toCurrency);

        $formattedAmount = self::formatAmount($convertedAmount, $toCurrency);

        return $thousanSepartorRemove ? self::thousandSeparatorRemove($formattedAmount) : $formattedAmount;
    }

    public static function thousandSeparatorRemove($amount)
    {
        return str_replace(',', '', $amount);
    }

    public static function getCurrencyRate($code)
    {
        $currency = $code instanceof Currency || setting('site_currency', 'global') == $code ? $code : once(fn () => Currency::where('code', $code)->first());

        return $currency->conversion_rate ?? 1;
    }

    public static function getCashoutLimitText($currency)
    {
        $min_amount = static::getConvertedAmount(setting('cashout_minimum', 'cashout'), $currency);
        $max_amount = static::getConvertedAmount(setting('cashout_maximum', 'cashout'), $currency);

        $currencyCode = $currency instanceof Currency ? $currency->code : $currency;

        return self::textFormat($min_amount, $max_amount, $currencyCode);
    }

    public static function getCashinLimitText($currency)
    {
        $min_amount = static::getConvertedAmount(setting('cashin_minimum', 'cashin'), $currency);
        $max_amount = static::getConvertedAmount(setting('cashin_maximum', 'cashin'), $currency);
        $currencyCode = $currency instanceof Currency ? $currency->code : $currency;

        return self::textFormat($min_amount, $max_amount, $currencyCode);
    }

    public static function getPaymentLimitText($currency)
    {
        $min_amount = static::getConvertedAmount(setting('payment_minimum', 'make_payment'), $currency);
        $max_amount = static::getConvertedAmount(setting('payment_maximum', 'make_payment'), $currency);
        $currencyCode = $currency instanceof Currency ? $currency->code : $currency;

        return self::textFormat($min_amount, $max_amount, $currencyCode);
    }

    public static function getExchangeLimitText($currency)
    {
        $min_amount = static::getConvertedAmount(setting('exchange_minimum', 'exchange'), $currency);
        $max_amount = static::getConvertedAmount(setting('exchange_maximum', 'exchange'), $currency);
        $currencyCode = $currency instanceof Currency ? $currency->code : $currency;

        return self::textFormat($min_amount, $max_amount, $currencyCode);
    }

    public static function getRequestMoneyLimitText($currency)
    {
        $min_amount = static::getConvertedAmount(setting('request_money_minimum', 'request_money'), $currency);
        $max_amount = static::getConvertedAmount(setting('request_money_maximum', 'request_money'), $currency);
        $currencyCode = $currency instanceof Currency ? $currency->code : $currency;

        return self::textFormat($min_amount, $max_amount, $currencyCode);
    }

    public static function getGiftLimitText($currency)
    {
        $min_amount = static::getConvertedAmount(setting('gift_minimum', 'gift'), $currency);
        $max_amount = static::getConvertedAmount(setting('gift_maximum', 'gift'), $currency);

        $currencyCode = $currency instanceof Currency ? $currency->code : $currency;

        return self::textFormat($min_amount, $max_amount, $currencyCode);
    }

    public static function getTransferLimitText($currency)
    {
        $min_amount = static::getConvertedAmount(setting('transfer_minimum', 'transfer'), $currency);
        $max_amount = static::getConvertedAmount(setting('transfer_maximum', 'transfer'), $currency);

        $currencyCode = $currency instanceof Currency ? $currency->code : $currency;

        return self::textFormat($min_amount, $max_amount, $currencyCode);
    }

    private static function textFormat($min_amount, $max_amount, $currencyCode)
    {
        return __('Mininum :min_amount and Maximum :max_amount.', [
            'min_amount' => $min_amount.' '.$currencyCode,
            'max_amount' => $max_amount.' '.$currencyCode,
        ]);
    }

    public static function formatAmount($amount, $currencyCode)
    {
        return formatAmount($amount, $currencyCode);
    }

    public static function getBaseCurrency()
    {
        return setting('site_currency', 'global');
    }
}
