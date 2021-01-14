<?php

namespace App\Http\Controllers;

use App\Factory\TaxRateFactory;
use App\Models\TaxRate;
use App\Repositories\Interfaces\TaxRateRepositoryInterface;
use App\Requests\SearchRequest;
use App\Requests\TaxRate\CreateTaxRateRequest;
use App\Requests\TaxRate\UpdateTaxRateRequest;
use App\Search\TaxRateSearch;
use App\Transformations\TaxRateTransformable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;

class TaxRateController extends Controller
{
    use TaxRateTransformable;

    /**
     * @var TaxRateRepositoryInterface
     */
    private $tax_rate_repo;

    /**
     * TaxRateController constructor.
     * @param TaxRateRepositoryInterface $tax_rate_repo
     */
    public function __construct(TaxRateRepositoryInterface $tax_rate_repo)
    {
        $this->tax_rate_repo = $tax_rate_repo;
    }

    public function index(SearchRequest $request)
    {
        $tax_rates =
            (new TaxRateSearch($this->tax_rate_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($tax_rates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateTaxRateRequest $request
     * @return Response
     */
    public function store(CreateTaxRateRequest $request)
    {
        $tax_rate = TaxRateFactory::create(auth()->user()->account_user()->account_id, auth()->user()->id);
        $this->tax_rate_repo->save($request->all(), $tax_rate);

        return response()->json($this->transformTaxRate($tax_rate));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTaxRateRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateTaxRateRequest $request, $id)
    {
        $taxRate = $this->tax_rate_repo->findTaxRateById($id);
        $taxRate = $this->tax_rate_repo->save($request->all(), $taxRate);
        return response()->json($this->transformTaxRate($taxRate));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function archive(int $id)
    {
        $tax_rate = $this->tax_rate_repo->findTaxRateById($id);
        $tax_rate->archive();
        return response()->json('deleted');
    }

    /**
     * @param int $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $tax_rate = TaxRate::withTrashed()->where('id', '=', $id)->first();
        $this->authorize('delete', $tax_rate);
        $tax_rate->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $tax_rate = TaxRate::withTrashed()->where('id', '=', $id)->first();
        $tax_rate->restoreEntity();
        return response()->json([], 200);
    }

}
