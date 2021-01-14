<?php

namespace App\Factory;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;

/**
 * Class CloneInvoiceToQuoteFactory
 * @package App\Factory
 */
class CloneInvoiceToQuoteFactory
{
    /**
     * @param Invoice $invoice
     * @param User $user
     * @return Quote|null
     */
    public static function create(Invoice $invoice, User $user): ?Quote
    {
        $quote = new Quote();
        $quote->fill($invoice->toArray());
        $quote->number = null;
        $quote->setCustomer($invoice->customer);
        $quote->setUser($invoice->user);
        $quote->setAccount($invoice->account);
        $quote->setTotal($invoice->total);
        $quote->setStatus(Quote::STATUS_DRAFT);
        $quote->setNumber();
        $quote->setDueDate();
        $quote->setBalance($invoice->total);

        return $quote;
    }
}
