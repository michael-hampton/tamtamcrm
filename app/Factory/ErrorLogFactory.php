<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\ErrorLog;
use App\Models\User;

class ErrorLogFactory
{
    public static function create(
        Account $account,
        User $user,
        Customer $customer
    ): ErrorLog {
        $error_log = new ErrorLog;
        $error_log->customer_id = $customer->id;
        $error_log->account_id = $account->id;
        $error_log->user_id = $user->id;
        return $error_log;
    }
}
