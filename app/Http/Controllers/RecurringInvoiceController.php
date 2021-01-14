<?php

namespace App\Http\Controllers;

use App\Factory\CloneRecurringInvoiceFactory;
use App\Factory\CloneRecurringInvoiceToQuoteFactory;
use App\Factory\RecurringInvoiceFactory;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\RecurringInvoice;
use App\Notifications\ClientContactRequestCancellation;
use App\Repositories\CreditRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\QuoteRepository;
use App\Repositories\RecurringInvoiceRepository;
use App\Requests\RecurringInvoice\CreateRecurringInvoiceRequest;
use App\Requests\SearchRequest;
use App\Search\RecurringInvoiceSearch;
use App\Transformations\RecurringInvoiceTransformable;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionException;

/**
 * Class RecurringInvoiceController
 * @package App\Http\Controllers\RecurringInvoiceController
 */
class RecurringInvoiceController extends BaseController
{
    use RecurringInvoiceTransformable;

    /**
     * @var RecurringInvoiceRepository
     */
    private RecurringInvoiceRepository $recurring_invoice_repo;

    /**
     * RecurringInvoiceController constructor.
     * @param RecurringInvoiceRepository $recurring_invoice_repo
     * @param InvoiceRepositoryInterface $invoice_repo
     * @param QuoteRepository $quote_repo
     * @param CreditRepository $credit_repo
     */
    public function __construct(
        RecurringInvoiceRepository $recurring_invoice_repo,
        InvoiceRepositoryInterface $invoice_repo,
        QuoteRepository $quote_repo,
        CreditRepository $credit_repo
    ) {
        parent::__construct($invoice_repo, $quote_repo, $credit_repo, 'RecurringInvoice');
        $this->recurring_invoice_repo = $recurring_invoice_repo;
    }

    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function index(SearchRequest $request)
    {
        $invoices = (new RecurringInvoiceSearch($this->recurring_invoice_repo))->filter(
            $request,
            auth()->user()->account_user()->account
        );
        return response()->json($invoices);
    }

    /**
     * @param CreateRecurringInvoiceRequest $request
     * @return mixed
     * @throws Exception
     */
    public function store(CreateRecurringInvoiceRequest $request)
    {
        $recurring_invoice = (new RecurringInvoiceRepository(new RecurringInvoice))->createInvoice(
            $request->all(),
            RecurringInvoiceFactory::create(
                Customer::where('id', $request->customer_id)->first(),
                auth()->user()->account_user()->account,
                auth()->user()
            )
        );

        return response()->json($this->transformRecurringInvoice($recurring_invoice));
    }

    /**
     * @param int $id
     * @param CreateRecurringInvoiceRequest $request
     * @return mixed
     */
    public function update(int $id, CreateRecurringInvoiceRequest $request)
    {
        $recurring_invoice = $this->recurring_invoice_repo->findInvoiceById($id);

        $recurring_invoice = $this->recurring_invoice_repo->save($request->all(), $recurring_invoice);
        return response()->json($this->transformRecurringInvoice($recurring_invoice));
    }

    /**
     * @param Request $request
     * @param RecurringInvoice $recurring_invoice
     * @param $action
     * @return array|bool|JsonResponse|string
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function action(Request $request, RecurringInvoice $recurring_invoice, $action)
    {
        return $this->performAction($request, $recurring_invoice, $action);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function archive(int $id)
    {
        $recurring_invoice = $this->recurring_invoice_repo->findInvoiceById($id);
        $recurring_invoice->archive();
        return response()->json([], 200);
    }

    public function destroy(int $id)
    {
        $recurring_invoice = RecurringInvoice::withTrashed()->where('id', '=', $id)->first();
        $this->authorize('delete', $recurring_invoice);
        $recurring_invoice->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $recurring_invoice = RecurringInvoice::withTrashed()->where('id', '=', $id)->first();
        $recurring_invoice->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function requestCancellation(Request $request)
    {
        $recurring_invoice = $this->recurring_invoice_repo->findInvoiceById($request->invoice_id);
        $client_contact = CustomerContact::find($request->contact_id);
        $recurring_invoice->user->notify(new ClientContactRequestCancellation($recurring_invoice, $client_contact));
        return response()->json(['code' => 200]);
    }
}
