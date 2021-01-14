<?php

namespace App\Http\Controllers;

use App\Factory\CloneRecurringQuoteFactory;
use App\Factory\CloneRecurringQuoteToQuoteFactory;
use App\Factory\RecurringQuoteFactory;
use App\Jobs\Invoice\CreateInvoicePdf;
use App\Jobs\RecurringInvoice\SendRecurring;
use App\Models\Customer;
use App\Models\RecurringQuote;
use App\Repositories\BaseRepository;
use App\Repositories\CreditRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\QuoteRepository;
use App\Repositories\RecurringQuoteRepository;
use App\Requests\RecurringQuote\CreateRecurringQuoteRequest;
use App\Requests\RecurringQuote\UpdateRecurringQuoteRequest;
use App\Requests\SearchRequest;
use App\Search\RecurringQuoteSearch;
use App\Transformations\RecurringQuoteTransformable;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionException;

/**
 * Class RecurringQuoteController
 * @package App\Http\Controllers\RecurringQuoteController
 */
class RecurringQuoteController extends BaseController
{
    use RecurringQuoteTransformable;

    /**
     * @var RecurringQuoteRepository
     */
    protected RecurringQuoteRepository $recurring_quote_repo;

    /**
     * RecurringQuoteController constructor.
     * @param RecurringQuoteRepository $recurring_quote_repo
     * @param InvoiceRepositoryInterface $invoice_repo
     * @param QuoteRepository $quote_repo
     * @param CreditRepository $credit_repo
     */
    public function __construct(
        RecurringQuoteRepository $recurring_quote_repo,
        InvoiceRepositoryInterface $invoice_repo,
        QuoteRepository $quote_repo,
        CreditRepository $credit_repo
    ) {
        parent::__construct($invoice_repo, $quote_repo, $credit_repo, 'RecurringQuote');
        $this->recurring_quote_repo = $recurring_quote_repo;
    }

    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function index(SearchRequest $request)
    {
        $invoices = (new RecurringQuoteSearch($this->recurring_quote_repo))->filter(
            $request,
            auth()->user()->account_user()->account
        );
        return response()->json($invoices);
    }

    /**
     * @param CreateRecurringQuoteRequest $request
     * @return mixed
     * @throws Exception
     */
    public function store(CreateRecurringQuoteRequest $request)
    {
        $recurring_quote = (new RecurringQuoteRepository(new RecurringQuote))->createQuote(
            $request->all(),
            RecurringQuoteFactory::create(
                Customer::where('id', $request->customer_id)->first(),
                auth()->user()->account_user()->account,
                auth()->user()
            )
        );
        return response()->json($this->transformRecurringQuote($recurring_quote));
    }

    /**
     * @param int $id
     * @param UpdateRecurringQuoteRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateRecurringQuoteRequest $request)
    {
        $recurring_quote = $this->recurring_quote_repo->findQuoteById($id);

        $recurring_quote = $this->recurring_quote_repo->save($request->all(), $recurring_quote);
        return response()->json($this->transformRecurringQuote($recurring_quote));
    }

    /**
     * @param Request $request
     * @param RecurringQuote $recurring_quote
     * @param $action
     * @return array|bool|JsonResponse|string
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function action(Request $request, RecurringQuote $recurring_quote, $action)
    {
        return $this->performAction($request, $recurring_quote, $action);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function archive(int $id)
    {
        $recurring_quote = $this->recurring_quote_repo->findQuoteById($id);
        $recurring_quote->archive();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $recurring_quote = RecurringQuote::withTrashed()->where('id', '=', $id)->first();
        $this->authorize('delete', $recurring_quote);
        $recurring_quote->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $recurring_quote = RecurringQuote::withTrashed()->where('id', '=', $id)->first();
        $recurring_quote->restoreEntity();
        return response()->json([], 200);
    }

}
