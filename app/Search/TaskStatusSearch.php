<?php

namespace App\Search;

use App\Models\Account;
use App\Models\TaskStatus;
use App\Repositories\TaskStatusRepository;
use App\Requests\SearchRequest;
use App\Transformations\TaskStatusTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * TokenSearch
 */
class TaskStatusSearch extends BaseSearch
{
    use TaskStatusTransformable;

    /**
     * @var TaskStatusRepository
     */
    private TaskStatusRepository $task_status_repo;

    private TaskStatus $model;

    /**
     * CaseCategorySearch constructor.
     * @param TaskStatusRepository $task_status_repo
     */
    public function __construct(TaskStatusRepository $task_status_repo)
    {
        $this->task_status_repo = $task_status_repo;
        $this->model = $task_status_repo->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'created_at' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('task_statuses.*');

        if ($request->has('status')) {
            $this->status('task_statuses', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->has('task_type') && !empty($request->task_type)) {
            $this->query->whereTaskType($request->task_type);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $statuses = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->task_status_repo->paginateArrayResults($statuses, $recordsPerPage);
            return $paginatedResults;
        }

        return $statuses;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('task_statuses.name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $case_categories = $list->map(
            function (TaskStatus $task_status) {
                return $this->transformTaskStatus($task_status);
            }
        )->all();

        return $case_categories;
    }
}
