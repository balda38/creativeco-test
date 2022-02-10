<?php

namespace Tests\Integration\GraphQL;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;

class CurrencyTest extends TestCase
{
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
     * @var Currency
     */
    private $archivedCurrency;
    /**
     * @var CurrencyExchangeRate
     */
    private $currencyExchangeRate1;
    /**
     * @var CurrencyExchangeRate
     */
    private $currencyExchangeRate2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->be($this->user, 'api');

        $this->currency1 = Currency::factory()->create();
        $this->currency2 = Currency::factory()->create();
        $this->currency3 = Currency::factory()->create();
        $this->archivedCurrency = Currency::factory()->create([
            'archived' => true,
        ]);
        $this->currencyExchangeRate1 = CurrencyExchangeRate::factory()->create([
            'from_currency_id' => $this->currency1->id,
            'to_currency_id' => $this->currency2->id,
        ]);
        $this->currencyExchangeRate2 = CurrencyExchangeRate::factory()->create([
            'from_currency_id' => $this->currency2->id,
            'to_currency_id' => $this->currency1->id,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->currency1 = null;
        $this->currency2 = null;
        $this->currency3 = null;
        $this->archivedCurrency = null;
        $this->currencyExchangeRate1 = null;
        $this->currencyExchangeRate2 = null;

    }

    public function testGetTradedCurrencies()
    {
        $response = $this->graphQL(/** @lang GraphQL */ "
            {
                tradedCurrencies {
                    id
                    exchangeRates {
                        id
                        fromCurrency {
                            id
                        }
                        toCurrency {
                            id
                        }
                    }
                }
            }
        ");
        $this->assertSame($response->json(), [
            'data' => [
                'tradedCurrencies' => [
                    [
                        'id' => (string) $this->currency1->id,
                        'exchangeRates' => [
                            [
                                'id' => (string) $this->currencyExchangeRate1->id,
                                'fromCurrency' => [
                                    'id' => (string) $this->currency1->id,
                                ],
                                'toCurrency' => [
                                    'id' => (string) $this->currency2->id,
                                ],
                            ],
                        ],
                    ],
                    [
                        'id' => (string) $this->currency2->id,
                        'exchangeRates' => [
                            [
                                'id' => (string) $this->currencyExchangeRate2->id,
                                'fromCurrency' => [
                                    'id' => (string) $this->currency2->id,
                                ],
                                'toCurrency' => [
                                    'id' => (string) $this->currency1->id,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
