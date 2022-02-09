<?php

namespace App\GraphQL\Validators;

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
                'exists:currencies,id',
            ],
        ];
    }
}
