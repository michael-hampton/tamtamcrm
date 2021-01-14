<?php

namespace App\Http\Controllers;

use App\Factory\ExpenseFactory;
use App\Jobs\Expense\GenerateInvoice;
use App\Models\Expense;
use App\Models\Invoice;
use App\Repositories\ExpenseRepository;
use App\Repositories\InvoiceRepository;
use App\Requests\Expense\CreateExpenseRequest;
use App\Requests\Expense\UpdateExpenseRequest;
use App\Requests\SearchRequest;
use App\Search\ExpenseSearch;
use App\Transformations\ExpenseTransformable;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class ExpenseController
 * @package App\Http\Controllers
 */
class ExpenseController extends Controller
{
    use ExpenseTransformable;

    /**
     * @var
     */
    protected $expense_repo;

    /**
     * ExpenseController constructor.
     * @param ExpenseRepository $expense_repo
     */
    public function __construct(ExpenseRepository $expense_repo)
    {
        $this->expense_repo = $expense_repo;
    }

    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function index(SearchRequest $request)
    {
        $expenses =
            (new ExpenseSearch($this->expense_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($expenses);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $expense = $this->expense_repo->findExpenseById($id);
        return response()->json($this->transformExpense($expense));
    }

    /**
     * @param UpdateExpenseRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateExpenseRequest $request, int $id)
    {
        $expense = $this->expense_repo->findExpenseById($id);

        $expense = $this->expense_repo->updateExpense($request->all(), $expense);

        return response()->json($this->transformExpense($expense->fresh()));
    }

    /**
     * @param CreateExpenseRequest $request
     * @return JsonResponse
     */
    public function store(CreateExpenseRequest $request)
    {
        $expense = $this->expense_repo->createExpense(
            $request->all(),
            ExpenseFactory::create(auth()->user(), auth()->user()->account_user()->account)
        );

        return response()->json($this->transformExpense($expense));
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $expense = Expense::withTrashed()->where('id', '=', $id)->first();
        $this->authorize('delete', $expense);
        $expense->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $expense = Expense::withTrashed()->where('id', '=', $id)->first();
        $expense->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param $id
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function archive(int $id)
    {
        $expense = $this->expense_repo->findExpenseById($id);
        $expense->archive();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @param Expense $expense
     * @param $action
     * @return JsonResponse
     */
    public function action(Request $request, Expense $expense, $action)
    {
        if ($action === 'approve') {
            $expense->service()->approve(new ExpenseRepository(new Expense()));

            return response()->json(['message' => 'The expenses have been approved successfully!'], 200);
        }
    }

    public function bulk(Request $request)
    {
        $action = $request->action;

        $ids = $request->ids;

        $expenses = Expense::withTrashed()->whereIn('id', $ids)->get();

        if (!$expenses) {
            return response()->json(['message' => "No expense Found"]);
        }

        if ($action === 'approve') {
            foreach ($expenses as $expense) {
                $expense->service()->approve(new ExpenseRepository(new Expense()));
            }

            return response()->json(['message' => 'The expenses have been approved successfully!'], 200);
        }

        if ($action === 'generate_invoice') {
            GenerateInvoice::dispatchNow(new InvoiceRepository(new Invoice), $expenses);
            return response()->json(['message' => 'The invoice was created successfully!'], 200);
        }

        $responses = [];

        return response()->json($responses);
    }
}
