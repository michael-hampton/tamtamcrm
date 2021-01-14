<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface DepartmentRepositoryInterface extends BaseRepositoryInterface
{

    /**
     *
     * @param string $order
     * @param string $sort
     * @return Collection
     * @return Collection
     */
    public function listDepartments(string $order = 'id', string $sort = 'desc'): Collection;

    /**
     *
     * @param int $id
     */
    public function findDepartmentById(int $id);

    /**
     *
     */
    public function deleteDepartment(): bool;
}
