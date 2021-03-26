<?php

namespace App\Repositories\Interfaces;

use App\Models\AccountUser;
use App\Models\Department;
use App\Models\User;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param User $user
     * @param array $permissions
     * @return mixed
     */
    public function savePermissions(User $user, AccountUser $account_user, array $permissions);

    /**
     *
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listUsers($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection;

    /**
     * @param User $user
     * @param bool $delete_account
     * @return User
     */
    public function deleteUser(User $user, $delete_account = false): ?User;
    /**
     *
     * @param array $data
     */
    //public function updateUser(array $data) : bool;

    /**
     *
     * @param int $id
     * @return User
     * @return User
     */
    public function findUserById(int $id): User;

    /**
     *
     * @param array $data
     * @param User $user
     * @return User|null
     */
    //public function createUser(array $data) : User;

    public function save(array $data, User $user): ?User;

    /**
     *
     * @param string $username
     * @return User|null
     * @return User|null
     */
    public function findUserByUsername(string $username): ?User;

    /**
     *
     * @param Department $objDepartment
     * @return Collection
     * @return Collection
     */
    public function getUsersForDepartment(Department $objDepartment): Collection;
}
