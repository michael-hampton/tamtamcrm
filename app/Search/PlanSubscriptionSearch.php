<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Repositories\PlanSubscriptionRepository;
use App\Requests\SearchRequest;
use App\Transformations\PlanSubscriptionTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * TokenSearch
 */
class PlanSubscriptionSearch extends BaseSearch
{
    use PlanSubscriptionTransformable;

    /**
     * @var PlanSubscriptionRepository
     */
    private PlanSubscriptionRepository $plan_subscription_repo;

    /**
     * @var Plan|PlanSubscription
     */
    private PlanSubscription $model;

    /**
     * PlanSubscriptionSearch constructor.
     * @param PlanSubscriptionRepository $plan_subscription_repository
     */
    public function __construct(PlanSubscriptionRepository $plan_subscription_repository)
    {
        $this->plan_subscription_repo = $plan_subscription_repository;
        $this->model = $plan_subscription_repository->getModel();
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

        $this->query = $this->model->select('plan_subscriptions.*', 'plans.name AS plan_name')->join(
            'plans',
            'plans.id',
            '=',
            'plan_subscriptions.plan_id'
        );


        if ($request->has('status')) {
            $this->status('plan_subscriptions', $request->status);
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

        $this->query->where('plan_subscriptions.name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $plan_subscriptions = $list->map(
            function (PlanSubscription $plan_subscription) {
                return $this->transformPlanSubscription($plan_subscription);
            }
        )->all();

        return $plan_subscriptions;
    }
}
