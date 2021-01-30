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
        $this->query = DB::table('orders')
                         ->select(
                             DB::raw('count(*) as count, currencies.name, SUM(total) as total, SUM(balance) AS balance')
                         )
                         ->join('currencies', 'currencies.id', '=', 'orders.currency_id')
                         ->where('currency_id', '<>', 0)
                         ->where('account_id', '=', $account->id)
                         ->groupBy('currency_id');
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('orders');

        if (!empty($request->input('group_by'))) {
            $this->query->select(
                DB::raw('count(*) as count, customers.name AS customer, SUM(total) as total, SUM(balance) AS balance')
            )
                        ->groupBy($request->input('group_by'));
        } else {
            $this->query->select('customers.name AS customer, total, number, balance, date, due_date');
        }

        $this->query->join('customers', 'customers.id', '=', 'orders.customer_id')
                    ->where('account_id', '=', $account->id)
                    ->orderBy('invoices.created_at');
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
