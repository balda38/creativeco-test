<?php

namespace Tests\Unit\Providers;

use Balda38\CoingateExchangeClient\Client;

use Tests\TestCase;

class AppServiceProvider extends TestCase
{
    public function testServicesExist()
    {
        $this->assertTrue(app()->coingateClient instanceof Client);
    }
}
