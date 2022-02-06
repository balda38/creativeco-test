<?php

namespace App\Contracts;

use App\Models\User;

interface OwnedModel
{
    public function getOwner(): User;
}
