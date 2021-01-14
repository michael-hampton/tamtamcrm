<?php


namespace App\Factory;


use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\User;

class ExpenseCategoryFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @return ExpenseCategory
     */
    public static function create(Account $account, User $user)
    {
        $category = new ExpenseCategory();
        $category->account_id = $account->id;
        $category->user_id = $user->id;

        return $category;
    }
}