<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Task;
use App\Repositories\TaskRepository;
use App\Requests\SearchRequest;
use App\Transformations\TaskTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskSearch extends BaseSearch
{
    use TaskTransformable;

    private TaskRepository $taskRepository;

    private Task $model;

    /**
     * TaskSearch constructor.
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->model = $taskRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'task_sort_order' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query =
            $this->model->select('*', 'tasks.id as id')->leftJoin('task_user', 'tasks.id', '=', 'task_user.task_id');

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('project_id')) {
            $this->query->whereProjectId($request->project_id);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->filled('task_status')) {
            $this->status('tasks', $request->task_status, 'task_status_id');
        }

        if ($request->filled('task_type')) {
            $this->query->whereTaskType($request->task_type);
        }

        if ($request->filled('user_id')) {
            $this->query->where('task_user.user_id', '=', $request->user_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $this->query->groupBy('tasks.id');

        $tasks = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->taskRepository->paginateArrayResults($tasks, $recordsPerPage);
            return $paginatedResults;
        }

        return $tasks;
    }

    /**
     * @param string $filter
     * @return bool
     */
    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter . '%')
                      ->orWhere('description', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $tasks = $list->map(
            function (Task $task) {
                return $this->transformTask($task);
            }
        )->all();

        return $tasks;
    }

    /**
     * @param $filters
     * @param int $task_type
     * @param int $account_id
     * @return mixed
     */
    public function filterBySearchCriteria($filters, int $task_type, int $account_id)
    {
        $this->query = $this->model->select('tasks.id as id', 'tasks.*')
                                   ->leftJoin('task_user', 'tasks.id', '=', 'task_user.task_id');
        $this->query = $this->query->where('is_completed', 0)->where('task_type', $task_type)->where('parent_id', 0);

        foreach ($filters as $column => $value) {
            if (empty($value)) {
                continue;
            }

            if ($column === 'task_status' && $value === parent::STATUS_ARCHIVED) {
                $this->status($value);
                continue;
            }

            $this->query->where($column, '=', $value);
        }

        $this->addAccount($account_id);

        return $this->transformList();
    }

}
