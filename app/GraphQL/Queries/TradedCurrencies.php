<?php

namespace App\GraphQL\Queries;

use App\Models\Currency;

class TradedCurrencies
{
    /**
     * @param null                 $_
     * @param array<string, mixed> $args
     */
    public function __invoke($_, array $args)
    {
        return Currency::isArchived(false)->orderBy('id')->get();
    }
}
