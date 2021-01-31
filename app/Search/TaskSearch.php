<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Task;
use App\Repositories\TaskRepository;
use App\Requests\SearchRequest;
use App\Transformations\TaskTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

        $this->checkPermissions('taskcontroller.index', 'tasks');

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

    public function buildCurrencyReport(Request $request, Account $account)
    {
        $this->query = DB::table('tasks')
                         ->select(
                             DB::raw('count(*) as count, currencies.name, SUM(total) as total, SUM(balance) AS balance')
                         )
                         ->join('currencies', 'currencies.id', '=', 'invoices.currency_id')
                         ->where('currency_id', '<>', 0)
                         ->where('account_id', '=', $account->id)
                         ->groupBy('currency_id')
                         ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('tasks');

        if (!empty($request->input('group_by'))) {
            // assigned to, status, customer, project
            $this->query->select(
                DB::raw(
                    'count(*) as count, customers.name AS customer, task_statuses.name AS status, projects.name AS project, CONCAT(users.first_name," ",users.last_name) as assigned_to'
                )
            )
                        ->groupBy($request->input('group_by'));
        } else {
            $this->query->select(
                'customers.name AS customer', 'task_statuses.name AS status', 'projects.name AS project', 'timers.started_at', 'timers.stopped_at', 'tasks.name', 'tasks.description', 'tasks.due_date',
                DB::raw('CONCAT(first_name," ",last_name) as assigned_to')
            );
        }

        $this->query->join('customers', 'customers.id', '=', 'tasks.customer_id')
                    ->leftJoin('timers', 'timers.task_id', '=', 'tasks.id')
                    ->join('task_statuses', 'task_statuses.id', '=', 'tasks.task_status_id')
                    ->leftJoin('users', 'users.id', '=', 'tasks.assigned_to')
                    ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
                    ->where('tasks.account_id', '=', $account->id);

        $order = $request->input('orderByField');

        if ($order === 'status') {
            $this->query->orderBy('task_statuses.name', $request->input('orderByDirection'));
        } elseif ($order === 'project') {
            $this->query->orderBy('projects.name', $request->input('orderByDirection'));
        } elseif ($order === 'customer') {
            $this->query->orderBy('customers.name', $request->input('orderByDirection'));
        } elseif($order === 'started_at') {
            $this->query->orderBy('timers.started_at', $request->input('orderByDirection'));
        } elseif($order === 'stopped_at') {
            $this->query->orderBy('timers.stopped_at', $request->input('orderByDirection'));
        } else {
            $this->query->orderBy('tasks.' . $order, $request->input('orderByDirection'));
        }

        if(!empty($request->input('date_format'))) {
           $this->filterByDate($request->input('date_format'), 'tasks');
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request, 'tasks', 'due_date');
        }

        $rows = $this->query->get()->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->taskRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

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
