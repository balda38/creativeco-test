<?php

namespace App\Common\CoingateParser;

use App\Common\Database;

use App\Models\Currency;

class Currencies extends Parser
{
    protected static function getClientOperation() : string
    {
        return 'getCurrencies';
    }

    public static function parse() : void
    {
        $data = parent::getData();
        Database::makeTransaction(function () use ($data) {
            foreach ($data as $currency) {
                $model = self::getModel($currency['symbol']);
                $model->name = $currency['title'];
                $model->code = $currency['symbol'];
                $model->archived = $currency['disabled'];
                $model->save();
            }

            self::markArchive($data);
        });
    }

    private static function getModel(string $currencyCode) : Currency
    {
        $currencyModel = Currency::firstWhere('code', $currencyCode);
        if (!$currencyModel) {
            $currencyModel = new Currency();
        }

        return $currencyModel;
    }

    /**
     * Mark existing in database, but not existing in Coingate currencies
     * as archived.
     */
    private static function markArchive(array $data) : void
    {
        $data = collect($data);
        $currencyCodes = $data->pluck(['symbol']);
        Currency::whereNotIn('code', $currencyCodes)->update(['archived' => true]);
    }
}
