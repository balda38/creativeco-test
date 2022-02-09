<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

class TopUpUserAccount extends Validator
{
    public function rules(): array
    {
        return [
            'input.id' => [
                'required',
                'numeric',
                'gt:0',
                'exists:user_accounts,id',
            ],
            'input.value' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
