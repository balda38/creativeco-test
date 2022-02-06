<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Exchange rate from one currency to other currency.
 *
 * @property int      $id
 * @property int      $from_currency_id
 * @property int      $to_currency_id
 * @property float    $value
 * @property string   $created_at
 * @property string   $updated_at
 * @property Currency $fromCurrency
 * @property Currency $toCurrency
 */
class CurrencyExchangeRate extends Model
{
    use HasFactory;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['from_currency_id', 'to_currency_id', 'value', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function scopeForFromCurrency(Builder $query, Currency $currency): Builder
    {
        return $query->where('from_currency_id', '=', $currency->id);
    }

    public function scopeForToCurrency(Builder $query, Currency $currency): Builder
    {
        return $query->where('to_currency_id', '=', $currency->id);
    }
}
