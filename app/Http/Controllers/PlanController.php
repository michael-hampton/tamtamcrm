<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Repositories\CreditRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\PlanRepository;
use App\Repositories\QuoteRepository;
use App\Requests\SearchRequest;
use App\Search\InvoiceSearch;
use App\Search\PlanSearch;
use Illuminate\Http\Request;

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
    public function __construct(PlanRepository $plan_repository) {
        $this->plan_repository = $plan_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        //
    }
}
