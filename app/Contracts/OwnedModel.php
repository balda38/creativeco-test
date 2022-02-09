<?php

namespace App\Contracts;

use App\Models\User;

interface OwnedModel
{
    /**
     * Return owner of the model.
     */
    public function getOwner(): User;
}
