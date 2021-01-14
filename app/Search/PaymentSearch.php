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
        $list = $this->query->get();
        $payments = $list->map(
            function (Payment $payment) {
                return $this->transformPayment($payment);
            }
        )->all();

        return $payments;
    }

}
