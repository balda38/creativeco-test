<?php

namespace Tests\Stubs;

class CoingateClientWithErrorStub
{
    public function getCurrencies(): array
    {
        return [
            [
                // field 'title' is missing
                'symbol' => 'EUR',
                'disabled' => false,
            ],
        ];
    }

    public function getExchangeRates(): array
    {
        return [
            'USD' => [
                'EUR' => 'something else', // not numeric value
                'RUB' => 70.1,
            ],
            'EUR' => [
                'USD' => 1.2,
                'RUB' => 80.3,
            ],
            'RUB' => [
                'USD' => 0.014,
                'EUR' => 0.008,
            ],
        ];
    }
}
