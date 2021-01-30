<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Requests\SearchRequest;
use App\Transformations\PaymentTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

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
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('gateway_id')) {
            $this->query->whereCompanyGatewayId($request->gateway_id);
        }

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

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

    public function buildCurrencyReport (Request $request, Account $account)
    {
        $this->query =!DB::table('payments')
             ->select(DB::raw('count(*) as count, currencies.name, SUM(amount) as amount'))
             ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
             ->where('currency_id', '<>', 0)
             ->where('account_id', '=', $account->id)
             ->groupBy('currency_id');
    }

    public function buildReport (Request $request, Account $account)
    {
        $this->query = DB::table('payments');
        
         if(!empty($request->input('group_by')) {
            $this->query->select(DB::raw('count(*) as count, customers.name AS customer, SUM(amount) as amount'))
            $this->query->groupBy($request->input('group_by'));
        } else {
            $this->query->select('customers.name AS customer, amount, number, date, reference_number');
        }
         $this->query->join('customers', 'customers.id', '=', 'invoices.customer_id')
         ->where('account_id', '=', $account->id)
         ->orderBy('payments.created_at');
       
             //$this->query->where('status', '<>', 1)
            
    }

    private function transformList()
    {
        $list = $this->query->get();
        $payments = $list->map(
            function (Payment $payment) {
                return $this->transformPayment($payment);
            }
        )->all();

        return $payments;
    }

}
