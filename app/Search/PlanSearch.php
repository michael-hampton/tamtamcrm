<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use App\Requests\SearchRequest;
use App\Transformations\PlanTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * TokenSearch
 */
class PlanSearch extends BaseSearch
{
    use PlanTransformable;

    /**
     * @var PlanRepository
     */
    private PlanRepository $plan_repo;

    private Plan $model;

    /**
     * PlanSearch constructor.
     * @param PlanRepository $plan_repository
     */
    public function __construct(PlanRepository $plan_repository)
    {
        $this->plan_repo = $plan_repository;
        $this->model = $plan_repository->getModel();
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

        $this->query = $this->model->select('plans.*');

        if ($request->has('status')) {
            $this->status('plans', $request->status);
        } else {
            $this->query->withTrashed();
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->query->byDate($request->input('start_date'), $request->input('end_date'));
        }

        $this->query->byAccount($account);

        $this->query->where('is_locked', '=', false);

        $this->orderBy($orderBy, $orderDir);

        $plans = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->plan_repo->paginateArrayResults($plans, $recordsPerPage);
            return $paginatedResults;
        }

        return $plans;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('plans.name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $plans = $list->map(
            function (Plan $plan) {
                return $this->transformPlan($plan);
            }
        )->all();

        return $plans;
    }
}
