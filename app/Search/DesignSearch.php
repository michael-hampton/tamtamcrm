<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Design;
use App\Repositories\DesignRepository;
use App\Requests\SearchRequest;
use App\Transformations\DesignTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class DesignSearch extends BaseSearch
{
    use DesignTransformable;

    private $design_repo;

    private $model;

    /**
     * GroupSearch constructor.
     * @param DesignRepository $design_repo
     */
    public function __construct(DesignRepository $design_repo)
    {
        $this->design_repo = $design_repo;
        $this->model = $design_repo->getModel();
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
            $this->status('designs', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $groups = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->design_repo->paginateArrayResults($groups, $recordsPerPage);
            return $paginatedResults;
        }

        return $groups;
    }

    public function searchFilter(string $filter = '')
    {
        if (strlen($filter) == 0) {
            return $this->query;
        }

        $this->query->where('name', 'like', '%' . $filter . '%');
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $designs = $list->map(
            function (Design $design) {
                return $this->transformDesign($design);
            }
        )->all();

        return $designs;
    }

}
