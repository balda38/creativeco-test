<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Common\CoingateParser\Currencies;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Stubs\CoingateClientStub;

class CoingateParserCurrenciesTest extends TestCase
{
    use DatabaseTransactions;

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

        app()->bind('coingateClient', function() {
            return new CoingateClientStub();
        });
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->currency = null;
    }

    public function testCurrenciesParse()
    {
        Currencies::parse();

        $this->assertDatabaseHas('currencies', [
            'code' => 'EUR',
        ]);
        $this->assertDatabaseHas('currencies', [
            'code' => self::EXISTING_CURRENCY_CODE,
            'archived' => true,
        ]);
    }
}
