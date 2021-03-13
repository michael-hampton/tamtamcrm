<?php

namespace App\Repositories\Interfaces;

use App\Models\TaskStatus;
use Illuminate\Support\Collection;

interface TaskStatusRepositoryInterface
{
    public function getAll();

    /**
     *
     * @param int $task_type
     */
    public function getAllStatusForTaskType(int $task_type);

    /**
     * @param array $data
     * @param TaskStatus $task_status
     * @return TaskStatus
     */
    public function create(array $data, TaskStatus $task_status): TaskStatus;

    /**
     * @param array $data
     * @param TaskStatus $task_status
     * @return TaskStatus
     */
    public function update(array $data, TaskStatus $task_status): TaskStatus;

    public function findTasks(): Collection;

    public function findByName(string $name);
}
