<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories\Interfaces;

use App\Models\Permission;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface PermissionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     *
     * @param array $data
     * @return Permission
     * @return Permission
     */
    public function createPermission(array $data): Permission;

    /**
     *
     * @param int $id
     * @return Permission
     * @return Permission
     */
    public function findPermissionById(int $id): Permission;

    /**
     *
     * @param array $data
     * @return bool
     * @return bool
     */
    public function updatePermission(array $data): bool;

    /**
     *
     */
    public function deletePermissionById(): bool;

    /**
     *
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listPermissions($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection;
}
