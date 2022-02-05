<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\CurrenciesParse as CurrenciesParseJob;

class CurrenciesParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse data about currencies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CurrenciesParseJob::dispatch();

        return 0;
    }
}
