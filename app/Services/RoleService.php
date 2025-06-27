<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public function fetchRoles()
    {
        return Role::query()->get();
    }
}
