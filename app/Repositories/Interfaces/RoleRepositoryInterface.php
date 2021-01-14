<?php

namespace App\Repositories\Interfaces;

use App\Models\Permission;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{

    /**
     *
     * @param string $order
     * @param string $sort
     * @return Collection
     * @return Collection
     */
    public function listRoles(string $order = 'id', string $sort = 'desc'): Collection;

    /**
     *
     * @param int $id
     */
    public function findRoleById(int $id);

    /**
     *
     */
    public function deleteRoleById(): bool;

    /**
     *
     * @param Permission $permission
     */
    public function attachToPermission(Permission $permission);

    /**
     *
     * @param mixed ...$permissions
     */
    public function attachToPermissions(...$permissions);

    /**
     *
     * @param array $ids
     */
    public function syncPermissions(array $ids);

    /**
     *
     */
    public function listPermissions(): Collection;
}
