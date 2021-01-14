<?php

namespace App\Factory;

use App\Models\Credit;
use App\Models\Quote;
use App\Models\User;

/**
 * Class CloneCreditToQuoteFactory
 * @package App\Factory
 */
class CloneCreditToQuoteFactory
{
    /**
     * @param Credit $credit
     * @param User $user
     * @return Quote|null
     */
    public static function create(Credit $credit, User $user): ?Quote
    {
        $quote = new Quote();
        $quote->fill($credit->toArray());
        $quote->number = null;
        $quote->setCustomer($credit->customer);
        $quote->setUser($user);
        $quote->setAccount($credit->account);
        $quote->setTotal($credit->total);
        $quote->setBalance($credit->balance);
        $quote->setStatus(Quote::STATUS_DRAFT);
        $quote->number = '';
        $quote->setDueDate();

        return $quote;
    }
}
