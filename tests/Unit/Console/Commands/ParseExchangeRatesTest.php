<?php

namespace Tests\Unit\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ParseExchangeRatesTest extends TestCase
{
    use DatabaseTransactions;

    public function testCommandResult()
    {
        $this->artisan('parse:exchangeRates')->assertExitCode(0);
    }
}
