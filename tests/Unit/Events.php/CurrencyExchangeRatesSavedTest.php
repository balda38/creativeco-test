<?php

namespace Tests\Unit\Events;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;

use App\Events\CurrencyExchangeRatesSaved;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExchangeRate extends TestCase
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
     * @var Currency
     */
    private $currency3;
    /**
     * @var CurrencyExchangeRate
     */
    private $exchangeRate1;
    /**
     * @var CurrencyExchangeRate
     */
    private $exchangeRate2;
    /**
     * @var CurrencyExchangeRatesSaved
     */
    private $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency1 = Currency::factory()->create();
        $this->currency2 = Currency::factory()->create();
        $this->currency3 = Currency::factory()->create();
        $this->exchangeRate1 = CurrencyExchangeRate::factory()->create([
            'from_currency_id' => $this->currency1->id,
            'to_currency_id' => $this->currency2->id,
        ]);
        $this->exchangeRate2 = CurrencyExchangeRate::factory()->create([
            'from_currency_id' => $this->currency1->id,
            'to_currency_id' => $this->currency3->id,
        ]);
        $this->event = new CurrencyExchangeRatesSaved([$this->exchangeRate1, $this->exchangeRate2]);
    }

    protected function tearDonw(): void
    {
        parent::tearDown();

        $this->currency1 = null;
        $this->currency2 = null;
        $this->currency3 = null;
        $this->exchangeRate1 = null;
        $this->event = null;
    }

    public function testIsEmpty()
    {
        $this->assertFalse($this->event->isEmpty());
    }

    public function testGetExchangeRates()
    {
        $this->assertEquals($this->event->getExchangeRates(), [$this->exchangeRate1, $this->exchangeRate2]);
    }

    public function testGetToCurrency()
    {
        $this->assertEquals($this->event->getToCurrency(), $this->exchangeRate1->fromCurrency);
    }
}
