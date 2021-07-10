<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Deal;
use App\Models\File;
use App\Repositories\DealRepository;
use App\Requests\SearchRequest;
use App\Transformations\DealTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DealSearch extends BaseSearch
{
    use DealTransformable;

    private $dealRepository;

    private Deal $model;

    /**
     * DealSearch constructor.
     * @param DealRepository $dealRepository
     */
    public function __construct(DealRepository $dealRepository)
    {
        $this->dealRepository = $dealRepository;
        $this->model = $dealRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'order_id' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query =
            $this->model->select('*');

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('customer_id')) {
            $this->query->byCustomer($request->customer_id);
        }

        if ($request->filled('project_id')) {
            $this->query->byProject($request->project_id);
        }

        if ($request->filled('task_status')) {
            $this->status('deals', $request->task_status_id, 'task_status_id');
        } else {
            $this->query->withTrashed();
        }

        if ($request->filled('task_type')) {
            $this->query->whereTaskType($request->task_type);
        }

        if ($request->filled('user_id')) {
            $this->query->byAssignee($request->user_id);
        }

        if ($request->filled('id')) {
            $this->query->byId($request->id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->query->byDate($request->input('start_date'), $request->input('end_date'));
        }

        $this->query->byAccount($account);

        $this->checkPermissions('dealcontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $deals = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->dealRepository->paginateArrayResults($deals, $recordsPerPage);
            return $paginatedResults;
        }

        return $deals;
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
                $query->where('name', 'like', '%' . $filter . '%')->orWhere('description', 'like', '%' . $filter . '%')
                      ->orWhere('rating', 'like', '%' . $filter . '%')
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
        $files = File::where('fileable_type', '=', 'App\Models\Deal')->get()->groupBy('fileable_id');

        $deals = $list->map(
            function (Deal $deal) use ($files) {
                return $this->transformDeal($deal, $files);
            }
        )->all();

        return $deals;
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('invoices')
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
        $this->query = DB::table('deals');

        if (!empty($request->input('group_by'))) {
            // assigned to, status, source_type, customer, project
            $this->query->select(
                DB::raw(
                    'count(*) as count, customers.name AS customer, task_statuses.name AS status, source_type.name AS source_type, projects.name AS project, CONCAT(users.first_name," ",users.last_name) as assigned_to, SUM(valued_at) AS valued_at'
                )
            )
                        ->groupBy($request->input('group_by'));
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
                'source_type.name AS source_type',
                'projects.name AS project',
                'valued_at',
                'deals.due_date',
                'deals.custom_value1 AS custom1',
                'deals.custom_value2 AS custom2',
                'deals.custom_value3 AS custom3',
                'deals.custom_value4 AS custom4',
                DB::raw('CONCAT(first_name," ",last_name) as assigned_to')
            );
        }

        $this->query->join('customers', 'customers.id', '=', 'deals.customer_id')
                    ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                    ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                    ->leftJoin('countries AS billing_country', 'billing_country.id', '=', 'billing.country_id')
                    ->leftJoin('countries AS shipping_country', 'shipping_country.id', '=', 'shipping.country_id')
                    ->leftJoin('source_type', 'source_type.id', '=', 'deals.source_type')
                    ->leftJoin('projects', 'projects.id', '=', 'deals.project_id')
                    ->join('task_statuses', 'task_statuses.id', '=', 'deals.task_status_id')
                    ->leftJoin('users', 'users.id', '=', 'deals.assigned_to')
                    ->where('deals.account_id', '=', $account->id);

        $order = $request->input('orderByField');

        if (!empty($order)) {
            if ($order === 'status') {
                $this->query->orderBy('task_statuses.name', $request->input('orderByDirection'));
            } elseif ($order === 'project') {
                $this->query->orderBy('projects.name', $request->input('orderByDirection'));
            } elseif ($order === 'source_type') {
                $this->query->orderBy('source_type.name', $request->input('orderByDirection'));
            } elseif ($order === 'customer') {
                $this->query->orderBy('customers.name', $request->input('orderByDirection'));
            } elseif (!empty($this->field_mapping[$order])) {
                $order = str_replace('$table', 'deals', $this->field_mapping[$order]);
                $this->query->orderBy($order, $request->input('orderByDirection'));
            } else {
                $this->query->orderBy('deals.' . $order, $request->input('orderByDirection'));
            }
        }


        if (!empty($request->input('date_format'))) {
            $this->filterByDate($request->input('date_format'), 'deals');
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request, 'deals', 'due_date');
        }

        $rows = $this->query->get()->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->dealRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

    }
}
