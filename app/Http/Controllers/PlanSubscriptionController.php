<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Repositories\PlanRepository;
use App\Repositories\PlanSubscriptionRepository;
use App\Requests\PlanSubscriptions\CreatePlanSubscription;
use App\Requests\PlanSubscriptions\UpdatePlanSubscription;
use App\Requests\SearchRequest;
use App\Search\PlanSubscriptionSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanSubscriptionController extends Controller
{
    /**
     * @var PlanRepository
     */
    private PlanSubscriptionRepository $plan_subscription_repository;

    /**
     * PlanSubscriptionController constructor.
     * @param PlanSubscriptionRepository $plan_subscription_repository
     */
    public function __construct(PlanSubscriptionRepository $plan_subscription_repository) {
        $this->plan_subscription_repository = $plan_subscription_repository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $plan_subscriptions =
            (new PlanSubscriptionSearch($this->plan_subscription_repository))->filter($request, auth()->user()->account_user()->account);

        return response()->json($plan_subscriptions);
    }

    /**
     * @param CreatePlanSubscription $request
     */
    public function store(CreatePlanSubscription $request)
    {
        //TODO
    }

    /**
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     */
    public function show(PlanSubscription $plan_subscription)
    {
        return response()->json($plan_subscription);
    }

    /**
     * @param UpdatePlanSubscription $request
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     */
    public function update(UpdatePlanSubscription $request, PlanSubscription $plan_subscription)
    {
        $plan_subscription = $this->plan_subscription_repository->update($request->all(), $plan_subscription);
        return response()->json($plan_subscription);
    }

    /**
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(PlanSubscription $plan_subscription)
    {
        $this->authorize('delete', $plan_subscription);
        $plan_subscription->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     */
    public function archive(PlanSubscription $plan_subscription)
    {
        $plan_subscription->archive();
        return response()->json([], 200);
    }

     /**
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     */
    public function renew(PlanSubscription $plan_subscription)
    {
        
        return response()->json([], 200);
    }

     /**
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     */
    public function cancel(PlanSubscription $plan_subscription)
    {
        
        return response()->json([], 200);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $plan = Plan::withTrashed()->where('id', '=', $id)->first();
        $plan->restoreEntity();
        return response()->json([], 200);
    }
}
