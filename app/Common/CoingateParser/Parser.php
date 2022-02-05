<?php

namespace App\Common\CoingateParser;

use App\Exceptions\CoingateParserException;

use Illuminate\Support\Facades\Validator;

abstract class Parser
{
    /**
     * Get operation of Coingate client needed to run.
     *
     * @see \Balda38\CoingateExchangeClient\Client
     */
    abstract protected static function getClientOperation() : string;

    /**
     * Validation rules for $data in process function.
     *
     * @see https://laravel.com/docs/8.x/validation
     */
    abstract protected static function validationRules() : array;

    abstract protected static function process(array $data) : void;

    final public static function parse() : void
    {
        $data = app()->coingateClient->{static::getClientOperation()}();
        if (self::validateCoingateData($data)) {
            static::process($data);
        } else {
            throw new CoingateParserException('Unsupported data structure!');
        }
    }

    private static function validateCoingateData(array $data) : bool
    {
        return !Validator::make($data, static::validationRules())->fails();
    }
}
