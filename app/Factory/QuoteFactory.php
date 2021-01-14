<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\User;

class QuoteFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @param Customer $customer
     * @return Quote
     */
    public static function create(
        Account $account,
        User $user,
        Customer $customer
    ): Quote {
        $quote = new Quote();
        $quote->setAccount($account);
        $quote->setStatus(Quote::STATUS_DRAFT);
        $quote->setUser($user);
        $quote->setCustomer($customer);

        return $quote;
    }
}
