<?php

namespace App\Factory;

use App\Models\Department;

class DepartmentFactory
{
    public static function create(int $account_id, int $user_id): Department
    {
        $department = new Department;
        $department->name = '';
        $department->account_id = $account_id;
        $department->user_id = $user_id;
        $department->department_manager = null;

        return $department;
    }
}
