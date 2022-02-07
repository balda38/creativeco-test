<?php

namespace App\Models;

use App\Contracts\OwnedModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $user_account_id
 * @property int         $currency_id
 * @property float       $value
 * @property float       $count
 * @property string      $created_at
 * @property string      $buy_before
 * @property string      $completed_at
 * @property UserAccount $userAccount
 * @property Currency    $currency
 */
class UserAccountBuyTask extends Model implements OwnedModel
{
    use HasFactory;

    const UPDATED_AT = null;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'currency_id',
        'value',
        'count',
        'created_at',
        'buy_before',
        'completed_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function scopeForUserAccount(Builder $query, UserAccount $userAccount): Builder
    {
        return $query->where('user_account_id', '=', $userAccount->id);
    }

    public function scopeForCurrency(Builder $query, Currency $currency): Builder
    {
        return $query->where('currency_id', '=', $currency->id);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('buy_before IS NOT NULL')
            ->andWhere('buy_before', '<=', Carbon::now());
    }

    public function getOwner(): User
    {
        return $this->userAccount->user;
    }

    public function getIsExpired(): bool
    {
        return Carbon::now()->gt(new Carbon($this->buy_before));
    }

    public function getIsCompleted(): bool
    {
        return !is_null($this->completed_at);
    }
}
