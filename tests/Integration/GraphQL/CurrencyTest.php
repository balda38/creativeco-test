<?php

namespace Tests\Integration\GraphQL;

use App\Models\Currency;

class CurrencyTest extends TestCase
{
    /**
     * @var Currency
     */
    private $currency;
    /**
     * @var Currency
     */
    private $archivedCurrency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->be($this->user, 'api');

        $this->currency = Currency::factory()->create();
        $this->archivedCurrency = Currency::factory()->create([
            'archived' => true,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->currency = null;
        $this->archivedCurrency = null;
    }

    public function testGetTradedCurrencies()
    {
        $this->graphQL(/** @lang GraphQL */ "
            {
                tradedCurrencies {
                    id
                    code
                }
            }
        ")->assertJson([
            'data' => [
                'tradedCurrencies' => [
                    [
                        'id' => $this->currency->id,
                        'code' => $this->currency->code,
                    ],
                ],
            ],
        ]);
    }
}
