<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use App\Requests\SearchRequest;
use App\Transformations\InvoiceTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceSearch extends BaseSearch
{
    private InvoiceRepository $invoiceRepository;

    private Invoice $model;

    /**
     * InvoiceSearch constructor.
     * @param InvoiceRepository $invoiceRepository
     */
    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->model = $invoiceRepository->getModel();
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

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('status')) {
            $this->status('invoices', $request->status);
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

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->checkPermissions('invoicecontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $invoices = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->invoiceRepository->paginateArrayResults($invoices, $recordsPerPage);

            return $paginatedResults;
        }

        return $invoices;
    }

    /**
     * Filter based on search text
     *
     * @param string query filter
     * @return bool
     * @deprecated
     */
    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('invoices.number', 'like', '%' . $filter . '%')
                      ->orWhere('invoices.po_number', 'like', '%' . $filter . '%')
                      ->orWhere('invoices.date', 'like', '%' . $filter . '%')
                      ->orWhere('invoices.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('invoices.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('invoices.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('invoices.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
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
        $this->query = DB::table('invoices');

        if (!empty($request->input('group_by'))) {
            $this->query->select(
                DB::raw('count(*) as count, customers.name AS customer, SUM(total) as total, SUM(invoices.balance) AS balance')
            )
                        ->groupBy($request->input('group_by'));
        } else {
            $this->query->select('customers.name AS customer', 'total', 'invoices.number', 'invoices.balance', 'date', 'due_date');
        }

        $this->query->join('customers', 'customers.id', '=', 'invoices.customer_id')
                    ->where('invoices.account_id', '=', $account->id)
                    ->orderBy('invoices.'.$request->input('orderByField'), $request->input('orderByDirection'));
        //$this->query->where('status', '<>', 1)

        if(!empty($request->input('date_format'))) {
            $params = explode('|', $request->input('date_format'));
      
            if($params[0] === 'last_month') {
                $this->query->whereDate($params[0], '>', Carbon::now()->subMonth($params[1]));
            } elseif($params[0] === 'last_year') {
                $this->query->whereDate($params[0], '>', Carbon::now()->subYear($params[1]));
            } else {
                $this->query->whereDate($params[0], '>', Carbon::now()->subDays($params[1]));
            }
        }

        $rows = $this->query->get()->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->invoiceRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();

        $invoices = $list->map(
            function (Invoice $invoice) {
                return (new InvoiceTransformable())->transformInvoice($invoice);
            }
        )->all();

        return $invoices;
    }

}
