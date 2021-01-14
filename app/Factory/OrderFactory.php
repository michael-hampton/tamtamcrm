<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;

/**
 * Class OrderFactory
 * @package App\Factory
 */
class OrderFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @param Customer|null $customer
     * @return Order
     */
    public static function create(
        Account $account,
        User $user,
        Customer $customer
    ): Order {
        $order = new Order;
        $order->setAccount($account);
        $order->setStatus(Order::STATUS_DRAFT);
        $order->setCustomer($customer);
        $order->setUser($user);

        return $order;
    }
}
