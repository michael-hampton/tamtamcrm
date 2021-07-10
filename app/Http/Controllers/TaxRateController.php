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
use Illuminate\Http\JsonResponse;

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

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $tax_rates =
            (new TaxRateSearch($this->tax_rate_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($tax_rates);
    }

    /**
     * @param CreateTaxRateRequest $request
     * @return JsonResponse
     */
    public function store(CreateTaxRateRequest $request)
    {
        $tax_rate = TaxRateFactory::create(auth()->user()->account_user()->account_id, auth()->user()->id);
        $this->tax_rate_repo->create($request->all(), $tax_rate);

        return response()->json($this->transformTaxRate($tax_rate));
    }

    /**
     * @param UpdateTaxRateRequest $request
     * @param TaxRate $tax_rate
     * @return JsonResponse
     */
    public function update(UpdateTaxRateRequest $request, TaxRate $tax_rate)
    {
        $tax_rate = $this->tax_rate_repo->update($request->all(), $tax_rate);
        return response()->json($this->transformTaxRate($tax_rate));
    }

    /**
     * @param TaxRate $tax_rate
     * @return JsonResponse
     */
    public function archive(TaxRate $tax_rate)
    {
        $tax_rate->archive();
        return response()->json('deleted');
    }

    /**
     * @param TaxRate $tax_rate
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(TaxRate $tax_rate)
    {
        $this->authorize('delete', $tax_rate);
        $tax_rate->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id)
    {
        $tax_rate = TaxRate::withTrashed()->where('id', '=', $id)->first();
        $tax_rate->restoreEntity();
        return response()->json([], 200);
    }

}
