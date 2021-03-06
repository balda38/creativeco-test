<?php

namespace Tests\Unit\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParseCurrenciesTest extends TestCase
{
    use RefreshDatabase;

    public function testCommandResult()
    {
        $this->artisan('parse:currencies')->assertExitCode(0);
    }
}
