<?php

namespace App\Search;

use App\Models\Account;
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
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->filled('project_id')) {
            $this->query->whereProjectId($request->project_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

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

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('quotes')
                 ->select(
                     DB::raw('count(*) as count, currencies.name, SUM(total) as total, SUM(balance) AS balance')
                 )
                 ->join('currencies', 'currencies.id', '=', 'quotes.currency_id')
                 ->where('currency_id', '<>', 0)
                 ->where('account_id', '=', $account->id)
                 ->groupBy('currency_id')
                 ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('quotes');

        if (!empty($request->input('group_by'))) {
            $this->query->select(
                DB::raw('count(*) as count, customers.name AS customer, SUM(total) as total, SUM(quotes.balance) AS balance')
            )
                        ->groupBy($request->input('group_by'));
        } else {
            $this->query->select('customers.name AS customer', 'total', 'quotes.number', 'quotes.balance', 'date', 'due_date AS expiry_date');
        }

        $this->query->join('customers', 'customers.id', '=', 'quotes.customer_id')
                    ->where('quotes.account_id', '=', $account->id);

        $order_by = $request->input('orderByField');

        if ($order_by === 'customer') {
            $this->query->orderBy('customers.name', $request->input('orderByDirection'));
        } else {
            $this->query->orderBy('quotes.' . $order_by, $request->input('orderByDirection'));
        }

        if (!empty($request->input('date_format'))) {
            $this->filterByDate($request->input('date_format'));
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $rows = $this->query->get()->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->quoteRepository->paginateArrayResults($rows, $request->input('perPage'));
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
        $quotes = $list->map(
            function (Quote $quote) {
                return (new QuoteTransformable())->transformQuote($quote);
            }
        )->all();

        return $quotes;
    }
}
