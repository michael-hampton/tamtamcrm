<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

/**
 * Class CloneOrderToinvoiceFactory
 * @package App\Factory
 */
class CloneTaskToInvoiceFactory
{
    /**
     * @param Task $task
     * @param User $user
     * @param Account $account
     * @return Invoice|null
     */
    public static function create(Task $task, User $user, Account $account): ?Invoice
    {
        $invoice = new Invoice();
        $invoice->number = null;
        $invoice->setAccount($account);
        $invoice->setCustomer($task->customer);
        $invoice->setUser($user);
        //$invoice->setTotal($task->total);
        $invoice->setStatus(invoice::STATUS_DRAFT);
        $invoice->setNumber();
        $invoice->setDueDate();
        //$invoice->setBalance($order->total);

        //$invoice->order_id = $order->id;
        //$invoice->task_id = $order->task_id;
        $invoice->discount_total = 0;
        $invoice->tax_total = 0;
        $invoice->is_amount_discount = false;
        $invoice->footer = '';
        $invoice->tax_rate = 0;
        $invoice->public_notes = $task->public_notes;
        $invoice->private_notes = $task->private_notes;
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
