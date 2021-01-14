<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;

/**
 * Class CloneOrderToinvoiceFactory
 * @package App\Factory
 */
class CloneExpenseToInvoiceFactory
{
    /**
     * @param Expense $expense
     * @param User $user
     * @param Account $account
     * @return Invoice|null
     */
    public static function create(Expense $expense, User $user, Account $account): ?Invoice
    {
        $invoice = new Invoice();
        $invoice->setAccount($account);
        $invoice->setCustomer($expense->customer);
        $invoice->setUser($user);
        $invoice->setTotal($expense->amount);
        $invoice->setStatus(invoice::STATUS_DRAFT);
        $invoice->setNumber();
        $invoice->setDueDate();
        $invoice->setBalance($expense->amount);

        //$invoice->order_id = $order->id;
        //$invoice->expense_id = $order->expense_id;
        $invoice->discount_total = 0;
        $invoice->tax_total = 0;
        $invoice->is_amount_discount = false;
        $invoice->footer = '';
        $invoice->tax_rate = 0;
        $invoice->public_notes = $expense->public_notes;
        $invoice->private_notes = $expense->private_notes;
        //$invoice->terms = $order->terms;
        $invoice->sub_total = 0;
        $invoice->partial = 0;
        $invoice->partial_due_date = null;
        $invoice->last_viewed = null;
        $invoice->date = Carbon::now();
        $invoice->partial_due_date = null;
        $invoice->line_items = [];
        $invoice->transaction_fee = 0;
        $invoice->shipping_cost = 0;
        return $invoice;
    }
}
