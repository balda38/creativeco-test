<?php

namespace App\Jobs;

use App\Models\UserAccount;
use App\Models\UserAccountBuyTask;

use App\Common\Utils\CurrencyExchangeRatesConverter;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CompleteUserAccountBuyTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * @var UserAccount
     */
    private $account;
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $exchangeRates;

    public function __construct(UserAccount $account, array $exchangeRates)
    {
        $this->account = $account;
        $this->exchangeRates = collect($exchangeRates);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tasks = $this->account->incomingBuyTasks->filter(function (UserAccountBuyTask $task) {
            return $task->getIsWaiting();
        })->sortBy('created_at');
        foreach ($tasks as $task) {
            $toCurrency = $task->userAccount->currency;
            $exchangeRate = $this->exchangeRates->where('to_currency_id', '=', $toCurrency->id)
                ->first();

            $exchangeRateSum = $exchangeRate->value * $task->count;
            $exchangeRateSumReverse = CurrencyExchangeRatesConverter::reverseValue(
                $exchangeRate->value,
                $task->goalUserAccount->currency,
                $task->userAccount->currency
            ) * $task->count;
            if ($exchangeRateSumReverse <= $task->getSum() && $exchangeRateSum <= $task->userAccount->value) {
                DB::transaction(function () use ($task, $exchangeRateSum, $exchangeRateSumReverse) {
                    $task->userAccount->value -= $exchangeRateSum;
                    $task->userAccount->save();
                    $task->goalUserAccount->value += $exchangeRateSumReverse;
                    $task->goalUserAccount->save();
                    $task->completed_at = Carbon::now();
                    $task->save();
                });
            }
        }
    }
}
