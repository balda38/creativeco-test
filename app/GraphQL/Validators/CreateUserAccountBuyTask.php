<?php

namespace App\GraphQL\Validators;

use App\Models\UserAccount;

use Nuwave\Lighthouse\Validation\Validator;

class CreateUserAccountBuyTask extends Validator
{
    public function rules(): array
    {
        $account = UserAccount::find($this->arg('input.user_account_id'));

        return [
            'input.user_account_id' => [
                'required',
                'numeric',
                'gt:0',
                function  ($attr, $value, $fail) use ($account) {
                    if (!$account) {
                        $fail('The selected '.$attr.' is invalid.');
                    }
                },
            ],
            'input.goal_user_account_id' => [
                'required',
                'numeric',
                'gt:0',
                function ($attr, $value, $fail) use ($account) {
                    $goalUserAccount = UserAccount::find($value);
                    if (!$goalUserAccount) {
                        $fail('The selected '.$attr.' is invalid.');
                    } elseif ($account && $goalUserAccount->currency_id === $account->currency_id) {
                        $fail('The '.$attr.' must be different from account currency.');
                    }
                },
            ],
            'input.value' => ['required', 'numeric', 'gt:0'],
            'input.count' => ['required', 'numeric', 'gt:0'],
            'input.buy_before' => ['date', 'after:now'],
        ];
    }
}
