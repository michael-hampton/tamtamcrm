<?php


namespace App\Events\Account;


use App\Components\Export\ExportsAccountData;
use App\Models\Account;

class AccountDataExportCreated
{
    public string $zipFilename;
    public Account $account;

    public function __construct(string $zipFilename, Account $account)
    {
        $this->zipFilename = $zipFilename;
        $this->account = $account;
    }
}