<?php

namespace App\Common\CoingateParser;

abstract class Parser
{
    /**
     * Get operation of Coingate client needed to run.
     *
     * @see \Balda38\CoingateExchangeClient\Client
     */
    abstract protected static function getClientOperation() : string;

    abstract public static function parse() : void;

    final protected static function getData() : array
    {
        return app()->coingateClient->{static::getClientOperation()}();
    }
}
