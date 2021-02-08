<?php

namespace App\Search;

use App\Models\Account;
use App\Models\PurchaseOrder;
use App\Repositories\PurchaseOrderRepository;
use App\Requests\SearchRequest;
use App\Transformations\PurchaseOrderTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSearch extends BaseSearch
{
    use PurchaseOrderTransformable;

    private PurchaseOrderRepository $poRepository;

    private PurchaseOrder $model;

    /**
     * purchase_ordersearch constructor.
     * @param PurchaseOrderRepository $poRepository
     */
    public function __construct(PurchaseOrderRepository $poRepository)
    {
        $this->poRepository = $poRepository;
        $this->model = $poRepository->getModel();
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
            $this->status('purchase_orders', $request->status);
        }

        if ($request->filled('company_id')) {
            $this->query->whereCompanyId($request->company_id);
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

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->checkPermissions('purchaseordercontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $pos = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->poRepository->paginateArrayResults($pos, $recordsPerPage);
            return $paginatedResults;
        }

        return $pos;
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
                $query->where('purchase_orders.number', 'like', '%' . $filter . '%')
                      ->orWhere('purchase_orders.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('purchase_orders.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('purchase_orders.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('purchase_orders.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('purchase_orders')
                 ->select(
                     DB::raw('count(*) as count, currencies.name, SUM(total) as total, SUM(balance) AS balance')
                 )
                 ->join('currencies', 'currencies.id', '=', 'purchase_orders.currency_id')
                 ->where('currency_id', '<>', 0)
                 ->where('account_id', '=', $account->id)
                 ->groupBy('currency_id')
                 ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('purchase_orders');

        if (!empty($request->input('group_by'))) {
            if (in_array($request->input('group_by'), ['date', 'due_date']) && !empty(
                $request->input(
                    'group_by_frequency'
                )
                )) {
                $this->addMonthYearToSelect('purchase_orders', $request->input('group_by'));
            }

            $this->query->addSelect(
                DB::raw(
                    'count(*) as count, companies.name AS company, SUM(total) as total, SUM(purchase_orders.balance) AS balance, purchase_orders.status_id AS status'
                )
            );

            $this->addGroupBy('purchase_orders', $request->input('group_by'), $request->input('group_by_frequency'));
        } else {
            $this->query->select(
                'total',
                'purchase_orders.balance',
                DB::raw('(purchase_orders.total * 1 / purchase_orders.exchange_rate) AS converted_amount'),
                DB::raw('(purchase_orders.balance * 1 / purchase_orders.balance) AS converted_balance'),
                'companies.name AS company',
                'companies.address_1',
                'companies.address_2',
                'companies.city',
                'companies.town',
                'companies.postcode',
                'purchase_orders.number',
                'discount_total',
                'po_number',
                'date',
                'due_date AS expiry_date',
                'partial',
                'partial_due_date',
                'purchase_orders.custom_value1 AS custom1',
                'purchase_orders.custom_value2 AS custom2',
                'purchase_orders.custom_value3 AS custom3',
                'purchase_orders.custom_value4 AS custom4',
                'shipping_cost',
                'tax_total',
                'purchase_orders.status_id AS status'
            );
        }

        $this->query->join('companies', 'companies.id', '=', 'purchase_orders.company_id')
                    ->where('purchase_orders.account_id', '=', $account->id);

        $order_by = $request->input('orderByField');

        if ($order_by === 'company') {
            $this->query->orderBy('companies.name', $request->input('orderByDirection'));
        } elseif (!empty($this->field_mapping[$order)) {
            $order = str_replace('$table', 'purchase_orders', $this->field_mapping[$order);
            $this->query->orderBy($order, $request->input('orderByDirection'));
        } elseif ($order_by !== 'status') {
            $this->query->orderBy('purchase_orders.' . $order_by, $request->input('orderByDirection'));
        }

        if (!empty($request->input('date_format'))) {
            $this->filterByDate($request->input('date_format'));
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $date_field = !empty($request->input('manual_date_field')) ? $request->input('manual_date_field') : 'date';
            $this->filterDates($request, 'purchase_orders', $date_field);
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
            return $this->poRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $pos = $list->map(
            function (PurchaseOrder $po) {
                return $this->transformPurchaseOrder($po);
            }
        )->all();

        return $pos;
    }
}
