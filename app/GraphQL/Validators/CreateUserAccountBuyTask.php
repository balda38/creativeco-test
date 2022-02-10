<?php

namespace App\GraphQL\Validators;

use App\Models\UserAccount;
use App\Models\CurrencyExchangeRate;

use Nuwave\Lighthouse\Validation\Validator;

class CreateUserAccountBuyTask extends Validator
{
    public function rules(): array
    {
        $userAccount = UserAccount::find($this->arg('input.user_account_id'));

        return [
            'input.user_account_id' => [
                'required',
                'numeric',
                'gt:0',
                function ($attr, $value, $fail) use ($userAccount) {
                    if (!$userAccount) {
                        $fail('The selected '.$attr.' is invalid.');
                    } elseif ($userAccount->currency->archived) {
                        $fail('The selected '.$attr.' has archived currency.');
                    }
                },
            ],
            'input.goal_user_account_id' => [
                'required',
                'numeric',
                'gt:0',
                function ($attr, $value, $fail) use ($userAccount) {
                    $goalUserAccount = UserAccount::find($value);
                    if (!$goalUserAccount) {
                        $fail('The selected '.$attr.' is invalid.');
                    } else {
                        if ($goalUserAccount->currency->archived) {
                            $fail('The selected '.$attr.' has archived currency.');
                        }
                        if ($userAccount) {
                            if ($goalUserAccount->currency_id === $userAccount->currency_id) {
                                $fail('The '.$attr.' must be different from user account currency.');
                            } elseif (
                                !CurrencyExchangeRate::forFromCurrency($goalUserAccount->currency)
                                    ->forToCurrency($userAccount->currency)
                                    ->exists()
                            ) {
                                $fail('The '.$attr.' currency not be traded with user account currency.');
                            }
                        }
                    }
                },
            ],
            'input.value' => ['required', 'numeric', 'gt:0'],
            'input.count' => ['required', 'numeric', 'gt:0'],
            'input.buy_before' => ['date', 'after:now'],
        ];
    }
}
