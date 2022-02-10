<?php

namespace Tests\Feature\CoingateParser;

use App\Models\Currency;
use App\Common\CoingateParser\Currencies;

use App\Exceptions\CoingateParserException;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Stubs\CoingateClientStub;
use Tests\Stubs\CoingateClientWithErrorStub;

class CoingateParserCurrenciesTest extends TestCase
{
    use RefreshDatabase;

    const EXISTING_CURRENCY_CODE = 'USD';

    /**
     * @var Currency
     */
    private $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency = Currency::factory()->create([
            'code' => self::EXISTING_CURRENCY_CODE,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        app()->bind('coingateClient', function() {
            return new \Balda38\CoingateExchangeClient\Client();
        });

        $this->currency = null;
    }

    public function testCurrenciesParse()
    {
        app()->bind('coingateClient', function() {
            return new CoingateClientStub();
        });
        Currencies::parse();

        $this->assertDatabaseHas('currencies', [
            'code' => 'EUR',
        ]);
        $this->assertDatabaseHas('currencies', [
            'code' => self::EXISTING_CURRENCY_CODE,
            'archived' => true,
        ]);
    }

    public function testCurrenciesWithErrorParse()
    {
        $this->expectException(CoingateParserException::class);
        app()->bind('coingateClient', function() {
            return new CoingateClientWithErrorStub();
        });
        Currencies::parse();
    }
}
