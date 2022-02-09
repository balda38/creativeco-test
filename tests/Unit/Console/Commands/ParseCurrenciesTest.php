<?php

namespace Tests\Unit\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ParseCurrenciesTest extends TestCase
{
    use DatabaseTransactions;

    public function testCommandResult()
    {
        $this->artisan('parse:currencies')->assertExitCode(0);
    }
}
