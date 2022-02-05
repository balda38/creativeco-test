<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Common\CoingateParser\ExchangeRates;

class ExchangeRatesParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:exchangeRates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse data about exchange rates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ExchangeRates::parse();

        return 0;
    }
}
