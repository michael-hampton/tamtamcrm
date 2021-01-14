<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;

/**
 * Class CloneQuoteToInvoiceFactory
 * @package App\Factory
 */
class CloneQuoteToInvoiceFactory
{
    /**
     * @param Quote $quote
     * @param User $user
     * @param Account $account
     * @return Invoice|null
     */
    public static function create(Quote $quote, User $user, Account $account): ?Invoice
    {
        $invoice = new Invoice();
        $invoice->fill($quote->toArray());
        $invoice->number = null;
        $invoice->setAccount($account);
        $invoice->setCustomer($quote->customer);
        $invoice->setUser($user);
        $invoice->setTotal($quote->total);
        $invoice->setStatus(Invoice::STATUS_DRAFT);
        $invoice->setNumber();
        $invoice->setBalance($quote->total);
        $invoice->setDueDate();

        return $invoice;
    }
}
