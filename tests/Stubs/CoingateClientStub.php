<?php

namespace Tests\Stubs;

class CoingateClientStub
{
    public function getCurrencies() : array
    {
        return [
            [
                'title' => 'Euro',
                'symbol' => 'EUR',
                'disabled' => false,
            ]
        ];
    }

    public function getExchangeRates() : array
    {
        return [
            'USD' => [
                'EUR' => 0.8,
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
