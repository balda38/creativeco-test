<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Joselfonseca\LighthouseGraphQLPassport\HasLoggedInTokens;
use Joselfonseca\LighthouseGraphQLPassport\MustVerifyEmailGraphQL;
use Laravel\Passport\HasApiTokens;

use Illuminate\Support\Carbon;

/**
 * @property int                                                    $id
 * @property string                                                 $name
 * @property string                                                 $email
 * @property string                                                 $email_verified_at
 * @property string                                                 $password
 * @property string                                                 $remember_token
 * @property Carbon                                                 $created_at
 * @property Carbon                                                 $updated_at
 * @property string                                                 $avatar
 * @property \Illuminate\Database\Eloquent\Collection|UserAccount[] $accounts
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasLoggedInTokens, MustVerifyEmailGraphQL;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(UserAccount::class);
    }
}
