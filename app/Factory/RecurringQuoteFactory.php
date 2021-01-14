<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\RecurringQuote;
use App\Models\User;

class RecurringQuoteFactory
{
    public static function create(Customer $customer, Account $account, User $user): RecurringQuote
    {
        $quote = new RecurringQuote();
        $quote->account_id = $account->id;
        $quote->status_id = RecurringQuote::STATUS_DRAFT;
        $quote->user_id = $user->id;
        $quote->customer_id = $customer->id;
        $quote->frequency = 'MONTHLY';

        return $quote;
    }
}
