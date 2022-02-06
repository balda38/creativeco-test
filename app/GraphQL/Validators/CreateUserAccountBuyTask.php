<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

class CreateUserAccountBuyTask extends Validator
{
    public function rules(): array
    {
        return [
            'input.value' => ['required', 'numeric', 'gt:0'],
            'input.buy_before' => ['date', 'after:now'],
        ];
    }
}
