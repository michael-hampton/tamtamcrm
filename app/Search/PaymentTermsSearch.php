<?php

namespace App\Search;

use App\Models\Account;
use App\Models\PaymentTerms;
use App\Repositories\PaymentTermsRepository;
use App\Requests\SearchRequest;
use App\Transformations\PaymentTermsTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentTermsSearch extends BaseSearch
{
    use PaymentTermsTransformable;

    private PaymentTermsRepository $payment_terms_repo;

    private PaymentTerms $model;

    /**
     * GroupSearch constructor.
     * @param PaymentTermsRepository $payment_terms_repo
     */
    public function __construct(PaymentTermsRepository $payment_terms_repo)
    {
        $this->payment_terms_repo = $payment_terms_repo;
        $this->model = $payment_terms_repo->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'name' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('status')) {
            $this->status('payment_terms', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $payment_terms = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->payment_terms_repo->paginateArrayResults($payment_terms, $recordsPerPage);
            return $paginatedResults;
        }

        return $payment_terms;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $payment_terms = $list->map(
            function (PaymentTerms $payment_term) {
                return $this->transformPaymentTerms($payment_term);
            }
        )->all();

        return $payment_terms;
    }
}
