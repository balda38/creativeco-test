<?php

namespace App\Common\CoingateParser;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;

use App\Events\CurrencyExchangeRatesSaved;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ExchangeRates extends Parser
{
    protected static function getClientOperation(): string
    {
        return 'getExchangeRates';
    }

    protected static function validationRules(): array
    {
        return [
            '*.*' => 'required|numeric|min:0',
        ];
    }

    protected static function process(array $data): void
    {
        DB::transaction(function () use ($data) {
            $now = Carbon::now();
            $currencies = Currency::isArchived(false)->get(['id', 'code']);
            if ($currencies->isEmpty()) {
                return;
            }

            foreach ($data as $fromCurrencyCode => $exchangeRates) {
                $fromCurrency = $currencies->firstWhere('code', $fromCurrencyCode);

                $ratesPart = [];
                foreach ($exchangeRates as $toCurrencyCode => $rate) {
                    if ($fromCurrencyCode === $toCurrencyCode) {
                        continue;
                    }

                    $toCurrency = $currencies->firstWhere('code', $toCurrencyCode);
                    if (!$fromCurrency || !$toCurrency) {
                        continue;
                    }

                    $model = self::getModel($fromCurrency, $toCurrency);
                    $model->value = (float) $rate;
                    if (!$model->exists) {
                        $model->created_at = $now;
                    }
                    $model->updated_at = $now;
                    $model->save();
                    $ratesPart[] = $model;
                }
                event(new CurrencyExchangeRatesSaved($ratesPart));
            }
        });
    }

    private static function getModel(Currency $fromCurrency, Currency $toCurrency): CurrencyExchangeRate
    {
        $exchangeRateModel = CurrencyExchangeRate::forFromCurrency($fromCurrency)
            ->forToCurrency($toCurrency)
            ->first()
        ;
        if (!$exchangeRateModel) {
            $exchangeRateModel = new CurrencyExchangeRate();
            $exchangeRateModel->from_currency_id = $fromCurrency->id;
            $exchangeRateModel->to_currency_id = $toCurrency->id;
        }

        return $exchangeRateModel;
    }
}
