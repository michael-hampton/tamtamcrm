<?php

namespace App\Search;

use App\Models\Account;
use App\Models\File;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Requests\SearchRequest;
use App\Transformations\PaymentTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PaymentSearch extends BaseSearch
{
    use PaymentTransformable;

    private PaymentRepository $paymentRepository;

    private Payment $model;

    /**
     * PaymentSearch constructor.
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
        $this->model = $paymentRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return array|LengthAwarePaginator
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'amount' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('status')) {
            $this->status('payments', $request->status);
        } else {
            $this->query->withTrashed();
        }

        if ($request->filled('customer_id')) {
            $this->query->byCustomer($request->customer_id);
        }

        if ($request->filled('gateway_id')) {
            $this->query->whereCompanyGatewayId($request->gateway_id);
        }

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->query->byDate($request->input('start_date'), $request->input('end_date'));
        }

        $this->query->byAccount($account);

        $this->checkPermissions('paymentcontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $payments = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->paymentRepository->paginateArrayResults($payments, $recordsPerPage);
            return $paginatedResults;
        }
        return $payments;
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
                $query->where('payments.amount', 'like', '%' . $filter . '%')
                      ->orWhere('payments.number', 'like', '%' . $filter . '%')
                      ->orWhere('payments.date', 'like', '%' . $filter . '%')
                      ->orWhere('payments.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('payments.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('payments.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('payments.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    private function transformList()
    {
        $list = $this->query->cacheFor(now()->addMonthNoOverflow())->cacheTags(['payments'])->get();
        $files = File::where('fileable_type', '=', 'App\Models\Payment')->get()->groupBy('fileable_id');

        $payments = $list->map(
            function (Payment $payment) use ($files) {
                return $this->transformPayment($payment, $files);
            }
        )->all();

        return $payments;
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('payments')
                 ->select(DB::raw('count(*) as count, currencies.name, SUM(payments.amount) as amount'))
                 ->join('customers', 'customers.id', '=', 'payments.customer_id')
                 ->join('currencies', 'currencies.id', '=', 'customers.currency_id')
                 ->where('customers.currency_id', '<>', 0)
                 ->where('payments.account_id', '=', $account->id)
                 ->groupBy('customers.currency_id')
                 ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('payments');

        if (!empty($request->input('group_by'))) {
            if (in_array($request->input('group_by'), ['date', 'due_date']) && !empty(
                $request->input(
                    'group_by_frequency'
                )
                )) {
                $this->addMonthYearToSelect('payments', $request->input('group_by'));
            }

            $this->query->addSelect(
                DB::raw('count(*) as count, customers.name AS customer, SUM(amount) as amount, status_id AS status')
            );

            $this->addGroupBy('payments', $request->input('group_by'), $request->input('group_by_frequency'));
        } else {
            $this->query->select(
                'payments.number',
                'payments.amount',
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
                'payments.date',
                'reference_number',
                'payments.custom_value1 AS custom1',
                'payments.custom_value2 AS custom2',
                'payments.custom_value3 AS custom3',
                'payments.custom_value4 AS custom4',
                'status_id AS status'
            );
        }
        $this->query->join('customers', 'customers.id', '=', 'payments.customer_id')
                    ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                    ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                    ->leftJoin('countries AS billing_country', 'billing_country.id', '=', 'billing.country_id')
                    ->leftJoin('countries AS shipping_country', 'shipping_country.id', '=', 'shipping.country_id')
                    ->where('payments.account_id', '=', $account->id);

        $order_by = $request->input('orderByField');

        if (!empty($order_by)) {
            if (!empty($this->field_mapping[$order_by])) {
                $order = str_replace('$table', 'payments', $this->field_mapping[$order_by]);
                $this->query->orderBy($order, $request->input('orderByDirection'));
            } elseif ($order_by !== 'status') {
                $this->query->orderBy('payments.' . $order_by, $request->input('orderByDirection'));
            }
        }


        if (!empty($request->input('date_format'))) {
            $this->filterByDate($request->input('date_format'));
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $date_field = !empty($request->input('manual_date_field')) ? $request->input('manual_date_field') : 'date';
            $this->filterDates($request, 'payments', $date_field);
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
            return $this->paymentRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

    }

}
