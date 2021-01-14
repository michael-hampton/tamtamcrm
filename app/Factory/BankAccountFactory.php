<?php


namespace App\Factory;


use App\Models\Account;
use App\Models\BankAccount;
use App\Models\User;

class BankAccountFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @return BankAccount
     */
    public static function create(Account $account, User $user): BankAccount
    {
        $bank_account = new BankAccount;
        $bank_account->account_id = $account->id;
        $bank_account->user_id = $user->id;

        return $bank_account;
    }
}
