<?php

namespace App\Repositories;

use App\Models\TaskStatus;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\TaskStatusRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;

class TaskStatusRepository extends BaseRepository implements TaskStatusRepositoryInterface
{

    /**
     * TaskStatusRepository constructor.
     *
     * @param TaskStatus $taskStatus
     */
    public function __construct(TaskStatus $taskStatus)
    {
        parent::__construct($taskStatus);
        $this->model = $taskStatus;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getAll()
    {
        return $this->model->where('is_active', 1)->orderBy('id', 'asc')->get();
    }

    /**
     *
     * @param int $task_type
     * @return type
     */
    public function getAllStatusForTaskType(int $task_type)
    {
        return $this->model->where('is_active', 1)->where('task_type', $task_type)->orderBy('id', 'asc')->get();
    }

    /**
     * Create the order status
     *
     * @param array $params
     * @return TaskStatus
     */
    public function createTaskStatus(array $params): TaskStatus
    {
        return $this->create($params);
    }

    /**
     * @param array $data
     * @param TaskStatus $task_status
     * @return TaskStatus|null
     */
    public function save(array $data, TaskStatus $task_status): ?TaskStatus
    {
        $task_status->fill($data);
        $task_status->save();
        return $task_status;
    }

    /**
     * Update the order status
     *
     * @param array $data
     *
     * @return bool
     * @throws OrderStatusInvalidArgumentException
     */
    public function updateTaskStatus(array $data): bool
    {
        return $this->update($data);
    }

    /**
     * @param int $id
     * @return TaskStatus
     */
    public function findTaskStatusById(int $id): TaskStatus
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param string $order
     * @param string $sort
     * @param array $columns
     * @return mixed
     */
    public function listTaskStatuses(string $order = 'id', string $sort = 'desc', array $columns = ['*']): Collection
    {
        return $this->all($columns, $order, $sort);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteTaskStatus(): bool
    {
        return $this->delete();
    }

    /**
     * @return Collection
     */
    public function findTasks(): Collection
    {
        return $this->model->tasks()->get();
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function searchTaskStatus(string $text = null)
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchTaskStatus($text)->get();
    }
}
