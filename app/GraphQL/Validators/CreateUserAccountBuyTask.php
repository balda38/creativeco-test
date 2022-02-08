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
                function  ($attr, $value, $fail) use ($account) {
                    if (!$account) {
                        $fail('The user account with id: '.$value.' not found.');
                    }
                },
            ],
            'input.currency_id' => [
                'required',
                function ($attr, $value, $fail) use ($account) {
                    $currency = Currency::find($value);
                    if (!$currency) {
                        $fail('The currency with id: '.$value.' not found.');
                    } elseif ($account && (int) $value === $account->currency_id) {
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
