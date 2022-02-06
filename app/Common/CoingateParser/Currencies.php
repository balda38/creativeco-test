<?php

namespace App\Common\CoingateParser;

use App\Models\Currency;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Currencies extends Parser
{
    protected static function getClientOperation() : string
    {
        return 'getCurrencies';
    }

    protected static function validationRules(): array
    {
        return [
            '*.title' => 'required|string',
            '*.symbol' => 'required|string',
            '*.disabled' => 'required|boolean',
        ];
    }

    protected static function process(array $data) : void
    {
        DB::transaction(function () use ($data) {
            $now = Carbon::now();
            foreach ($data as $currency) {
                $model = self::getModel($currency['symbol']);
                $model->name = $currency['title'];
                $model->code = $currency['symbol'];
                $model->archived = $currency['disabled'];
                if (!$model->exists) {
                    $model->created_at = $now;
                }
                $model->updated_at = $now;
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
        $currencyCodes = collect($data)->pluck(['symbol']);
        Currency::whereNotIn('code', $currencyCodes)->update(['archived' => true]);
    }
}
