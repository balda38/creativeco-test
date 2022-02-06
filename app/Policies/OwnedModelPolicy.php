<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAccount;

use Illuminate\Auth\Access\HandlesAuthorization;

class OwnedModelPolicy
{
    use HandlesAuthorization;

    /**
     * Specifies that user can create models.
     */
    public function graphql(User $user, array $injectedArgs = null, array $staticArgs = null) : bool
    {
        if (is_array($staticArgs) && isset($staticArgs['queryByModel'])) {
            $queryByModel = $staticArgs['queryByModel'];
            switch ($queryByModel) {
                case class_basename(User::class):
                    return isset($injectedArgs['user_id']) && $user->id === (int) $injectedArgs['user_id'];
                case class_basename(UserAccount::class):
                    if (isset($injectedArgs['user_account_id'])) {
                        $userAccount = UserAccount::find($injectedArgs['user_account_id']);
                    }

                    return isset($userAccount) && $user->id === $userAccount->getOwner()->id;
            }
        }

        return false;
    }
}
