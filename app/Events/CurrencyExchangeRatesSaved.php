<?php

namespace App\Events;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event fired when currency exchange rates was changed.
 */
class CurrencyExchangeRatesSaved
{
    use Dispatchable, SerializesModels;

    /**
     * @var CurrencyExchangeRate[]
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
     * @return CurrencyExchangeRate[]
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
