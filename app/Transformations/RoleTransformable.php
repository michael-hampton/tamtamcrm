<?php

namespace App\Transformations;

use App\Models\Role;

trait RoleTransformable
{

    /**
     * Transform the role
     *
     * @param Role $role
     * @return Role
     */
    protected function transformRole(Role $role)
    {
        $prop = new Role;

        $prop->id = (int)$role->id;
        $prop->name = $role->name;
        $prop->description = $role->description;

        return $prop;
    }

}
