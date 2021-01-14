<?php

namespace App\Factory;

use App\Models\Role;

class RoleFactory
{
    public static function create(int $account_id, int $user_id): Role
    {
        $role = new Role;
        $role->name = '';
        $role->description = '';
        $role->account_id = $account_id;
        $role->user_id = $user_id;

        return $role;
    }
}
