<?php

namespace App\Events;

use App\Models\Currency;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CurrencyExchangeRatesSaved
{
    use Dispatchable, SerializesModels;

    /**
     * @var App\Models\CurrencyExchangeRate[]
     */
    protected $exchangeRates;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $exchangeRates)
    {
        $this->exchangeRates = $exchangeRates;
    }

    public function isEmpty(): bool
    {
        return empty($this->exchangeRates);
    }

    /**
     * @return App\Models\CurrencyExchangeRate[]
     */
    public function getExchangeRates(): array
    {
        return $this->exchangeRates;
    }

    public function getToCurrency(): Currency
    {
        return reset($this->exchangeRates)->fromCurrency;
    }
}
