<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Traits;

/**
 * Description of HasPermissionsTrait
 *
 * @author michael.hampton
 */
trait HasPermissionsTrait
{

    public function userHasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }

        return false;
    }

    public function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermissionTo($permission)
    {
        return $this->userHasPermission($permission) || $this->checkIfRoleHasPermission($permission);
    }

    protected function userHasPermission($permission)
    {
        return (bool)$this->permissions->where('name', $permission)->count();
    }

    /**
     *
     * @param type $permission
     * @return boolean
     */
    protected function checkIfRoleHasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ((bool)$role->permissions->where('name', $permission)->count()) {
                return true;
            }
        }

        return false;
    }

    public function givePermissionsTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        dd($permissions);
        if ($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    public function deletePermissions(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }

}
