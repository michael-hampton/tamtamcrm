<?php

namespace App\Repositories\Interfaces;

use App\Models\Department;
use App\Models\User;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     *
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listUsers($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection;

    /**
     *
     */
    public function deleteUser(): bool;

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
