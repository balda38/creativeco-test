<?php

namespace App\Models;

use App\Contracts\OwnedModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Carbon;

/**
 * Task on buy currency from user account.
 *
 * @property int         $id
 * @property int         $user_account_id      Account from which currency will be debited
 * @property int         $goal_user_account_id Account to which currency will be received
 * @property float       $value                Max value of curerncies exchange rate
 * @property float       $count                Count of purchased currency
 * @property string      $created_at
 * @property string      $buy_before           After this time purchase will not to be attempt to carried out
 * @property string      $completed_at
 * @property string      $canceled_at          Time when is one of accounts currency became archived
 * @property UserAccount $userAccount
 * @property UserAccount $goalUserAccount
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
        'completed_at',
        'canceled_at',
    ];

    /**
     * @var array
     */
    protected $dates = ['created_at', 'buy_before', 'completed_at', 'canceled_at'];

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
    public function goalUserAccount()
    {
        return $this->belongsTo(UserAccount::class);
    }

    public function scopeForUserAccount(Builder $query, UserAccount $userAccount): Builder
    {
        return $query->where('user_account_id', '=', $userAccount->id);
    }

    public function scopeForGoalUserAccount(Builder $query, UserAccount $userAccount): Builder
    {
        return $query->where('goal_user_account_id', '=', $userAccount->id);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('buy_before')
            ->where('buy_before', '<=', Carbon::now())
            ->whereNull('completed_at')
            ->whereNull('canceled_at');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeCanceled(Builder $query): Builder
    {
        return $query->whereNotNull('canceled_at');
    }

    public function scopeWaiting(Builder $query): Builder
    {
        return $query->whereNull('completed_at')
            ->whereNull('canceled_at')
            ->where(function (Builder $query) {
                $query->whereNull('buy_before')
                    ->orWhere('buy_before', '>', Carbon::now());
            });
    }

    public function getOwner(): User
    {
        return $this->userAccount->user;
    }

    public function getIsExpired(): bool
    {
        return !$this->completed_at &&
            !$this->canceled_at &&
            $this->buy_before && Carbon::now()->gte($this->buy_before);
    }

    public function getIsCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    public function getIsCancled(): bool
    {
        return !is_null($this->canceled_at);
    }

    public function getIsWaiting(): bool
    {
        return !$this->completed_at &&
            !$this->canceled_at &&
            !$this->buy_before || (
                $this->buy_before && Carbon::now()->lt($this->buy_before)
            );
    }

    public function getSum(): float
    {
        return $this->value * $this->count;
    }
}
