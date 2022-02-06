<?php

namespace App\GraphQL\Validators;

use App\Models\UserAccountBuyTask;

use Nuwave\Lighthouse\Validation\Validator;

class DeleteUserAccountBuyTask extends Validator
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                function ($attr, $value, $fail) {
                    if ($value && !($task = UserAccountBuyTask::find($value))) {
                        $fail('Buy task with '.$attr.': '.$value.' not found');
                    } elseif ($task->getIsCompleted()) {
                        $fail('Unable to delete completed task');
                    }
                },
            ],
        ];
    }
}
