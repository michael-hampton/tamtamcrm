<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Credit;
use App\Repositories\CreditRepository;
use App\Requests\SearchRequest;
use App\Transformations\CreditTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CreditSearch extends BaseSearch
{
    use CreditTransformable;

    private $credit_repo;

    private Credit $model;

    /**
     * CompanySearch constructor.
     * @param CreditRepository $credit_repository
     */
    public function __construct(CreditRepository $credit_repository)
    {
        $this->credit_repo = $credit_repository;
        $this->model = $credit_repository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'total' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->has('status')) {
            $this->status('credits', $request->status);
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

        $this->checkPermissions('creditcontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $companies = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->credit_repo->paginateArrayResults($companies, $recordsPerPage);
            return $paginatedResults;
        }

        return $companies;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('credits.number', 'like', '%' . $filter . '%')
                      ->orWhere('credits.number', 'like', '%' . $filter . '%')
                      ->orWhere('credits.date', 'like', '%' . $filter . '%')
                      ->orWhere('credits.total', 'like', '%' . $filter . '%')
                      ->orWhere('credits.balance', 'like', '%' . $filter . '%')
                      ->orWhere('credits.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('credits.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('credits.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('credits.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('credits')
                 ->select(
                     DB::raw(
                         'count(*) as count, currencies.name, SUM(credits.total) as total, SUM(credits.balance) AS balance'
                     )
                 )
                 ->join('customers', 'customers.id', '=', 'credits.customer_id')
                 ->join('currencies', 'currencies.id', '=', 'customers.currency_id')
                 ->where('customers.currency_id', '<>', 0)
                 ->where('credits.account_id', '=', $account->id)
                 ->groupBy('customers.currency_id')
                 ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('credits');

        if (!empty($request->input('group_by'))) {
            if (in_array($request->input('group_by'), ['date', 'due_date']) && !empty(
                $request->input(
                    'group_by_frequency'
                )
                )) {
                $this->addMonthYearToSelect('credits', $request->input('group_by'));
            }

            $this->query->addSelect(
                DB::raw(
                    'count(*) as count, customers.name AS customer, SUM(total) as total, SUM(credits.balance) AS balance, credits.status_id AS status'
                )
            );

            $this->addGroupBy('credits', $request->input('group_by'), $request->input('group_by_frequency'));
        } else {
            $this->query->select(
                'total',
                'credits.balance',
                DB::raw('(credits.total * 1 / credits.exchange_rate) AS converted_amount'),
                DB::raw('(credits.balance * 1 / credits.balance) AS converted_balance'),
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
                'credits.number',
                'discount_total',
                'po_number',
                'date',
                'due_date',
                'partial',
                'partial_due_date',
                'credits.custom_value1',
                'credits.custom_value2',
                'credits.custom_value3',
                'credits.custom_value4',
                'shipping_cost',
                'tax_total',
                'credits.status_id AS status'
            );
        }

        $this->query->join('customers', 'customers.id', '=', 'credits.customer_id')
                    ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                    ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                    ->leftJoin('countries AS billing_country', 'billing_country.id', '=', 'billing.country_id')
                    ->leftJoin('countries AS shipping_country', 'shipping_country.id', '=', 'shipping.country_id')
                    ->where('credits.account_id', '=', $account->id);

        $order_by = $request->input('orderByField');

        if (!empty($order_by)) {
            if (!empty($this->field_mapping[$order_by])) {
                $order = str_replace('$table', 'credits', $this->field_mapping[$order_by]);
                $this->query->orderBy($order, $request->input('orderByDirection'));
            } elseif ($order_by !== 'status') {
                $this->query->orderBy('credits.' . $order_by, $request->input('orderByDirection'));
            }
        }

        if (!empty($request->input('date_format'))) {
            $this->filterByDate($request->input('date_format'));
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $date_field = !empty($request->input('manual_date_field')) ? $request->input('manual_date_field') : 'date';
            $this->filterDates($request, 'credits', $date_field);
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
            return $this->credit_repo->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $credits = $list->map(
            function (Credit $credit) {
                return $this->transformCredit($credit);
            }
        )->all();

        return $credits;
    }
}
