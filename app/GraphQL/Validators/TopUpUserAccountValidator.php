<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

class TopUpUserAccountValidator extends Validator
{
    public function rules() : array
    {
        return [
            'input.value' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
