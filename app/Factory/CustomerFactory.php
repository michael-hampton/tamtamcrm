<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use App\Settings\CustomerSettings;

class CustomerFactory
{
    public static function create(Account $account, User $user): Customer
    {
        $client = new Customer;
        $client->currency_id = 2;
        $client->account_id = $account->id;
        $client->user_id = $user->id;
        $client->settings = (new CustomerSettings)->getCustomerDefaults();

        return $client;
    }
}
