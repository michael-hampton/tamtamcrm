<?php

namespace App\Search;

use App\Models\Account;
use App\Models\File;
use App\Models\Quote;
use App\Repositories\QuoteRepository;
use App\Requests\SearchRequest;
use App\Transformations\QuoteTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class QuoteSearch extends BaseSearch
{
    private QuoteRepository $quoteRepository;

    private Quote $model;

    /**
     * QuoteSearch constructor.
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
        $this->model = $quoteRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'due_date' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('status')) {
            $this->status('quotes', $request->status);
        } else {
            $this->query->withTrashed();
        }

        if ($request->filled('customer_id')) {
            $this->query->byCustomer($request->customer_id);
        }

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('id')) {
            $this->query->byId($request->id);
        }

        if ($request->filled('user_id')) {
            $this->query->byAssignee($request->user_id);
        }

        if ($request->filled('project_id')) {
            $this->query->byProject($request->project_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->query->byDate($request->input('start_date'), $request->input('end_date'));
        }

        $this->query->byAccount($account);

        $this->checkPermissions('quotecontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $quotes = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->quoteRepository->paginateArrayResults($quotes, $recordsPerPage);
            return $paginatedResults;
        }

        return $quotes;
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
                $query->where('quotes.number', 'like', '%' . $filter . '%')
                      ->orWhere('quotes.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('quotes.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('quotes.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('quotes.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->cacheFor(now()->addMonthNoOverflow())->cacheTags(['quotes'])->get();
        $files = File::where('fileable_type', '=', 'App\Models\Quote')->get()->groupBy('fileable_id');

        $quotes = $list->map(
            function (Quote $quote) use ($files) {
                return (new QuoteTransformable())->transformQuote($quote, $files);
            }
        )->all();

        return $quotes;
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('quotes')
                 ->select(
                     DB::raw(
                         'count(*) as count, currencies.name, SUM(quotes.total) as total, SUM(quotes.balance) AS balance'
                     )
                 )
                 ->join('customers', 'customers.id', '=', 'quotes.customer_id')
                 ->join('currencies', 'currencies.id', '=', 'customers.currency_id')
                 ->where('customers.currency_id', '<>', 0)
                 ->where('customers.account_id', '=', $account->id)
                 ->groupBy('customers.currency_id')
                 ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('quotes');

        if (!empty($request->input('group_by'))) {
            if (in_array($request->input('group_by'), ['date', 'due_date']) && !empty(
                $request->input(
                    'group_by_frequency'
                )
                )) {
                $this->addMonthYearToSelect('quotes', $request->input('group_by'));
            }

            $this->query->addSelect(
                DB::raw(
                    'count(*) as count, customers.name AS customer, SUM(total) as total, SUM(quotes.balance) AS balance, quotes.status_id AS status'
                )
            );

            $this->addGroupBy('quotes', $request->input('group_by'), $request->input('group_by_frequency'));
        } else {
            $this->query->select(
                'total',
                'quotes.balance',
                DB::raw('(quotes.total * 1 / quotes.exchange_rate) AS converted_amount'),
                DB::raw('(quotes.balance * 1 / quotes.balance) AS converted_balance'),
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
                'quotes.number',
                'discount_total',
                'po_number',
                'date',
                'due_date AS expiry_date',
                'partial',
                'partial_due_date',
                'quotes.custom_value1 AS custom1',
                'quotes.custom_value2 AS custom2',
                'quotes.custom_value3 AS custom3',
                'quotes.custom_value4 AS custom4',
                'shipping_cost',
                'tax_total',
                'quotes.status_id AS status'
            );
        }

        $this->query->join('customers', 'customers.id', '=', 'quotes.customer_id')
                    ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                    ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                    ->leftJoin('countries AS billing_country', 'billing_country.id', '=', 'billing.country_id')
                    ->leftJoin('countries AS shipping_country', 'shipping_country.id', '=', 'shipping.country_id')
                    ->where('quotes.account_id', '=', $account->id);

        $order_by = $request->input('orderByField');

        if (!empty($order_by)) {
            if (!empty($this->field_mapping[$order_by])) {
                $order = str_replace('$table', 'quotes', $this->field_mapping[$order_by]);
                $this->query->orderBy($order, $request->input('orderByDirection'));
            } elseif ($order_by !== 'status') {
                $this->query->orderBy('quotes.' . $order_by, $request->input('orderByDirection'));
            }
        }

        if (!empty($request->input('date_format'))) {
            $this->filterByDate($request->input('date_format'));
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $date_field = !empty($request->input('manual_date_field')) ? $request->input('manual_date_field') : 'date';
            $this->filterDates($request, 'quotes', $date_field);
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
            return $this->quoteRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

    }
}
