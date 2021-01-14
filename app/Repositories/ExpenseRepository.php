<?php

namespace App\Repositories;

use App\Events\Expense\ExpenseWasCreated;
use App\Events\Expense\ExpenseWasUpdated;
use App\Jobs\Expense\GenerateInvoice;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\ExpenseRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\ExpenseSearch;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ExpenseRepository
 */
class ExpenseRepository extends BaseRepository implements ExpenseRepositoryInterface
{

    /**
     * ExpenseRepository constructor.
     * @param Expense $expense
     */
    public function __construct(Expense $expense)
    {
        parent::__construct($expense);
        $this->model = $expense;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return Expense
     */
    public function findExpenseById(int $id): Expense
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new ExpenseSearch($this))->filter($search_request, $account);
    }

    public function createExpense(array $data, Expense $expense): ?Expense
    {
        $expense = $this->save($data, $expense);

        if (!empty($data['create_invoice']) && $data['create_invoice'] === true && $expense->customer->getSetting(
                'expense_auto_create_invoice'
            ) === true) {
            GenerateInvoice::dispatchNow(new InvoiceRepository(new Invoice), collect([$expense]), $data);
        }

        event(new ExpenseWasCreated($expense));

        return $expense;
    }

    /**
     * @param array $data
     * @param Expense $expense
     * @return Expense|null
     */
    public function save(array $data, Expense $expense): ?Expense
    {
        $expense->fill($data);

        if (!empty($data['create_invoice']) && $data['create_invoice'] === true && empty($expense->invoice_id)) {
            $expense->setStatus(Expense::STATUS_PENDING);
        }

        if (empty($expense->currency_id)) {
            $expense->currency_id = $expense->account->settings->currency_id;
        }

        $expense->setNumber();
        $expense->save();

        return $expense;
    }

    public function updateExpense(array $data, Expense $expense): ?Expense
    {
        $expense = $this->save($data, $expense);

        event(new ExpenseWasUpdated($expense));

        return $expense;
    }
}
