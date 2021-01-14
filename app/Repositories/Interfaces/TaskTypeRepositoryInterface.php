<?php

namespace App\Repositories\Interfaces;

use App\Models\TaskType;

interface TaskTypeRepositoryInterface
{
    public function getAll();

    /**
     *
     * @param int $id
     * @return TaskType
     * @return TaskType
     */
    public function findTaskTypeById(int $id): TaskType;
}
