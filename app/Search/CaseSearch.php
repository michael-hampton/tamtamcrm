<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Cases;
use App\Repositories\CaseRepository;
use App\Requests\SearchRequest;
use App\Transformations\CaseTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * TokenSearch
 */
class CaseSearch extends BaseSearch
{
    use CaseTransformable;

    /**
     * @var CaseRepository
     */
    private CaseRepository $case_repo;

    private Cases $model;

    /**
     * CaseSearch constructor.
     * @param CaseRepository $case_repo
     */
    public function __construct(CaseRepository $case_repo)
    {
        $this->case_repo = $case_repo;
        $this->model = $case_repo->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'created_at' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('cases.*');

        if ($request->has('status')) {
            $this->status('cases', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('category_id')) {
            $this->query->whereCategoryId($request->category_id);
        }

        if ($request->filled('priority_id')) {
            $this->query->wherePriorityId($request->priority_id);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $subscriptions = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->case_repo->paginateArrayResults($subscriptions, $recordsPerPage);
            return $paginatedResults;
        }

        return $subscriptions;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('cases.message', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $cases = $list->map(
            function (Cases $case) {
                return $this->transform($case);
            }
        )->all();

        return $cases;
    }
}
