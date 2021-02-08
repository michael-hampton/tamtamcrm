<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Order;
use App\Models\Task;
use App\Repositories\OrderRepository;
use App\Repositories\Support;
use App\Requests\SearchRequest;
use App\Transformations\OrderTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderSearch extends BaseSearch
{
    use OrderTransformable;

    private OrderRepository $orderRepository;

    private Order $model;

    /**
     * OrderSearch constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->model = $orderRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'due_date' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->has('status')) {
            $this->status('product_task', $request->status);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('project_id')) {
            $this->query->whereProjectId($request->project_id);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->checkPermissions('ordercontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $orders = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->orderRepository->paginateArrayResults($orders, $recordsPerPage);
            return $paginatedResults;
        }

        return $orders;
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
                $query->where('number', 'like', '%' . $filter . '%')
                      ->orWhere('product_task.po_number', 'like', '%' . $filter . '%')
                      ->orWhere('product_task.date', 'like', '%' . $filter . '%')
                      ->orWhere('product_task.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('product_task.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('product_task.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('product_task.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('product_task')
                 ->select(
                     DB::raw(
                         'count(*) as count, currencies.name, SUM(product_task.total) as total, SUM(product_task.balance) AS balance'
                     )
                 )
                 ->join('customers', 'customers.id', '=', 'product_task.customer_id')
                 ->join('currencies', 'currencies.id', '=', 'customers.currency_id')
                 ->where('customers.currency_id', '<>', 0)
                 ->where('product_task.account_id', '=', $account->id)
                 ->groupBy('customers.currency_id')
                 ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('product_task');

        if (!empty($request->input('group_by'))) {
            if (in_array($request->input('group_by'), ['date', 'due_date']) && !empty(
                $request->input(
                    'group_by_frequency'
                )
                )) {
                $this->addMonthYearToSelect('product_task', $request->input('group_by'));
            }

            $this->query->addSelect(
                DB::raw(
                    'count(*) as count, customers.name AS customer, SUM(total) as total, SUM(product_task.balance) AS balance, product_task.status_id AS status'
                )
            );

            $this->addGroupBy('product_task', $request->input('group_by'), $request->input('group_by_frequency'));
        } else {
            $this->query->select(
                'total',
                'product_task.balance',
                DB::raw('(product_task.total * 1 / product_task.exchange_rate) AS converted_amount'),
                DB::raw('(product_task.balance * 1 / product_task.balance) AS converted_balance'),
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
                'product_task.number',
                'discount_total',
                'po_number',
                'date',
                'due_date',
                'partial',
                'partial_due_date',
                'product_task.custom_value1',
                'product_task.custom_value2',
                'product_task.custom_value3',
                'product_task.custom_value4',
                'shipping_cost',
                'tax_total',
                'product_task.status_id AS status'
            );
        }

        $this->query->join('customers', 'customers.id', '=', 'product_task.customer_id')
                    ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                    ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                    ->leftJoin('countries AS billing_country', 'billing_country.id', '=', 'billing.country_id')
                    ->leftJoin('countries AS shipping_country', 'shipping_country.id', '=', 'shipping.country_id')
                    ->where('product_task.account_id', '=', $account->id);

        $order_by = $request->input('orderByField');

        if (!empty($order_by)) {
            if ($order_by === 'customer') {
                $this->query->orderBy('customers.name', $request->input('orderByDirection'));
               } elseif (!empty($this->field_mapping[$order])) {
                $order = str_replace('$table', 'product_task', $this->field_mapping[$order]);
                $this->query->orderBy($order, $request->input('orderByDirection'));
            } elseif ($order_by !== 'status') {
                $this->query->orderBy('product_task.' . $order_by, $request->input('orderByDirection'));
            }
        }

        if (!empty($request->input('date_format'))) {
            $this->filterByDate($request->input('date_format'));
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $date_field = !empty($request->input('manual_date_field')) ? $request->input('manual_date_field') : 'date';
            $this->filterDates($request, 'product_task', $date_field);
        }

        $rows = $this->query->get()->toArray();

        foreach ($rows as $key => $row) {
            $rows[$key]->status = $this->getStatus($this->model, $row->status);
        }

        if ($order_by === 'status') {
            $collection = collect($rows);
            $rows = $request->input('orderByDirection') === 'asc' ? $collection->sortby('status')->toArray(
            ) : $collection->sortByDesc('status')->toArray();
        }

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->orderRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

    }

    private function transformList()
    {
        $list = $this->query->get();

        $orders = $list->map(
            function (Order $order) {
                return $this->transformOrder($order);
            }
        )->all();
        return $orders;
    }

    /**
     * @param Task $task
     * @return Support
     */
    public function filterByTask(Task $task)
    {
        $this->baseQuery();
        $this->addTaskToQuery($task);
        return $this->transformList();
    }

    private function baseQuery()
    {
        $this->query = $this->model->join('products', 'products.id', '=', 'product_task.product_id')
                                   ->select('product_task.*', 'products.price', 'product_task.id as order_id');
    }

    /**
     * @param Task $objTask
     */
    private function addTaskToQuery(Task $objTask)
    {
        $this->baseQuery();
        $this->query->where('product_task.task_id', $objTask->id);
    }

    /**
     * @param Task $objTask
     * @param int $status
     * @return mixed
     */
    public function getProductsForTask(Task $objTask, $status)
    {
        $this->baseQuery();
        $this->addTaskToQuery($objTask);
        $this->addStatusToQuery($status);

        return $this->transformList();
    }

    /**
     * @param $filter
     * @return mixed
     */
    private function addStatusToQuery($filter)
    {
        if (strlen($filter) == 0) {
            return $this->query;
        }

        $filters = explode(',', $filter);

        $this->query->whereIn('product_task.status', $filters);
    }
}
