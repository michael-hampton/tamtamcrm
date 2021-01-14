<?php

namespace App\Http\Controllers;

use App\Factory\CreditFactory;
use App\Models\Credit;
use App\Models\Customer;
use App\Repositories\Interfaces\CreditRepositoryInterface;
use App\Repositories\InvoiceRepository;
use App\Repositories\QuoteRepository;
use App\Requests\Credit\CreateCreditRequest;
use App\Requests\Credit\UpdateCreditRequest;
use App\Requests\SearchRequest;
use App\Search\CreditSearch;
use App\Services\CreditService;
use App\Transformations\CreditTransformable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionException;

/**
 * Class CreditController
 * @package App\Http\Controllers
 */
class CreditController extends BaseController
{
    use CreditTransformable;

    protected $credit_repo;

    /**
     * CreditController constructor.
     * @param CreditRepositoryInterface $credit_repo
     * @param InvoiceRepository $invoice_repo
     * @param QuoteRepository $quote_repo
     */
    public function __construct(
        CreditRepositoryInterface $credit_repo,
        InvoiceRepository $invoice_repo,
        QuoteRepository $quote_repo
    ) {
        $this->credit_repo = $credit_repo;
        parent::__construct($invoice_repo, $quote_repo, $credit_repo, 'Credit');
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $credits = (new CreditSearch($this->credit_repo))->filter($request, auth()->user()->account_user()->account);

        return response()->json($credits);
    }

    /**
     * @param UpdateCreditRequest $request
     * @param int $id
     * @return mixed
     */
    public function update(UpdateCreditRequest $request, int $id)
    {
        $credit = $this->credit_repo->findCreditById($id);
        $credit = $this->credit_repo->save($request->all(), $credit);

        return response()->json($this->transformCredit($credit));
    }

    /**
     * @param CreateCreditRequest $request
     * @return mixed
     */
    public function store(CreateCreditRequest $request)
    {
        $customer = Customer::find($request->input('customer_id'));
        $credit = $this->credit_repo->createCreditNote(
            $request->all(),
            CreditFactory::create(auth()->user()->account_user()->account, auth()->user(), $customer)
        );

        return response()->json($this->transformCredit($credit));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function archive(int $id)
    {
        $credit = $this->credit_repo->findCreditById($id);
        $credit->archive();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $credit = Credit::withTrashed()->where('id', '=', $id)->first();

        $this->authorize('delete', $credit);

        $credit->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $credit = Credit::withTrashed()->where('id', '=', $id)->first();
        $credit->restoreEntity();
        return response()->json([], 200);
    }

    public function show(int $id)
    {
        $credit = $this->credit_repo->findCreditById($id);
        return response()->json($this->transformCredit($credit));
    }

    /**
     * @param Request $request
     * @param Credit $credit
     * @param $action
     * @return mixed
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function action(Request $request, Credit $credit, $action)
    {
        return $this->performAction($request, $credit, $action);
    }

    public function getCreditsByStatus(int $status)
    {
        $invoices = $this->credit_repo->findCreditsByStatus($status);
        return response()->json($invoices);
    }
}
