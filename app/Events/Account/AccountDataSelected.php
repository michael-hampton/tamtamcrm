<?php


namespace App\Events\Account;


use App\Components\Export\AccountDataSelection;
use App\Components\Export\ExportsAccountData;
use App\Models\Account;

class AccountDataSelected
{
    public AccountDataSelection $accountDataSelection;
    public Account $account;

    public function __construct(AccountDataSelection $accountDataSelection, Account $account)
    {
        $this->accountDataSelection = $accountDataSelection;
        $this->account = $account;
    }
}