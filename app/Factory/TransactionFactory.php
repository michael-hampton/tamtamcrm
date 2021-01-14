<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;

class TransactionFactory
{
    public static function create(Account $account, User $user): Transaction
    {
        $transaction = new Transaction;
        $transaction->account_id = $account->id;
        $transaction->user_id = $user->id;

        return $transaction;
    }
}
