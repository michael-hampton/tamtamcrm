<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\User;

class CloneInvoiceFactory
{
    /**
     * @param Invoice $invoice
     * @param User $user
     * @param Account $account
     * @return Invoice
     */
    public static function create(Invoice $invoice, User $user, Account $account)
    {
        $clone_invoice = $invoice->replicate();

        $clone_invoice->line_items = array_filter(
            $invoice->line_items,
            function ($item) {
                return $item->type_id !== Invoice::GATEWAY_FEE_TYPE;
            }
        );

        $clone_invoice->setAccount($account);
        $clone_invoice->setStatus(Invoice::STATUS_DRAFT);
        $clone_invoice->number = null;
        $clone_invoice->setNumber();
        $clone_invoice->setUser($user);
        $clone_invoice->setBalance($invoice->total);
        $clone_invoice->setDueDate();
        $clone_invoice->gateway_fee = 0;
        $clone_invoice->gateway_fee_applied = 0;
        $clone_invoice->date_to_send = null;
        $clone_invoice->late_fee_charge = 0;

        return $clone_invoice;
    }
}
