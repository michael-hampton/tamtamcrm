<?php

namespace App\Repositories\Interfaces;

use App\Models\Account;
use App\Models\Expense;
use App\Requests\SearchRequest;

interface ExpenseRepositoryInterface
{
    /**
     *
     * @param SearchRequest $search_request
     * @param Account $account
     */
    public function getAll(SearchRequest $search_request, Account $account);

    /**
     * @param int $id
     * @return Expense
     */
    public function findExpenseById(int $id): Expense;

    /**
     * @param array $data
     * @param Expense $expense
     * @return Expense|null
     */
    public function save(array $data, Expense $expense): ?Expense;

    /**
     * @param array $data
     * @param Expense $expense
     * @return Expense|null
     */
    public function createExpense(array $data, Expense $expense): ?Expense;

    /**
     * @param array $data
     * @param Expense $expense
     * @return Expense|null
     */
    public function updateExpense(array $data, Expense $expense): ?Expense;

}
