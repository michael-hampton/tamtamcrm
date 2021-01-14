<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Group;
use App\Repositories\GroupRepository;
use App\Requests\SearchRequest;
use App\Transformations\GroupTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupSearch extends BaseSearch
{
    use GroupTransformable;

    private GroupRepository $group_setting_repo;

    private Group $model;

    /**
     * GroupSearch constructor.
     * @param GroupRepository $group_setting_repo
     */
    public function __construct(GroupRepository $group_setting_repo)
    {
        $this->group_setting_repo = $group_setting_repo;
        $this->model = $group_setting_repo->getModel();
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
            $this->status('groups', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        if ($request->has('group_id') && !empty($request->search_term)) {
            $this->where('id', '=', $request->search_term);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $groups = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->group_setting_repo->paginateArrayResults($groups, $recordsPerPage);
            return $paginatedResults;
        }

        return $groups;
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
        $groups = $list->map(
            function (Group $group) {
                return $this->transformGroup($group);
            }
        )->all();

        return $groups;
    }

    /**
     * @param $filters
     * @param int $account_id
     * @return mixed
     */
    public function filterBySearchCriteria($filters, int $account_id)
    {
        $this->query = $this->model->select('group_settings.*');
        foreach ($filters as $column => $value) {
            if (empty($value)) {
                continue;
            }

            if ($column === 'status_id') {
                $this->status($value);
                continue;
            }
            $this->query->where($column, '=', $value);
        }

        $this->addAccount($account_id);

        return $this->transformList();
    }

}
