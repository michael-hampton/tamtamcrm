<?php

namespace App\Transformations;

use App\Models\Permission;
use App\Shop\Cities\Exceptions\CityNotFoundException;
use App\Shop\Countries\Exceptions\CountryNotFoundException;
use App\Shop\Customers\Exceptions\CustomerNotFoundException;

trait PermissionTransformable
{

    /**
     * Transform the address
     *
     * @param Permission $permission
     * @return Permission
     */
    public function transformPermission(Permission $permission)
    {
        $obj = new Permission;
        $obj->id = $permission->id;
        $obj->name = $permission->name;
        $obj->description = $permission->description;

        return $obj;
    }

}
