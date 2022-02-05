<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int                     $id
 * @property string                  $name
 * @property string                  $code
 * @property bool                    $archived
 * @property string                  $created_at
 * @property string                  $updated_at
 * @property CurrencyExchangeRates[] $exchangeRates
 * @property CurrencyExchangeRates[] $exchangeRatesReverse
 */
class Currency extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['name', 'code', 'archived', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exchangeRates()
    {
        return $this->hasMany(CurrencyExchangeRates::class, 'from_currency_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exchangeRatesReverse()
    {
        return $this->hasMany(CurrencyExchangeRates::class, 'to_currency_id');
    }

    public function scopeIsArchived(Builder $query, bool $archived) : Builder
    {
        return $query->where('archived', '=', $archived);
    }
}
