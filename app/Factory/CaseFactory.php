<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Cases;
use App\Models\Customer;
use App\Models\User;

class CaseFactory
{
    public static function create(
        Account $account,
        User $user,
        Customer $customer
    ): Cases {
        $case = new Cases;
        $case->setStatus(Cases::STATUS_DRAFT);
        $case->setCustomer($customer);
        $case->setAccount($account);
        $case->setUser($user);
        return $case;
    }
}
