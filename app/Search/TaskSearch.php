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
        $orderBy = !$request->column ? 'task_sort_order' : 'tasks.' . $request->column;
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
        } else {
            $this->query->withTrashed();
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
                             DB::raw(
                                 'count(*) as count, currencies.name, SUM(tasks.total) as total, SUM(tasks.balance) AS balance'
                             )
                         )
                         ->join('customers', 'customers.id', '=', 'tasks.customer_id')
                         ->join('currencies', 'currencies.id', '=', 'customers.currency_id')
                         ->where('customers.currency_id', '<>', 0)
                         ->where('tasks.account_id', '=', $account->id)
                         ->groupBy('customers.currency_id')
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
            );

            $this->query->addSelect(
                DB::raw(
                    "    CONCAT(
        LPAD(
            HOUR(
                SEC_TO_TIME(
                    SUM(
                        TIMESTAMPDIFF(
                            SECOND,
                            timers.started_at,
                            timers.stopped_at
                        )
                    )
                )
            ),
            2,
            0
        ),
        ':',
        LPAD(
            MINUTE(
                SEC_TO_TIME(
                    SUM(
                        TIMESTAMPDIFF(
                            SECOND,
                            timers.started_at,
                            timers.stopped_at
                        )
                    )
                )
            ),
            2,
            0
        ),
        ':',
        LPAD(
            SECOND(
                SEC_TO_TIME(
                    SUM(
                        TIMESTAMPDIFF(
                            SECOND,
                            timers.started_at,
                            timers.stopped_at
                        )
                    )
                )
            ),
            2,
            0
        )
    ) AS duration"
                )
            );

            $this->query->groupBy($request->input('group_by'));
        } else {
            $this->query->select(
                'customers.name AS customer',
                'customers.balance AS customer_balance',
                'billing.address_1',
                'billing.address_2',
                'billing.city',
                'billing.state_code AS state',
                'billing.zip',
                'billing_country.name AS country',
                'shipping.address_1 AS shipping_address_1',
                'shipping.address_2 AS shipping_address_2',
                'shipping.city AS shipping_city',
                'shipping.state_code AS shipping_town',
                'shipping.zip AS shipping_zip',
                'shipping_country.name AS shipping_country',
                'task_statuses.name AS status',
                'projects.name AS project',
                'timers.started_at',
                'timers.stopped_at',
                'tasks.name',
                'tasks.description',
                'tasks.due_date',
                DB::raw('CONCAT(first_name," ",last_name) as assigned_to'),
                DB::raw(
                    "CONCAT(
                                LPAD(
                                    HOUR(
                                        SEC_TO_TIME(
                                            TIMESTAMPDIFF(
                                                SECOND,
                                                timers.started_at,
                                                timers.stopped_at
                                            )
                                        )
                                    ),
                                    2,
                                    0
                                ),
                                ':',
                                LPAD(
                                    MINUTE(
                                        SEC_TO_TIME(
                                            TIMESTAMPDIFF(
                                                SECOND,
                                                timers.started_at,
                                                timers.stopped_at
                                            )
                                        )
                                    ),
                                    2,
                                    0
                                ),
                                ':',
                                LPAD(
                                    SECOND(
                                        SEC_TO_TIME(
                                            TIMESTAMPDIFF(
                                                SECOND,
                                                timers.started_at,
                                                timers.stopped_at
                                            )
                                        )
                                    ),
                                    2,
                                    0
                                )
                            ) AS duration"
                ),
                'tasks.custom_value1 AS custom1',
                'tasks.custom_value2 AS custom2',
                'tasks.custom_value3 AS custom3',
                'tasks.custom_value4 AS custom4',
            );
        }

        $this->query->join('customers', 'customers.id', '=', 'tasks.customer_id')
                    ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                    ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                    ->leftJoin('countries AS billing_country', 'billing_country.id', '=', 'billing.country_id')
                    ->leftJoin('countries AS shipping_country', 'shipping_country.id', '=', 'shipping.country_id')
                    ->leftJoin('timers', 'timers.task_id', '=', 'tasks.id')
                    ->join('task_statuses', 'task_statuses.id', '=', 'tasks.task_status_id')
                    ->leftJoin('users', 'users.id', '=', 'tasks.assigned_to')
                    ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
                    ->where('tasks.account_id', '=', $account->id);

        $order = $request->input('orderByField');

        if (!empty($order)) {
            if ($order === 'status') {
                $this->query->orderBy('task_statuses.name', $request->input('orderByDirection'));
            } elseif ($order === 'project') {
                $this->query->orderBy('projects.name', $request->input('orderByDirection'));
            } elseif (!empty($this->field_mapping[$order])) {
                $order = str_replace('$table', 'tasks', $this->field_mapping[$order]);
                $this->query->orderBy($order, $request->input('orderByDirection'));
            } elseif ($order === 'started_at') {
                $this->query->orderBy('timers.started_at', $request->input('orderByDirection'));
            } elseif ($order === 'stopped_at') {
                $this->query->orderBy('timers.stopped_at', $request->input('orderByDirection'));
            } elseif ($order === 'duration') {
                $this->query->orderByRaw(
                    'FLOOR(TIMESTAMPDIFF(MINUTE, timers.started_at, timers.stopped_at)/60) ' . $request->input(
                        'orderByDirection'
                    )
                );
            } else {
                $this->query->orderBy('tasks.' . $order, $request->input('orderByDirection'));
            }
        }


        if (!empty($request->input('date_format'))) {
            $params = explode('|', $request->input('date_format'));
            $table = in_array($params[0], ['started_at', 'stopped_at']) ? 'timers' : 'tasks';

            $this->filterByDate($request->input('date_format'), $table);
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
