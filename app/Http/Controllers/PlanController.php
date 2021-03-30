<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Repositories\PlanRepository;
use App\Requests\SearchRequest;
use App\Search\PlanSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlanController extends Controller
{
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(SearchRequest $request)
    {
        $plans =
            (new PlanSearch($this->plan_repository))->filter($request, auth()->user()->account_user()->account);

        return response()->json($plans);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Plan $plan
     * @return Response
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Plan $plan
     * @return Response
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Plan $plan
     * @return Response
     */
    public function destroy(Plan $plan)
    {
        //
    }
}
