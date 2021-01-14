<?php

namespace App\Http\Controllers;

use App\Factory\PaymentTermsFactory;
use App\Models\PaymentTerms;
use App\Repositories\PaymentTermsRepository;
use App\Requests\PaymentTerms\StorePaymentTermsRequest;
use App\Requests\PaymentTerms\UpdatePaymentTermsRequest;
use App\Requests\SearchRequest;
use App\Search\PaymentTermsSearch;
use App\Traits\UploadableTrait;
use App\Transformations\PaymentTermsTransformable;
use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;

/**
 * Class PaymentTermsControllerController
 * @package App\Http\Controllers
 */
class PaymentTermsController extends Controller
{
    use DispatchesJobs;
    use UploadableTrait;
    use PaymentTermsTransformable;

    protected PaymentTermsRepository $payment_terms_repo;

    /**
     * GroupSettingController constructor.
     * @param PaymentTermsRepository $payment_terms_repo
     */
    public function __construct(PaymentTermsRepository $payment_terms_repo)
    {
        $this->payment_terms_repo = $payment_terms_repo;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $payment_terms = (new PaymentTermsSearch($this->payment_terms_repo))->filter(
            $request,
            auth()->user()->account_user()->account
        );

        return response()->json($payment_terms);
    }

    /**
     * @param StorePaymentTermsRequest $request
     * @return JsonResponse
     */
    public function store(StorePaymentTermsRequest $request)
    {
        $payment_terms = PaymentTermsFactory::create(auth()->user()->account_user()->account, auth()->user());
        $payment_terms = $this->payment_terms_repo->save($request->all(), $payment_terms);

        return response()->json($this->transformPaymentTerms($payment_terms));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        return response()->json($this->transformPaymentTerms($this->payment_terms_repo->findPaymentTermsById($id)));
    }

    /**
     * @param int $id
     * @param UpdatePaymentTermsRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdatePaymentTermsRequest $request)
    {
        $payment_terms = $this->payment_terms_repo->findPaymentTermsById($id);
        $payment_terms = $this->payment_terms_repo->save($request->all(), $payment_terms);
        return response()->json($this->transformPaymentTerms($payment_terms));
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function archive(int $id)
    {
        $payment_terms = $this->payment_terms_repo->findPaymentTermsById($id);
        $payment_terms->archive();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        $payment_terms = PaymentTerms::withTrashed()->where('id', '=', $id)->first();
        $payment_terms->deleteEntity($payment_terms);
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $payment_terms = PaymentTerms::withTrashed()->where('id', '=', $id)->first();
        $payment_terms->restore();
        return response()->json([], 200);
    }
}
