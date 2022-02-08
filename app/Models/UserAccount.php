<?php

namespace App\Models;

use App\Contracts\OwnedModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int      $id
 * @property int      $user_id
 * @property int      $currency_id
 * @property float    $value
 * @property string   $created_at
 * @property string   $updated_at
 * @property User     $user
 * @property Currency $currency
 * @property \Illuminate\Database\Eloquent\Collection|UserAccountBuyTask[] $outgoingBuyTasks
 * @property \Illuminate\Database\Eloquent\Collection|UserAccountBuyTask[] $incomingBuyTasks
 */
class UserAccount extends Model implements OwnedModel
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
    protected $fillable = ['user_id', 'currency_id', 'value', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function outgoingBuyTasks()
    {
        return $this->hasMany(UserAccountBuyTask::class, 'user_account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incomingBuyTasks()
    {
        return $this->hasMany(UserAccountBuyTask::class, 'goal_user_account_id');
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', '=', $user->id);
    }

    public function scopeForCurrency(Builder $query, Currency $currency): Builder
    {
        return $query->where('currency_id', '=', $currency->id);
    }

    public function getOwner(): User
    {
        return $this->user;
    }
}
