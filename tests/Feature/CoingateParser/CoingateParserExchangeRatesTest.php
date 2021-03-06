<?php

namespace Tests\Feature\CoingateParser;

use App\Models\Currency;
use App\Common\CoingateParser\ExchangeRates;

use App\Exceptions\CoingateParserException;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Stubs\CoingateClientStub;
use Tests\Stubs\CoingateClientWithErrorStub;

class CoingateParserExchangeRatesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Currency
     * @psalm-ignore-var
     */
    private $currency1;
    /**
     * @var Currency
     * @psalm-ignore-var
     */
    private $currency2;
    /**
     * @var Currency
     * @psalm-ignore-var
     */
    private $currency3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency1 = Currency::factory()->create([
            'code' => 'USD',
        ]);
        $this->currency2 = Currency::factory()->create([
            'code' => 'EUR',
        ]);
        $this->currency3 = Currency::factory()->create([
            'code' => 'RUB',
            'archived' => true,
        ]);

        app()->bind('coingateClient', function () {
            return new CoingateClientStub();
        });
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        app()->bind('coingateClient', function () {
            return new \Balda38\CoingateExchangeClient\Client();
        });

        $this->currency1 = null;
        $this->currency2 = null;
        $this->currency3 = null;
    }

    public function testExchangeRatesParse()
    {
        ExchangeRates::parse();

        $this->assertDatabaseHas('currency_exchange_rates', [
            'from_currency_id' => $this->currency1->id,
            'to_currency_id' => $this->currency2->id,
        ]);
        $this->assertDatabaseHas('currency_exchange_rates', [
            'from_currency_id' => $this->currency2->id,
            'to_currency_id' => $this->currency1->id,
        ]);
        $this->assertDatabaseMissing('currency_exchange_rates', [
            'from_currency_id' => $this->currency3->id,
        ]);
        $this->assertDatabaseCount('currency_exchange_rates', 2);
    }

    public function testExchangeRatesOnArchivedCurrenciesParse()
    {
        $this->currency1->archived = true;
        $this->currency1->save();
        $this->currency2->archived = true;
        $this->currency2->save();

        ExchangeRates::parse();
        $this->assertDatabaseCount('currency_exchange_rates', 0);
    }

    public function testExchangeRatesWithErrorParse()
    {
        $this->expectException(CoingateParserException::class);
        app()->bind('coingateClient', function () {
            return new CoingateClientWithErrorStub();
        });
        ExchangeRates::parse();
    }
}
