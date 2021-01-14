<?php

namespace App\Http\Controllers;

use App\Factory\QuoteFactory;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\Task;
use App\Repositories\CreditRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\QuoteRepositoryInterface;
use App\Repositories\TaskRepository;
use App\Requests\Quote\CreateQuoteRequest;
use App\Requests\Quote\UpdateOrderRequest;
use App\Requests\Quote\UpdateQuoteRequest;
use App\Requests\SearchRequest;
use App\Search\QuoteSearch;
use App\Transformations\QuoteTransformable;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionException;

/**
 * Class QuoteController
 * @package App\Http\Controllers
 */
class QuoteController extends BaseController
{

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoice_repo;

    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $quote_repo;

    /**
     * QuoteController constructor.
     * @param InvoiceRepositoryInterface $invoice_repo
     * @param QuoteRepositoryInterface $quote_repo
     * @param CreditRepository $credit_repo
     */
    public function __construct(
        InvoiceRepositoryInterface $invoice_repo,
        QuoteRepositoryInterface $quote_repo,
        CreditRepository $credit_repo
    ) {
        $this->invoice_repo = $invoice_repo;
        $this->quote_repo = $quote_repo;
        parent::__construct($invoice_repo, $quote_repo, $credit_repo, 'Quote');
    }

    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function index(SearchRequest $request)
    {
        $invoices = (new QuoteSearch($this->quote_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($invoices);
    }

    /**
     * @param int $quote_id
     * @return mixed
     */
    public function show(int $quote_id)
    {
        $invoice = $this->quote_repo->findQuoteById($quote_id);
        return response()->json($invoice);
    }

    /**
     * @param CreateQuoteRequest $request
     * @return mixed
     */
    public function store(CreateQuoteRequest $request)
    {
        $customer = Customer::find($request->input('customer_id'));
        $quote = $this->quote_repo->createQuote(
            $request->all(),
            QuoteFactory::create(auth()->user()->account_user()->account, auth()->user(), $customer)
        );

        return response()->json((new QuoteTransformable())->transformQuote($quote));
    }

    /**
     * @param UpdateQuoteRequest $request
     * @param int $id
     * @return mixed
     */
    public function update(UpdateQuoteRequest $request, int $id)
    {
        $quote = $this->quote_repo->findQuoteById($id);

        $quote = $this->quote_repo->updateQuote($request->all(), $quote);

        return response()->json((new QuoteTransformable())->transformQuote($quote));
    }

    /**
     * @param Request $request
     * @param Quote $quote
     * @param $action
     * @return JsonResponse
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function action(Request $request, Quote $quote, $action)
    {
        return $this->performAction($request, $quote, $action);
    }

    /**
     * @param int $task_id
     * @return mixed
     * @throws Exception
     */
    public function getQuoteLinesForTask(int $task_id)
    {
        $task = (new TaskRepository(new Task))->findTaskById($task_id);
        $quote = $this->quote_repo->getQuoteForTask($task);

        if (!$quote->count()) {
            return response()->json('empty');
        }

        $arrTest = [
            'lines'   => $quote->line_items,
            'invoice' => $quote
        ];

        return response()->json($arrTest);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function archive(int $id)
    {
        $quote = $this->quote_repo->findQuoteById($id);
        $quote->archive();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $quote = Quote::withTrashed()->where('id', '=', $id)->first();
        $this->authorize('delete', $quote);
        $quote->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $quote = Quote::withTrashed()->where('id', '=', $id)->first();
        $quote->restoreEntity();
        return response()->json([], 200);
    }
}
