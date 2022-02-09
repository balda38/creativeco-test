<?php

namespace App\Jobs;

use App\Models\UserAccountBuyTask;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Carbon;

/**
 * Mark all user account buy tasks for archived currencies as canceled.
 */
class CancelUserAccountBuyTasksByArchivedCurrencies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * @var App\Models\Currency[]
     */
    private $archivedCurrencies;

    public function __construct(array $archivedCurrencies)
    {
        $this->archivedCurrencies = $archivedCurrencies;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $archivedCurrenciesIds = collect($this->archivedCurrencies)->pluck('id');

        UserAccountBuyTask::waiting()
            ->whereHas('userAccount', function (Builder $query) use ($archivedCurrenciesIds) {
                $query->whereIn('currency_id', $archivedCurrenciesIds);
            })
            ->orWhereHas('goalUserAccount', function (Builder $query) use ($archivedCurrenciesIds) {
                $query->whereIn('currency_id', $archivedCurrenciesIds);
            })
            ->update(['canceled_at' => Carbon::now()]);
    }
}
