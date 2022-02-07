<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencyTest extends TestCase
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
    private $archivedCurrency;
    /**
     * @var CurrencyExchangeRate
     */
    private $exchangeRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency1 = Currency::factory()->create();
        $this->currency2 = Currency::factory()->create();
        $this->archivedCurrency = Currency::factory()->create([
            'archived' => true,
        ]);
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
        $this->archivedCurrency = null;
        $this->exchangeRate = null;
    }

    public function testExchangeRates()
    {
        $this->assertCount(0, $this->currency2->exchangeRates);
        $this->assertCount(1, $this->currency1->exchangeRates);
        $this->assertTrue($this->currency1->exchangeRates->first()->is($this->exchangeRate));
    }

    public function testExchangeRatesReverse()
    {
        $this->assertCount(0, $this->currency1->exchangeRatesReverse);
        $this->assertCount(1, $this->currency2->exchangeRatesReverse);
        $this->assertTrue($this->currency2->exchangeRatesReverse->first()->is($this->exchangeRate));
    }

    public function testScopeIsArchived()
    {
        $archived = Currency::isArchived(true)->get();
        $notArchived = Currency::isArchived(false)->get();

        $this->assertCount(1, $archived);
        $this->assertCount(2, $notArchived);
    }
}
