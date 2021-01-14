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

    public function createTaskStatus(array $orderStatusData): TaskStatus;

    public function updateTaskStatus(array $data): bool;

    public function listTaskStatuses(string $order = 'id', string $sort = 'desc', array $columns = ['*']): Collection;

    public function deleteTaskStatus(): bool;

    public function findTasks(): Collection;

    public function findByName(string $name);
}
