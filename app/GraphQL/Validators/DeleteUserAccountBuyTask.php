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
                        $fail('The selected '.$attr.' is invalid.');
                    } elseif ($task->getIsCompleted()) {
                        $fail('Unable to delete completed task.');
                    }
                },
            ],
        ];
    }
}
