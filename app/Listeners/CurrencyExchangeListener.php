<?php

namespace App\Listeners;

use App\Models\UserAccount;
use App\Events\CurrencyExchangeRatesSaved;
use App\Jobs\CompleteUserAccountBuyTasks;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Listen changes of currency exchange rates.
 */
class CurrencyExchangeListener
{
    public $afterCommit = true;

    public function handle(CurrencyExchangeRatesSaved $event)
    {
        if ($event->isEmpty()) {
            return;
        }

        $toCurrency = $event->getToCurrency();
        $accounts = UserAccount::forCurrency($toCurrency)
            ->with('incomingBuyTasks', function (HasMany $query) {
                $query->waiting()->orderBy('created_at');
            })
            ->get()
        ;
        foreach ($accounts as $account) {
            CompleteUserAccountBuyTasks::dispatch($account, $event->getExchangeRates())
                ->onQueue('currency-'.$toCurrency->id.'-buy-tasks')
            ;
        }
    }
}
