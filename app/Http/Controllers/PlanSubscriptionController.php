<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Repositories\PlanRepository;
use App\Repositories\PlanSubscriptionRepository;
use App\Requests\PlanSubscriptions\CreatePlan;
use App\Requests\PlanSubscriptions\UpdatePlan;
use App\Requests\SearchRequest;
use App\Search\PlanSubscriptionSearch;
use Illuminate\Auth\Access\AuthorizationException;
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
    public function __construct(PlanSubscriptionRepository $plan_subscription_repository)
    {
        $this->plan_subscription_repository = $plan_subscription_repository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $plan_subscriptions =
            (new PlanSubscriptionSearch($this->plan_subscription_repository))->filter(
                $request,
                auth()->user()->account_user()->account
            );

        return response()->json($plan_subscriptions);
    }

    /**
     * @param CreatePlan $request
     */
    public function store(CreatePlan $request)
    {
        $plan = Plan::where('plan_id', '=', $request->input('plan_id'))->first();

        //TODO
//        newSubscription(
//            $request->input('name'),
//            $plan,
//            auth()->user()->account_user()->account,
//            $request->input('number_of_licences')
//        );
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
     * @param UpdatePlan $request
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     */
    public function update(UpdatePlan $request, PlanSubscription $plan_subscription)
    {
        $plan_subscription = $this->plan_subscription_repository->update($request->all(), $plan_subscription);
        return response()->json($plan_subscription);
    }

    /**
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     * @throws AuthorizationException
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
        $plan_subscription = $plan_subscription->renew();
        return response()->json($plan_subscription, 200);
    }

    /**
     * @param Request $request
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     */
    public function change(Request $request, PlanSubscription $plan_subscription)
    {
        $plan = Plan::where('id', '=', $request->input('plan'))->first();
        $plan_subscription = $plan_subscription->changePlan($plan);
        return response()->json($plan_subscription, 200);
    }

    /**
     * @param PlanSubscription $plan_subscription
     * @return JsonResponse
     */
    public function cancel(PlanSubscription $plan_subscription)
    {
        $plan_subscription = $plan_subscription->cancel(true);

        return response()->json($plan_subscription, 200);
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
