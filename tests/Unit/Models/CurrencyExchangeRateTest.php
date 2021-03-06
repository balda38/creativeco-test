<?php

namespace Tests\Unit\Models;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyExchangeRateTest extends TestCase
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
     * @var CurrencyExchangeRate
     * @psalm-ignore-var
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

    public function testFromCurrency()
    {
        $this->assertTrue($this->exchangeRate->fromCurrency->is($this->currency1));
    }

    public function testToCurrency()
    {
        $this->assertTrue($this->exchangeRate->toCurrency->is($this->currency2));
    }

    public function testScopeForFromCurrency()
    {
        $exchangeRate = CurrencyExchangeRate::forFromCurrency($this->currency1)->first();
        $this->assertTrue($exchangeRate->is($this->exchangeRate));
        $this->assertEquals($exchangeRate->from_currency_id, $this->currency1->id);
    }

    public function testScopeForToCurrency()
    {
        $exchangeRate = CurrencyExchangeRate::forToCurrency($this->currency2)->first();
        $this->assertTrue($exchangeRate->is($this->exchangeRate));
        $this->assertEquals($exchangeRate->to_currency_id, $this->currency2->id);
    }
}
