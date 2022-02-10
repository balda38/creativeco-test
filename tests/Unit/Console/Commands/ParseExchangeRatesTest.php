<?php

namespace Tests\Unit\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParseExchangeRatesTest extends TestCase
{
    use RefreshDatabase;

    public function testCommandResult()
    {
        $this->artisan('parse:exchangeRates')->assertExitCode(0);
    }
}
