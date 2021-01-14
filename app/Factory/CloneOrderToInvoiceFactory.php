<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;

/**
 * Class CloneOrderToInvoiceFactory
 * @package App\Factory
 */
class CloneOrderToInvoiceFactory
{
    /**
     * @param Order $order
     * @param User $user
     * @param Account $account
     * @return Invoice|null
     */
    public static function create(Order $order, User $user, Account $account): ?Invoice
    {
        $invoice = new Invoice();
        $invoice->fill($order->toArray());
        $invoice->number = null;
        $invoice->setAccount($account);
        $invoice->setCustomer($order->customer);
        $invoice->setUser($user);
        $invoice->setTotal($order->total);
        $invoice->setStatus(Invoice::STATUS_DRAFT);
        $invoice->setNumber();
        $invoice->setDueDate();
        $invoice->setBalance($order->total);
        $invoice->order_id = $order->id;

        return $invoice;
    }
}
