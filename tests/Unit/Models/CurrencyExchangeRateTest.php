<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencyExchangeRateTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Currency
     */
    private $currency1;
    /**
     * @var Currency
     */
    private $currency2;
    /**
     * @var CurrencyExchangeRate
     */
    private $exchangeRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency1 = Currency::factory()->create();
        $this->currency2 = Currency::factory()->create();
        $this->exchangeRate = CurrencyExchangeRate::factory()->create([
            'from_currency_id' => $this->currency1->id,
            'to_currency_id' => $this->currency2->id,
        ]);
    }

    protected function tearDonw(): void
    {
        parent::tearDown();

        $this->currency1 = null;
        $this->currency2 = null;
        $this->exchangeRate = null;
    }

    public function testCurrencyExchangeRateFromCurrency()
    {
        $this->assertTrue($this->exchangeRate->fromCurrency->is($this->currency1));
    }

    public function testCurrencyExchangeRateToCurrency()
    {
        $this->assertTrue($this->exchangeRate->toCurrency->is($this->currency2));
    }

    public function testCurrencyExchangeRateScopeForFromCurrency()
    {
        $exchangeRate = CurrencyExchangeRate::forFromCurrency($this->currency1)->first();
        $this->assertTrue($exchangeRate->is($this->exchangeRate));
        $this->assertEquals($exchangeRate->from_currency_id, $this->currency1->id);
    }

    public function testCurrencyExchangeRateScopeForToCurrency()
    {
        $exchangeRate = CurrencyExchangeRate::forToCurrency($this->currency2)->first();
        $this->assertTrue($exchangeRate->is($this->exchangeRate));
        $this->assertEquals($exchangeRate->to_currency_id, $this->currency2->id);
    }
}
