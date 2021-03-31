<?php

namespace App\Http\Controllers;

use App\Factory\PlanFactory;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Repositories\PlanRepository;
use App\Requests\Plan\CreatePlanRequest;
use App\Requests\Plan\UpdatePlanRequest;
use App\Requests\SearchRequest;
use App\Search\PlanSearch;
use App\Transformations\PlanTransformable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlanController extends Controller
{
    use PlanTransformable;

    /**
     * @var PlanRepository
     */
    private PlanRepository $plan_repository;

    /**
     * PlanController constructor.
     * @param PlanRepository $plan_repository
     */
    public function __construct(PlanRepository $plan_repository)
    {
        $this->plan_repository = $plan_repository;
    }

    /**
     * @param SearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $plans =
            (new PlanSearch($this->plan_repository))->filter($request, auth()->user()->account_user()->account);

        return response()->json($plans);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(CreatePlanRequest $request)
    {
        $plan = $this->plan_repository->create(
            PlanFactory::create(auth()->user(), auth()->user()->account_user()->account),
            $request->all()
        );

        return response()->json($this->transformPlan($plan));
    }

    /**
     * @param Plan $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Plan $plan)
    {
        return response()->json($this->transformPlan($plan));
    }

    /**
     * @param Request $request
     * @param Plan $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $plan = $this->plan_repository->update($request->all(), $plan);
        return response()->json($this->transformPlan($plan));
    }

    /**
     * @param Plan $plan
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Plan $plan)
    {
        $this->authorize('delete', $plan);
        $plan->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param Plan $plan
     * @return JsonResponse
     */
    public function archive(Plan $plan)
    {
        $plan->archive();
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
