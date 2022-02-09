<?php

namespace App\GraphQL\Validators;

use App\Models\Currency;

use Nuwave\Lighthouse\Validation\Validator;

class CreateUserAccount extends Validator
{
    public function rules(): array
    {
        return [
            'input.user_id' => [
                'required',
                'numeric',
                'gt:0',
                'exists:users,id',
            ],
            'input.currency_id' => [
                'required',
                'numeric',
                'gt:0',
                function ($attr, $value, $fail) {
                    $currency = Currency::find($value);
                    if (!$currency) {
                        $fail('The selected '.$attr.' is invalid.');
                    } elseif ($currency->archived) {
                        $fail('The selected '.$attr.' is archived.');
                    }
                },
            ],
        ];
    }
}
