<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Order;
use App\Models\Quote;
use App\Models\User;

/**
 * Class CloneQuoteToOrderFactory
 * @package App\Factory
 */
class CloneQuoteToOrderFactory
{
    /**
     * @param Quote $quote
     * @param User $user
     * @param Account $account
     * @return Order|null
     */
    public static function create(Quote $quote, User $user, Account $account): ?Order
    {
        $order = new Order;
        $order->fill($quote->toArray());
        $order->number = null;
        $order->setAccount($account);
        $order->setCustomer($quote->customer);
        $order->setUser($user);
        $order->setTotal($quote->total);
        $order->setStatus(Order::STATUS_DRAFT);
        $order->setNumber();
        $order->setDueDate();
        $order->setBalance($quote->total);
        $order->quote_id = $quote->id;

        return $order;
    }
}
