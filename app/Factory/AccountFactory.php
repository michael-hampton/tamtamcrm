<?php

namespace App\Factory;


use App\Models\Account;
use App\Settings\AccountSettings;

class AccountFactory
{

    /**
     * @param int $account_id
     * @return Account
     */
    public static function create(int $account_id): Account
    {
        $account = new Account;
        // $company->name = '';
        $account->domain_id = 5;
        $account->subdomain = '';
        $account->settings = (new AccountSettings)->getAccountDefaults();

        return $account;
    }
}