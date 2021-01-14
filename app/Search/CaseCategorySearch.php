<?php

namespace App\Search;

use App\Models\Account;
use App\Models\CaseCategory;
use App\Repositories\CaseCategoryRepository;
use App\Requests\SearchRequest;
use App\Transformations\CaseCategoryTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * TokenSearch
 */
class CaseCategorySearch extends BaseSearch
{
    use CaseCategoryTransformable;

    /**
     * @var CaseCategoryRepository
     */
    private CaseCategoryRepository $case_category_repo;

    private CaseCategory $model;

    /**
     * CaseCategorySearch constructor.
     * @param CaseCategoryRepository $case_category_repo
     */
    public function __construct(CaseCategoryRepository $case_category_repo)
    {
        $this->case_category_repo = $case_category_repo;
        $this->model = $case_category_repo->getModel();
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

        $this->query = $this->model->select('case_categories.*');

        if ($request->has('status')) {
            $this->status('case_categories', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $categories = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->case_category_repo->paginateArrayResults($categories, $recordsPerPage);
            return $paginatedResults;
        }

        return $categories;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('case_categories.name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $case_categories = $list->map(
            function (CaseCategory $case_category) {
                return $this->transformCategory($case_category);
            }
        )->all();

        return $case_categories;
    }
}
