<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Common\CoingateParser\Currencies;

class ParseCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse data about currencies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Currencies::parse();

        return 0;
    }
}
