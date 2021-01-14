<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Expense;
use App\Models\User;

class ExpenseFactory
{
    /**
     * @param User $user
     * @param Account $account
     * @return Expense
     */
    public static function create(User $user, Account $account): Expense
    {
        $expense = new Expense();
        $expense->user_id = $user->id;
        $expense->account_id = $account->id;
        $expense->is_deleted = false;

        return $expense;
    }
}
