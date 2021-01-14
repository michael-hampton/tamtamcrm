<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Requests\SearchRequest;
use App\Transformations\CategoryTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CategorySearch
 * @package App\Search
 */
class CategorySearch extends BaseSearch
{
    use CategoryTransformable;

    /**
     * @var CategoryRepository
     */
    private CategoryRepository $category_repo;

    private Category $model;

    /**
     * CategorySearch constructor.
     * @param CategoryRepository $category_repo
     */
    public function __construct(CategoryRepository $category_repo)
    {
        $this->category_repo = $category_repo;
        $this->model = $category_repo->getModel();
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

        $this->query = $this->model->select('categories.*');

        if ($request->has('status')) {
            $this->status('categories', $request->status);
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
            $paginatedResults = $this->category_repo->paginateArrayResults($categories, $recordsPerPage);
            return $paginatedResults;
        }

        return $categories;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('categories.name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $categories = $list->map(
            function (Category $category) {
                return $this->transformCategory($category);
            }
        )->all();

        return $categories;
    }
}
