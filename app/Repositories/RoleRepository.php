<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Models\Role;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Shop\Roles\Exceptions\CreateRoleErrorException;
use App\Shop\Roles\Exceptions\DeleteRoleErrorException;
use App\Shop\Roles\Exceptions\RoleNotFoundErrorException;
use App\Shop\Roles\Exceptions\UpdateRoleErrorException;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{

    /**
     * @var Role
     */
    protected $model;

    /**
     * RoleRepository constructor.
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        parent::__construct($role);
        $this->model = $role;
    }

    /**
     * List all Roles
     *
     * @param string $order
     * @param string $sort
     * @return Collection
     */
    public function listRoles(string $order = 'id', string $sort = 'desc'): Collection
    {
        return $this->all(['*'], $order, $sort);
    }

    /**
     * @param array $data
     * @return Role
     * @throws CreateRoleErrorException
     */
    public function createRole(array $data): Role
    {
        $role = new Role($data);
        $role->save();
        return $role;
    }

    /**
     * @param int $id
     *
     * @return Role
     * @throws RoleNotFoundErrorException
     */
    public function findRoleById(int $id): Role
    {
        try {
            return $this->findOneOrFail($id);
        } catch (QueryException $e) {
            throw new RoleNotFoundErrorException($e);
        }
    }

    /**
     * @param array $data
     *
     * @return bool
     * @throws UpdateRoleErrorException
     */
    public function updateRole(array $data): bool
    {
        return $this->update($data);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteRoleById(): bool
    {
        try {
            return $this->delete();
        } catch (QueryException $e) {
            throw new DeleteRoleErrorException($e);
        }
    }

    /**
     * @param Permission $permission
     */
    public function attachToPermission(Permission $permission)
    {
        $this->model->attachPermission($permission);
    }

    /**
     * @param Permission ...$permissions
     */
    public function attachToPermissions(...$permissions)
    {
        $this->model->attachPermissions($permissions);
    }

    /**
     * @param array $ids
     */
    public function syncPermissions(array $ids)
    {
        $this->model->syncPermissions($ids);
    }

    /**
     * @return Collection
     */
    public function listPermissions(): Collection
    {
        return $this->model->permissions()->get();
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchRole(string $text = null): Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchRole($text)->get();
    }

    public function save(array $data, Role $role): Role
    {
        $role->fill($data);
        $role->save();
        return $role->fresh();
    }

}
