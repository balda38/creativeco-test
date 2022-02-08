<?php

namespace App\Common\Utils;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;

use App\Exceptions\CurrencyExchangeRatesConverterException;

class CurrencyExchangeRatesConverter
{
    public static function getReverseExchangeRate(CurrencyExchangeRate $rate): CurrencyExchangeRate
    {
        $rate = CurrencyExchangeRate::forCurrency($rate->toCurrency)
            ->toCurrency($rate->fromCurrency)
            ->first();
        if (!$rate) {
            throw new CurrencyExchangeRatesConverterException('Exchange rate not found');
        }

        return $rate;
    }

    /**
     * Transform passed value in one currency to value in other currency
     * on current exchange rate.
     */
    public static function reverseValue(float $value, Currency $fromCurrency, Currency $toCurrency): float
    {
        $rate = CurrencyExchangeRate::forFromCurrency($fromCurrency)
            ->forToCurrency($toCurrency)
            ->first();
        if (!$rate) {
            throw new CurrencyExchangeRatesConverterException('Exchange rate not found');
        }

        return $rate->value / $value;
    }
}
