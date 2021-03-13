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
     * @param array $data
     * @param TaskStatus $task_status
     * @return TaskStatus
     */
    public function create(array $data, TaskStatus $task_status): TaskStatus
    {
        $task_status->fill($data);
        $task_status->save();
        return $task_status;
    }

    /**
     * @param array $data
     * @param TaskStatus $task_status
     * @return TaskStatus
     */
    public function update(array $data, TaskStatus $task_status): TaskStatus
    {
        $task_status->update($data);
        return $task_status;
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
}
