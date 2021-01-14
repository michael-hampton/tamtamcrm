<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;

class InvoiceFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @param Customer|null $customer
     * @return Invoice
     */
    public static function create(
        Account $account,
        User $user,
        Customer $customer
    ): Invoice {
        $invoice = new Invoice();
        $invoice->setAccount($account);
        $invoice->setStatus(Invoice::STATUS_DRAFT);
        $invoice->setUser($user);
        $invoice->setCustomer($customer);
        $invoice->setDueDate();

        return $invoice;
    }
}
