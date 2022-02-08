<?php

namespace App\Listeners;

use App\Models\UserAccount;
use App\Events\CurrencyExchangeRatesSaved;
use App\Jobs\CompleteUserAccountBuyTasks;

use Illuminate\Database\Eloquent\Builder;

class CurrencyExchangeListener
{
    public $afterCommit = true;

    public function handle(CurrencyExchangeRatesSaved $event)
    {
        if ($event->isEmpty()) {
            return;
        }

        $accounts = UserAccount::forCurrency($event->getFromCurrency())
            ->whereHas('incomingBuyTasks', function (Builder $query) {
                $query->waiting();
            })
            ->orderBy('created_at')
            ->get();
        foreach ($accounts as $account) {
            CompleteUserAccountBuyTasks::dispatch($account, $event->getExchangeRates());
        }
    }
}
