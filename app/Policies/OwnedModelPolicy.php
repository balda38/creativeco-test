<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserAccountBuyTask;

use Illuminate\Auth\Access\HandlesAuthorization;

class OwnedModelPolicy
{
    use HandlesAuthorization;

    /**
     * Specifies that user can manipulate models.
     */
    public function graphql(User $user, array $injectedArgs = null, array $staticArgs = null) : bool
    {
        if (is_array($staticArgs) && isset($staticArgs['queryByModel']) && isset($staticArgs['idArg'])) {
            $queryByModel = $staticArgs['queryByModel'];
            $idKey = $staticArgs['idArg'];
            switch ($queryByModel) {
                case class_basename(User::class):
                    return isset($injectedArgs[$idKey]) && $user->id === (int) $injectedArgs[$idKey];
                case class_basename(UserAccount::class):
                    if (isset($injectedArgs[$idKey])) {
                        $userAccount = UserAccount::find($injectedArgs[$idKey]);
                    }

                    return isset($userAccount) && $user->id === $userAccount->getOwner()->id;
                case class_basename(UserAccountBuyTask::class):
                    if (isset($injectedArgs[$idKey])) {
                        $userAccountBuyTask = UserAccountBuyTask::find($injectedArgs[$idKey]);
                    }

                    return isset($userAccountBuyTask) && $user->id === $userAccountBuyTask->getOwner()->id;
            }
        }

        return false;
    }
}
