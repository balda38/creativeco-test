<?php

namespace App\GraphQL\Mutations;

use App\Models\UserAccount;

class TopUpUserAccount
{
    /**
     * @param null                 $_
     * @param array<string, mixed> $args
     */
    public function __invoke($_, array $args)
    {
        $account = UserAccount::find($args['user_account_id']);
        $account->value += $args['value'];
        $account->save();

        return $account->toArray();
    }
}
