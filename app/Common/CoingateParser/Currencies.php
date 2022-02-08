<?php

namespace App\Common\CoingateParser;

use App\Models\Currency;

use App\Jobs\CancelUserAccountBuyTasksByArchivedCurrencies;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Currencies extends Parser
{
    protected static function getClientOperation(): string
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

    protected static function process(array $data): void
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

    private static function getModel(string $currencyCode): Currency
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
     *
     * Run dispatch job for cancel buy tasks with archived currencies.
     */
    private static function markArchive(array $data): void
    {
        $currencyCodes = collect($data)->pluck(['symbol']);
        $baseQuery = Currency::whereNotIn('code', $currencyCodes);
        $archive = $baseQuery->get();
        $baseQuery->update(['archived' => true]);

        if (!$archive->isEmpty()) {
            CancelUserAccountBuyTasksByArchivedCurrencies::dispatch($archive);
        }
    }
}
