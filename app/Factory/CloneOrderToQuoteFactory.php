<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Order;
use App\Models\Quote;
use App\Models\User;

/**
 * Class CloneOrderToQuoteFactory
 * @package App\Factory
 */
class CloneOrderToQuoteFactory
{
    /**
     * @param Order $order
     * @param User $user
     * @param Account $account
     * @return Quote|null
     */
    public static function create(Order $order, User $user, Account $account): ?Quote
    {
        $quote = new Quote;
        $quote->fill($order->toArray());
        $quote->number = null;
        $quote->setAccount($account);
        $quote->setCustomer($order->customer);
        $quote->setUser($user);
        $quote->setTotal($order->total);
        $quote->setStatus(Quote::STATUS_DRAFT);
        $quote->setNumber();
        $quote->setDueDate();
        $quote->setBalance($order->total);
        $quote->order_id = $order->id;

        return $quote;
    }
}
