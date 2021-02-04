<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use App\Requests\SearchRequest;
use App\Transformations\ExpenseTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * ExpenseFilters
 */
class ExpenseSearch extends BaseSearch
{
    use ExpenseTransformable;

    private ExpenseRepository $expense_repo;

    private Expense $model;

    /**
     * CompanySearch constructor.
     * @param ExpenseRepository $expense_repo
     */
    public function __construct(ExpenseRepository $expense_repo)
    {
        $this->expense_repo = $expense_repo;
        $this->model = $expense_repo->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'amount' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('status')) {
            $this->status('expenses', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('company_id')) {
            $this->query->whereCompanyId($request->company_id);
        }

        if ($request->filled('expense_category_id')) {
            $this->query->whereExpenseCategoryId($request->expense_category_id);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->checkPermissions('expensecontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $expenses = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->expense_repo->paginateArrayResults($expenses, $recordsPerPage);
            return $paginatedResults;
        }

        return $expenses;
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
                $query->where('expenses.private_notes', 'like', '%' . $filter . '%')
                      ->orWhere('expenses.number', 'like', '%' . $filter . '%')
                      ->orWhere('expenses.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('expenses.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('expenses.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('expenses.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('expenses')
                         ->select(DB::raw('count(*) as count, currencies.name, SUM(amount) as amount'))
                         ->join('currencies', 'currencies.id', '=', 'expenses.currency_id')
                         ->where('currency_id', '<>', 0)
                         ->where('account_id', '=', $account->id)
                         ->groupBy('currency_id')
                         ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('expenses');

        if (!empty($request->input('group_by'))) {
            $this->query->select(
                DB::raw(
                    'count(*) as count, customers.name AS customer, companies.name AS company, expense_categories.name AS category, SUM(amount) as amount, expenses.status_id AS status'
                )
            );
            
            $this->addGroupBy($request);
        } else {
            $this->query->select('customers.name AS customer', 'companies.name AS company', 'expense_categories.name AS category', 'invoices.number AS invoice', 'amount', 'expenses.number', 'expenses.date', 'expenses.status_id AS status');
        }

        $this->query->join('customers', 'customers.id', '=', 'expenses.customer_id')
                    ->leftJoin('companies', 'companies.id', '=', 'expenses.company_id')
                    ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
                    ->leftJoin('invoices', 'invoices.id', '=', 'expenses.invoice_id')
                    ->where('expenses.account_id', '=', $account->id);

        $order_by = $request->input('orderByField');

        if ($order_by === 'customer') {
            $this->query->orderBy('customers.name', $request->input('orderByDirection'));
        } elseif($order_by === 'category') {
            $this->query->orderBy('expense_categories.name', $request->input('orderByDirection'));
        } elseif($order_by === 'invoice') {
            $this->query->orderBy('invoices.number', $request->input('orderByDirection'));
        } elseif ($order_by === 'company') {
            $this->query->orderBy('companies.name', $request->input('orderByDirection'));
        } elseif ($order_by !== 'status') {
            $this->query->orderBy('expenses.' . $order_by, $request->input('orderByDirection'));
        }

        if(!empty($request->input('date_format'))) {
           $this->filterByDate($request->input('date_format'));
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $date_field = !empty($request->input('manual_date_field')) ? $request->input('manual_date_field') : 'date';
            $this->filterDates($request, 'expenses', $date_field);
        }

        $rows = $this->query->get()->toArray();

        foreach ($rows as $key => $row) {
            $rows[$key]->status = $this->getStatus($this->model, $row->status);
        }

        if($order_by === 'status') {
            $collection = collect($rows);
            $rows = $request->input('orderByDirection') === 'asc' ? $collection->sortby('status')->toArray() : $collection->sortByDesc('status')->toArray();
        }

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->expense_repo->paginateArrayResults($rows, $request->input('perPage'));
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
        $expenses = $list->map(
            function (Expense $expense) {
                return $this->transformExpense($expense);
            }
        )->all();

        return $expenses;
    }
}
